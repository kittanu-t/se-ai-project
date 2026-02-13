<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Sports Field Booking')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF สำหรับ fetch/form --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ป้องกัน Alpine flash --}}
    <style>[x-cloak]{ display:none !important; }</style>

    {{-- (คงไว้ตามเดิม) --}}
    @vite(['resources/css/app.css','resources/js/app.js'])

    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('styles')
    <!-- FullCalendar v6 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.19/main.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.19/main.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.19/main.min.css" rel="stylesheet">

    <style>
        .bg-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url('{{ asset('images/background.jpg') }}') center center / cover no-repeat fixed;
            opacity: 0.25;        /* ความโปร่ง (0.0 - 1.0) */
            z-index: -1;          /* ให้ภาพอยู่ใต้เนื้อหา */
        }
    </style>

</head>
<body class="antialiased">
<div class="bg-overlay"></div>
<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top navbar-light shadow-sm">
    <div class="container">

        <!-- Brand -->
        <a id="brandLink"
           class="navbar-brand fw-semibold d-flex align-items-center brand-disabled"
           href="{{ route('home') }}" aria-disabled="true" tabindex="-1" style="color:var(--txt-main)">
            <img src="{{ asset('images\logo_png.png') }}" alt="Khon Kaen Sport Hub Logo" style="height: 30px; width: auto;" class="me-2">
            <span>Khon Kaen Sport Hub</span>
        </a>

        <div id="topNav" class="collapse navbar-collapse">
            <!-- เมนูซ้าย -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                {{-- เมนูสำหรับ Guest --}}
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('fields.index') }}">Fields</a></li>
                @endguest

                {{-- เมนูสำหรับผู้ใช้ที่ล็อกอินแล้ว --}}
                @auth
                    @if(auth()->user()->role === 'user')
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('bookings.index') }}">My Bookings</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('fields.index') }}">Fields</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('user.announcements.index') }}">Announcements</a></li>
                    @endif

                    @if(auth()->user()->role === 'staff')
                        <li class="nav-item"><a class="nav-link" href="{{ route('staff.bookings.index') }}">Requests</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('staff.fields.index') }}">My Fields</a></li>
                    @endif

                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.index') }}">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.fields.index') }}">Fields</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.bookings.index') }}">Bookings</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.announcements.index') }}">Announcements</a></li>
                    @endif
                @endauth
            </ul>

            <!-- เมนูขวา -->
            <ul class="navbar-nav ms-auto align-items-lg-center">
                @auth
                    <li class="nav-item me-2">
                        <span class="nav-link disabled text-secondary">
                            Hi, {{ auth()->user()->name }}
                        </span>
                    </li>

                    {{-- 🔔 กระดิ่งแจ้งเตือน (Alpine) --}}
                    <li class="nav-item dropdown me-2" x-data="notificationBell()" x-cloak x-init="init()">
                        <button id="bellButton" class="btn bell-btn rounded-pill px-3 py-1"
                                data-bs-toggle="dropdown" aria-expanded="false" @click="open = !open">
                            <span class="me-1">🔔</span>
                            <span class="bell-badge" x-show="unread > 0" x-text="unread"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-2 notif-panel shadow-soft"
                             x-show="open" @click.outside="open = false">
                            <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                                <strong>Notifications</strong>
                                <form method="POST" action="{{ route('notifications.readAll') }}">
                                    @csrf
                                    <button class="btn btn-link btn-sm p-0 text-decoration-underline" type="submit">Mark all read</button>
                                </form>
                            </div>

                            <template x-if="items.length === 0">
                                <div class="text-secondary small px-1">No notifications</div>
                            </template>

                            <div class="list-group list-group-flush">
                                <template x-for="item in items" :key="item.id">
                                    <div class="list-group-item px-1">
                                        <div class="small" x-text="renderTitle(item)"></div>
                                        <div class="text-secondary" style="font-size:12px" x-text="new Date(item.created_at).toLocaleString()"></div>
                                        <form method="POST" :action="'/notifications/'+item.id+'/read'" class="mt-1">
                                            @csrf
                                            <button class="btn btn-link btn-sm p-0 text-decoration-underline" x-show="!item.read_at" type="submit">Mark read</button>
                                        </form>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </li>

                    {{-- Account (เฉพาะ role=user) ชิดขวา --}}
                    @if(auth()->user()->role === 'user')
                        <li class="nav-item me-2">
                            <a href="{{ route('account.show') }}" class="btn btn-light border">Account</a>
                        </li>
                    @endif

                    {{-- ปุ่ม Logout = สีแดงตามธีม --}}
                    <li class="nav-item">
                        <form class="d-inline" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-logout">Logout</button>
                        </form>
                    </li>
                @endauth

                @guest
                    <li class="nav-item me-2">
                        <a class="btn" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-secondary" href="{{ route('register') }}">Register</a>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

{{-- Flash message --}}
@if(session('status'))
    <div class="container mt-3">
        <div class="alert alert-success shadow-soft border-0">
            {{ session('status') }}
        </div>
    </div>
@endif
@if ($errors->any())
    <div class="container mt-3">
        <div class="alert alert-danger alert-border shadow-soft border-0">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<main class="container py-4">
    @yield('content')
</main>

{{-- Alpine helper สำหรับกระดิ่ง --}}
<script>
window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

function notificationBell() {
    return {
        open: false,
        unread: 0,
        items: [],
        fetchFeed() {
            fetch(`{{ route('notifications.feed') }}`, { credentials: 'same-origin' })
                .then(r => r.json())
                .then(data => {
                    this.unread = data.unread || 0;
                    this.items = data.items || [];
                })
                .catch(() => {});
        },
        renderTitle(item) {
            if (item.type === 'booking.status.changed') {
                const s = item.data?.status ?? '';
                if (s === 'approved') return 'การจองของคุณได้รับการอนุมัติแล้ว';
                if (s === 'rejected') return 'คำขอจองของคุณถูกปฏิเสธ';
            }
            return item.data?.message ?? 'Notification';
        },
        init() {
            this.fetchFeed();
            setInterval(() => this.fetchFeed(), 60000);
        }
    }
}
</script>

{{-- Alpine fallback (ถ้าใน app.js ยังไม่ได้ start Alpine) --}}
<script>
if (typeof window.Alpine === 'undefined') {
    var s = document.createElement('script');
    s.src = 'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js';
    s.defer = true;
    document.head.appendChild(s);
}
</script>

<!-- JS ฝั่งหน้า: 1) ปิดคลิก Brand เมื่อเป็น Staff/Admin  2) ไฮไลท์เมนูปัจจุบัน -->
<script>
(function(){
    // 1) ถ้าพบเมนูของ staff หรือ admin ใน navbar ให้ "ปิดคลิก" แบรนด์
    const hasStaff = document.querySelector('a.nav-link[href*="/staff"]');
    const hasAdmin = document.querySelector('a.nav-link[href*="/admin"]');
    const brand = document.getElementById('brandLink');
    if ((hasStaff || hasAdmin) && brand){
        brand.classList.add('brand-disabled');
        brand.setAttribute('aria-disabled','true');
        brand.addEventListener('click', function(e){ e.preventDefault(); }, {passive:false});
        // สื่อสารสถานะให้ผู้ใช้เล็กน้อย
        brand.title = 'คุณกำลังอยู่ในโหมด Staff/Admin';
    }

    // 2) ไฮไลท์ลิงก์ปัจจุบัน (แบบไม่แตะ Blade logic)
    const current = window.location.pathname.replace(/\/+$/,''); // ตัด / ท้าย
    document.querySelectorAll('.navbar .nav-link').forEach(a=>{
        try{
            const aPath = new URL(a.href, window.location.origin).pathname.replace(/\/+$/,'');
            // ตรงเป๊ะ หรือเป็นหน้าหลักของหมวด (เริ่มต้นด้วย path และยาวกว่า)
            if (aPath && (aPath === current || (current.startsWith(aPath) && aPath !== '/'))){
                a.classList.add('active');
                a.setAttribute('aria-current','page');
            }
        }catch(_e){}
    });
})();
</script>
</body>
</html>
