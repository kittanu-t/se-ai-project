<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use App\Models\User;
use App\Models\SportsField;
use App\Models\FieldUnit;
use App\Models\Booking;
use App\Models\FieldClosure;
use App\Models\Announcement;
use App\Models\BookingLog;

class InitialTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // === ใช้โซนเวลาไทยให้เดโมตรงกับหน้าปฏิทิน ===
        $tz   = 'Asia/Bangkok';
        $now  = Carbon::now($tz);
        $today = $now->toDateString();
        $tomorrow = $now->copy()->addDay()->toDateString();
        $yesterday = $now->copy()->subDay()->toDateString();
        $lastWeek = $now->copy()->subWeek()->toDateString();
        $nextWeek = $now->copy()->addWeek()->toDateString();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        // ล้างข้อมูลหลักที่ใช้ทดสอบ
        DB::table('booking_logs')->truncate();
        DB::table('bookings')->truncate();
        DB::table('field_closures')->truncate();
        DB::table('field_units')->truncate();
        DB::table('sports_fields')->truncate();
        DB::table('announcements')->truncate();
        DB::table('notifications')->truncate();
        // ถ้าต้องการล้าง users ด้วย ให้ uncomment
        // DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ===== USERS =====
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'phone'    => '080-000-0000',
                'active'   => 1,
            ]
        );

        $staff1 = User::firstOrCreate(
            ['email' => 'staff1@example.com'],
            [
                'name'     => 'Staff One',
                'password' => Hash::make('password'),
                'role'     => 'staff',
                'phone'    => '080-111-1111',
                'active'   => 1,
            ]
        );

        $staff2 = User::firstOrCreate(
            ['email' => 'staff2@example.com'],
            [
                'name'     => 'Staff Two',
                'password' => Hash::make('password'),
                'role'     => 'staff',
                'phone'    => '080-222-2222',
                'active'   => 1,
            ]
        );

        $user1 = User::firstOrCreate(
            ['email' => 'user1@example.com'],
            [
                'name'     => 'Alice User',
                'password' => Hash::make('password'),
                'role'     => 'user',
                'phone'    => '081-111-1111',
                'active'   => 1,
            ]
        );
        $user2 = User::firstOrCreate(
            ['email' => 'user2@example.com'],
            [
                'name'     => 'Bob User',
                'password' => Hash::make('password'),
                'role'     => 'user',
                'phone'    => '082-222-2222',
                'active'   => 1,
            ]
        );

        // ===== SPORTS FIELDS & UNITS =====
        $fieldsData = [
            [
                'name' => 'Main Court A',
                'sport_type' => 'Badminton',
                'location' => 'Building A',
                'capacity' => 20,
                'status' => 'available',
                'owner_id' => $staff1->id,
                'min_duration_minutes' => 60,
                'max_duration_minutes' => 180,
                'lead_time_hours' => 1,
                'units' => 4, // Court 1..4
            ],
            [
                'name' => 'Stadium B',
                'sport_type' => 'Futsal',
                'location' => 'Building B',
                'capacity' => 10,
                'status' => 'available',
                'owner_id' => $staff2->id,
                'min_duration_minutes' => 60,
                'max_duration_minutes' => 180,
                'lead_time_hours' => 2,
                'units' => 2, // Pitch 1..2
            ],
            [
                'name' => 'Tennis Court C',
                'sport_type' => 'Tennis',
                'location' => 'Outdoor C',
                'capacity' => 6,
                'status' => 'available',
                'owner_id' => $staff2->id,
                'min_duration_minutes' => 60,
                'max_duration_minutes' => 120,
                'lead_time_hours' => 1,
                'units' => 3, // Court 1..3
            ],
        ];

        $fields = [];
        foreach ($fieldsData as $fd) {
            $field = SportsField::create([
                'name' => $fd['name'],
                'sport_type' => $fd['sport_type'],
                'location' => $fd['location'],
                'capacity' => $fd['capacity'],
                'status' => $fd['status'],
                'owner_id' => $fd['owner_id'],
                'min_duration_minutes' => $fd['min_duration_minutes'],
                'max_duration_minutes' => $fd['max_duration_minutes'],
                'lead_time_hours' => $fd['lead_time_hours'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $fields[] = $field;

            for ($i=1; $i <= $fd['units']; $i++) {
                FieldUnit::create([
                    'sports_field_id' => $field->id,
                    'name'   => in_array($fd['sport_type'], ['Badminton','Tennis']) ? "Court {$i}" : "Pitch {$i}",
                    'index'  => $i,
                    'status' => 'available',
                    'capacity' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Reload units relationship
        $fields = SportsField::with('units')->get();

        // ===== BOOKINGS helper =====
        $makeBooking = function(User $u, SportsField $f, FieldUnit $unit, string $date, string $start, string $end, string $status, ?User $approver=null, ?string $purpose=null) use ($now) {
            $approvedAt = in_array($status, ['approved','rejected','cancelled','completed']) ? $now : null;

            $b = Booking::create([
                'user_id' => $u->id,
                'sports_field_id' => $f->id,
                'field_unit_id'   => $unit->id,
                'date'       => $date,
                'start_time' => $start,
                'end_time'   => $end,
                'status'     => $status,
                'purpose'    => $purpose ?? 'Demo booking',
                'contact_phone' => $u->phone,
                'approved_by' => $approvedAt ? ($approver?->id ?? null) : null,
                'approved_at' => $approvedAt,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Logs
            BookingLog::create([
                'booking_id' => $b->id,
                'action'     => 'created',
                'by_user_id' => $u->id,
                'note'       => null,
                'created_at' => $now,
            ]);
            if ($status !== 'pending') {
                BookingLog::create([
                    'booking_id' => $b->id,
                    'action'     => $status,
                    'by_user_id' => $approver?->id ?? $u->id,
                    'note'       => $status === 'rejected' ? 'Demo reject' : ($status === 'cancelled' ? 'Demo cancel' : null),
                    'created_at' => $now,
                ]);
            }

            // Notifications ให้ผู้จอง
            DB::table('notifications')->insert([
                'user_id' => $u->id,
                'type'    => 'booking.status.changed',
                'data'    => json_encode([
                    'booking_id' => $b->id,
                    'status'     => $status,
                    'message'    => "สถานะการจองของคุณ: {$status}",
                    'field'      => $f->name,
                    'unit'       => $unit->name,
                    'date'       => $date,
                    'time'       => "{$start}-{$end}",
                ], JSON_UNESCAPED_UNICODE),
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return $b;
        };

        // เลือก unit สำหรับทำเดโม
        $fieldA = $fields->firstWhere('name','Main Court A');
        $fieldB = $fields->firstWhere('name','Stadium B');
        $fieldC = $fields->firstWhere('name','Tennis Court C');

        $unitA1 = $fieldA->units->firstWhere('index',1);
        $unitA2 = $fieldA->units->firstWhere('index',2);
        $unitB1 = $fieldB->units->firstWhere('index',1);
        $unitC1 = $fieldC->units->firstWhere('index',1);
        $unitC2 = $fieldC->units->firstWhere('index',2);

        // ===== BOOKINGS (ครอบคลุมหลายสถานะและช่วงเวลา) =====

        // 1) Past completed (เมื่อวาน) ให้เห็นในประวัติ
        $makeBooking($user1, $fieldA, $unitA1, $yesterday, '09:00:00','10:30:00','completed', $staff1, 'Friendly match');

        // 2) Today - ongoing (กำลังเล่นตอนนี้)  ->  ใช้ now() อยู่ระหว่างเวลา
        $startOngoing = $now->copy()->subMinutes(15)->format('H:i:00'); // เริ่มก่อน 15 นาที
        $endOngoing   = $now->copy()->addMinutes(45)->format('H:i:00');  // จบอีก 45 นาที
        $makeBooking($user2, $fieldA, $unitA1, $today, $startOngoing, $endOngoing, 'approved', $staff1, 'Practice (ongoing)');

        // 3) Today - back-to-back กับ ongoing เพื่อทดสอบ "ชนขอบเวลา" (ไม่ทับซ้อน)
        $edgeStart = Carbon::parse($endOngoing, $tz)->format('H:i:00');
        $edgeEnd   = Carbon::parse($endOngoing, $tz)->addHour()->format('H:i:00');
        $makeBooking($user1, $fieldA, $unitA1, $today, $edgeStart, $edgeEnd, 'approved', $staff1, 'Back-to-back edge');

        // 4) Today - คนละคอร์ต (pending)
        $makeBooking($user2, $fieldA, $unitA2, $today, '18:00:00','19:00:00','pending', null, 'Evening play');

        // 5) Tomorrow - rejected
        $makeBooking($user2, $fieldB, $unitB1, $tomorrow, '14:00:00','15:00:00','rejected', $staff2, 'Team training');

        // 6) Tomorrow - cancelled
        $makeBooking($user1, $fieldB, $unitB1, $tomorrow, '16:00:00','17:00:00','cancelled', $staff2, 'Friendly futsal');

        // 7) Next week - approved (เทนนิส)
        $makeBooking($user2, $fieldC, $unitC1, $nextWeek, '08:00:00','09:00:00','approved', $staff2, 'Morning tennis');

        // 8) Today - เคส "เกือบชนขอบ" คนละยูนิต เทนนิส (ดู pattern ปกติ)
        $makeBooking($user1, $fieldC, $unitC2, $today, '09:00:00','10:00:00','approved', $staff2, 'Tennis single');

        // ===== FIELD CLOSURES (ครอบคลุมทั้งสนาม/เฉพาะคอร์ต/อดีต/อนาคต) =====

        // A) ปิดทั้งสนาม A วันนี้ช่วงบ่าย (ทับช่วงว่างระหว่างบุกกิ้ง)
        FieldClosure::create([
            'sports_field_id' => $fieldA->id,
            'field_unit_id'   => null, // ทั้งสนาม
            'start_datetime'  => Carbon::parse("$today 13:00:00", $tz),
            'end_datetime'    => Carbon::parse("$today 15:00:00", $tz),
            'reason'          => 'Cleaning (all courts)',
            'created_by'      => $staff1->id,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        // B) ปิดเฉพาะ Court 2 ของสนาม A พรุ่งนี้ทั้งวัน
        FieldClosure::create([
            'sports_field_id' => $fieldA->id,
            'field_unit_id'   => $unitA2->id,
            'start_datetime'  => Carbon::parse("$tomorrow 08:00:00", $tz),
            'end_datetime'    => Carbon::parse("$tomorrow 22:00:00", $tz),
            'reason'          => 'Maintenance Court 2',
            'created_by'      => $staff1->id,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        // C) ปิดสนาม C (เทนนิส) แบบหลายวัน (เดโม multi-day)
        FieldClosure::create([
            'sports_field_id' => $fieldC->id,
            'field_unit_id'   => null,
            'start_datetime'  => Carbon::parse($now->copy()->addDays(2)->format('Y-m-d').' 07:00:00', $tz),
            'end_datetime'    => Carbon::parse($now->copy()->addDays(3)->format('Y-m-d').' 20:00:00', $tz),
            'reason'          => 'Annual resurfacing',
            'created_by'      => $staff2->id,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        // D) ปิดที่เพิ่งผ่านมาเมื่อวาน (เพื่อให้หน้า “ประวัติการปิด” มีของ)
        FieldClosure::create([
            'sports_field_id' => $fieldB->id,
            'field_unit_id'   => null,
            'start_datetime'  => Carbon::parse("$yesterday 12:00:00", $tz),
            'end_datetime'    => Carbon::parse("$yesterday 13:00:00", $tz),
            'reason'          => 'Emergency cleaning',
            'created_by'      => $staff2->id,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        // ===== ANNOUNCEMENTS (หลาย audience + เวลาปัจจุบัน) =====
        Announcement::create([
            'title'        => 'ยินดีต้อนรับสู่ระบบจองสนาม',
            'content'      => 'ประกาศทดสอบสำหรับผู้ใช้ทุกท่าน — ลองดูปฏิทินวันนี้จะเห็นรายการกำลังใช้งานอยู่',
            'audience'     => 'all',
            'created_by'   => $admin->id,
            'published_at' => $now->copy()->subMinutes(30), // โผล่บนสุดแบบสดใหม่
        ]);

        Announcement::create([
            'title'        => "สนาม {$fieldA->name} ปิดบางช่วงวันนี้",
            'content'      => 'ทำความสะอาดช่วง 13:00-15:00 โปรดตรวจสอบก่อนทำการจอง',
            'audience'     => 'users',
            'created_by'   => $staff1->id,
            'published_at' => $now,
        ]);

        Announcement::create([
            'title'        => "แจ้งเตือนเจ้าหน้าที่: ปรับตารางดูแล {$fieldC->name}",
            'content'      => 'เตรียมงาน resurfacing ในอีก 2-3 วันข้างหน้า',
            'audience'     => 'staff',
            'created_by'   => $staff2->id,
            'published_at' => $now->copy()->subHours(1),
        ]);

        Announcement::create([
            'title'        => 'สรุปรายสัปดาห์',
            'content'      => 'สถิติการใช้งานสัปดาห์ก่อนมีการจองสูงสุดวันศุกร์',
            'audience' => 'staff',
            'created_by'   => $admin->id,
            'published_at' => $now->copy()->subDay(),
        ]);
    }
}