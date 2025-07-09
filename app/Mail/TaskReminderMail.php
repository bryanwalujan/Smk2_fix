<?php

namespace App\Mail;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $assignment;

    public function __construct(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }

    public function build()
    {
        return $this->subject('Pengingat: Tugas "' . $this->assignment->title . '" Akan Berakhir')
                    ->view('emails.task_reminder')
                    ->with(['assignment' => $this->assignment]);
    }
}