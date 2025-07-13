<?php

namespace App\Mail;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AssignmentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $assignment;

    public function __construct(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }

    public function build()
    {
        return $this->subject('Pengingat: Deadline Tugas Mendekati!')
                    ->view('emails.assignment_reminder');
    }
}