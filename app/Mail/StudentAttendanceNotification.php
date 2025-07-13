<?php

namespace App\Mail;

use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentAttendanceNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $attendance;

    /**
     * Create a new message instance.
     *
     * @param Student $student
     * @param StudentAttendance $attendance
     * @return void
     */
    public function __construct(Student $student, StudentAttendance $attendance)
    {
        $this->student = $student;
        $this->attendance = $attendance;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Notifikasi Absensi Siswa')
                    ->view('emails.student_attendance')
                    ->with([
                        'studentName' => $this->student->name,
                        'date' => $this->attendance->tanggal,
                        'time' => $this->attendance->waktu_masuk,
                    ]);
    }
}