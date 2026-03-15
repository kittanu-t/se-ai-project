<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class InitialTestDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // ================= USERS =================
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '080-000-0000',
                'active' => 1,
                'created_at' => '2026-02-21 02:19:46',
                'updated_at' => '2026-02-21 02:19:46',
            ],
            [
                'id' => 2,
                'name' => 'Staff One',
                'email' => 'staff1@example.com',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'phone' => '080-111-1111',
                'active' => 1,
                'created_at' => '2026-02-21 02:19:47',
                'updated_at' => '2026-02-21 02:19:47',
            ],
            [
                'id' => 3,
                'name' => 'Staff Two',
                'email' => 'staff2@example.com',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'phone' => '080-222-2222',
                'active' => 1,
                'created_at' => '2026-02-21 02:19:47',
                'updated_at' => '2026-02-21 02:19:47',
            ],
            [
                'id' => 4,
                'name' => 'Alice User',
                'email' => 'user1@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '081-111-1111',
                'active' => 1,
                'created_at' => '2026-02-21 02:19:47',
                'updated_at' => '2026-02-21 02:19:47',
            ],
            [
                'id' => 5,
                'name' => 'Bob User',
                'email' => 'user2@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '082-222-2222',
                'active' => 1,
                'created_at' => '2026-02-21 02:19:48',
                'updated_at' => '2026-02-21 02:19:48',
            ],
        ]);

        // ================= SPORTS FIELDS =================
        DB::table('sports_fields')->insert([
            [
                'id' => 1,
                'name' => 'Main Court A',
                'sport_type' => 'Badminton',
                'location' => 'Building A',
                'capacity' => 20,
                'status' => 'available',
                'owner_id' => 2,
                'min_duration_minutes' => 60,
                'max_duration_minutes' => 180,
                'lead_time_hours' => 1,
                'created_at' => '2026-02-21 09:19:44',
                'updated_at' => '2026-02-21 09:19:44',
            ],
            [
                'id' => 2,
                'name' => 'Stadium B',
                'sport_type' => 'Futsal',
                'location' => 'Building B',
                'capacity' => 10,
                'status' => 'available',
                'owner_id' => 3,
                'min_duration_minutes' => 60,
                'max_duration_minutes' => 180,
                'lead_time_hours' => 2,
                'created_at' => '2026-02-21 09:19:44',
                'updated_at' => '2026-02-21 09:19:44',
            ],
            [
                'id' => 3,
                'name' => 'Tennis Court C',
                'sport_type' => 'Tennis',
                'location' => 'Outdoor C',
                'capacity' => 6,
                'status' => 'available',
                'owner_id' => 3,
                'min_duration_minutes' => 60,
                'max_duration_minutes' => 120,
                'lead_time_hours' => 1,
                'created_at' => '2026-02-21 09:19:44',
                'updated_at' => '2026-02-21 09:19:44',
            ],
        ]);

        // ================= FIELD UNITS =================
        DB::table('field_units')->insert([
            ['id'=>1,'sports_field_id'=>1,'name'=>'Court 1','index'=>1,'status'=>'available','capacity'=>1],
            ['id'=>2,'sports_field_id'=>1,'name'=>'Court 2','index'=>2,'status'=>'available','capacity'=>1],
            ['id'=>3,'sports_field_id'=>1,'name'=>'Court 3','index'=>3,'status'=>'available','capacity'=>1],
            ['id'=>4,'sports_field_id'=>1,'name'=>'Court 4','index'=>4,'status'=>'available','capacity'=>1],
            ['id'=>5,'sports_field_id'=>2,'name'=>'Pitch 1','index'=>1,'status'=>'available','capacity'=>1],
            ['id'=>6,'sports_field_id'=>2,'name'=>'Pitch 2','index'=>2,'status'=>'available','capacity'=>1],
            ['id'=>7,'sports_field_id'=>3,'name'=>'Court 1','index'=>1,'status'=>'available','capacity'=>1],
            ['id'=>8,'sports_field_id'=>3,'name'=>'Court 2','index'=>2,'status'=>'available','capacity'=>1],
            ['id'=>9,'sports_field_id'=>3,'name'=>'Court 3','index'=>3,'status'=>'available','capacity'=>1],
        ]);

        // ================= ANNOUNCEMENTS =================
        DB::table('announcements')->insert([
            [
                'id'=>1,
                'title'=>'ยินดีต้อนรับสู่ระบบจองสนาม',
                'content'=>'ประกาศทดสอบสำหรับผู้ใช้ทุกท่าน — ลองดูปฏิทินวันนี้จะเห็นรายการกำลังใช้งานอยู่',
                'audience'=>'all',
                'created_by'=>1,
                'published_at'=>'2026-02-21 15:49:44',
                'created_at'=>'2026-02-21 02:19:50',
                'updated_at'=>'2026-02-21 02:19:50',
            ],
            [
                'id'=>2,
                'title'=>'สนาม Main Court A ปิดบางช่วงวันนี้',
                'content'=>'ทำความสะอาดช่วง 13:00-15:00 โปรดตรวจสอบก่อนทำการจอง',
                'audience'=>'users',
                'created_by'=>2,
                'published_at'=>'2026-02-21 16:19:44',
                'created_at'=>'2026-02-21 02:19:50',
                'updated_at'=>'2026-02-21 02:19:50',
            ],
        ]);

        // ================= BOOKINGS =================
        DB::table('bookings')->insert([
            [
                'id'=>1,
                'user_id'=>4,
                'sports_field_id'=>1,
                'field_unit_id'=>1,
                'date'=>'2026-02-20',
                'start_time'=>'09:00:00',
                'end_time'=>'10:30:00',
                'status'=>'completed',
                'purpose'=>'Friendly match',
                'contact_phone'=>'081-111-1111',
                'approved_by'=>2,
                'approved_at'=>'2026-02-21 16:19:44',
                'created_at'=>'2026-02-21 09:19:44',
                'updated_at'=>'2026-02-21 09:19:44',
            ],
        ]);

        // ================= REVIEWS =================
        DB::table('reviews')->insert([
            [
                'id'=>1,
                'booking_id'=>1,
                'user_id'=>4,
                'sports_field_id'=>1,
                'rating'=>5,
                'comment'=>'สนามมีหลุดบ่ ?',
                'sentiment'=>'neutral',
                'confidence_score'=>0.0000,
                'created_at'=>'2026-02-21 02:20:59',
                'updated_at'=>'2026-02-21 02:20:59',
            ],

            [
                'id'=>2,
                'booking_id'=>1,
                'user_id'=>5,
                'sports_field_id'=>1,
                'rating'=>5,
                'comment'=>'สนามสะอาดมาก พื้นดี ไฟสว่าง เล่นสบายสุด ๆ',
                'sentiment'=>'positive',
                'confidence_score'=>0.9821,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>3,
                'booking_id'=>1,
                'user_id'=>4,
                'sports_field_id'=>2,
                'rating'=>4,
                'comment'=>'จองง่าย ระบบไม่ซับซ้อน เจ้าหน้าที่บริการดี',
                'sentiment'=>'positive',
                'confidence_score'=>0.9453,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],

            [
                'id'=>4,
                'booking_id'=>1,
                'user_id'=>5,
                'sports_field_id'=>2,
                'rating'=>2,
                'comment'=>'พื้นสนามลื่นไปหน่อย เล่นแล้วไม่มั่นใจ เสี่ยงบาดเจ็บ',
                'sentiment'=>'negative',
                'confidence_score'=>0.9714,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>5,
                'booking_id'=>1,
                'user_id'=>4,
                'sports_field_id'=>3,
                'rating'=>1,
                'comment'=>'ไฟส่องสว่างไม่พอ ตอนกลางคืนมองลูกแทบไม่เห็น',
                'sentiment'=>'negative',
                'confidence_score'=>0.9932,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}