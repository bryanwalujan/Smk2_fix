<?php
// app/Services/TaskNotificationService.php
namespace App\Services;

use App\Mail\TaskReminderMail;
use App\Models\Assignment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TaskNotificationService
{
    public function sendReminderEmails()
    {
        $now = now();
        $tomorrow = now()->addDay();
        $assignments = Assignment::whereBetween('deadline', [$now, $tomorrow])->get();

        Log::info('Running sendReminderEmails', ['assignments_count' => $assignments->count()]);

        foreach ($assignments as $assignment) {
            $classroom = $assignment->schedule->classroom;
            $students = $classroom->students;

            Log::info('Processing assignment', ['assignment_id' => $assignment->id, 'students_count' => $students->count()]);

            foreach ($students as $student) {
                if ($student->user && $student->user->email) {
                    Log::info('Sending email to', ['email' => $student->user->email]);
                    Mail::to($student->user->email)->queue(new TaskReminderMail($assignment));
                }
            }
        }
    }
}