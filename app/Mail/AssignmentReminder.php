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
    public $teacherName;
    public $subjectName;
    public $studentName;

    public function __construct(Assignment $assignment, $teacherName, $subjectName, $studentName)
    {
        $this->assignment = $assignment;
        $this->teacherName = $teacherName;
        $this->subjectName = $subjectName;
        $this->studentName = $studentName;
    }

    public function build()
    {
        return $this->subject('Pengingat Tugas: ' . $this->assignment->title)
                    ->markdown('emails.assignment_reminder')
                    ->with([
                        'studentName' => $this->studentName,
                        'teacherName' => $this->teacherName,
                        'subjectName' => $this->subjectName,
                        'assignmentTitle' => $this->assignment->title,
                        'deadline' => \Carbon\Carbon::parse($this->assignment->deadline)->translatedFormat('l, d F Y H:i'),
                    ]);
    }
}