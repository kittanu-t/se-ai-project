<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Symfony\Component\Process\Process;

class ReviewController extends Controller {
    public function store(Request $request) {
        // 1. บันทึกข้อมูลรีวิวลง MySQL
        $review = new Review();
        $review->comment = $request->comment;
        $review->save();

        // 2. สั่ง Python ให้ทำงาน (เรียกไฟล์ .py ที่เราสร้างไว้)
        // ถ้าใช้ Windows อาจต้องใช้ 'python' หรือ 'C:\Path\To\python.exe'
        $process = new Process(['python', base_path('python/sentiment_ai.py'), $review->comment]);
        $process->run();

        // 3. รับผลลัพธ์จาก Python มาอัปเดต Database
        if ($process->isSuccessful()) {
            $output = json_decode($process->getOutput(), true);
            if (isset($output['status']) && $output['status'] == 'success') {
                $review->update([
                    'sentiment_label' => $output['label'],
                    'sentiment_score' => $output['score']
                ]);
            }
        }

        return back()->with('msg', 'วิเคราะห์เสร็จสิ้น: ' . $review->sentiment_label);
    }
}