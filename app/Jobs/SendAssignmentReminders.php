<?php

namespace App\Jobs;

use App\Mail\AssignmentReminder;
use App\Models\Assignment;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendAssignmentReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Log::info('SendAssignmentReminders job started at: ' . now());

        $tomorrow = Carbon::tomorrow()->startOfDay();
        Log::info('Checking assignments for deadline: ' . $tomorrow);

        $assignments = Assignment::whereDate('deadline', $tomorrow)->get();
        Log::info('Found assignments: ' . $assignments->count());

        foreach ($assignments as $assignment) {
            Log::info('Processing assignment: ' . $assignment->title . ' (ID: ' . $assignment->id . ')');
            $students = Student::whereHas('classroom.schedules', function ($query) use ($assignment) {
                $query->where('id', $assignment->schedule_id);
            })->get();
            Log::info('Found students for assignment: ' . $students->count());

            foreach ($students as $student) {
                if ($student->user && $student->user->email) {
                    Log::info('Sending email to: ' . $student->user->email);
                    Mail::to($student->user->email)->send(new AssignmentReminder($assignment));
                } else {
                    Log::info('No email found for student: ' . $student->name);
                }
            }
        }
        Log::info('SendAssignmentReminders job completed at: ' . now());
    }
}