<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class AttendanceExport implements FromCollection, WithHeadings
{
    protected $attendances;

    public function __construct(Collection $attendances)
    {
        $this->attendances = $attendances;
    }

    public function collection()
    {
        return $this->attendances->map(function ($attendance) {
            return [
                'Nama' => $attendance->user_name,
                'Tipe' => $attendance->user_type === 'student' ? 'Siswa' : 'Guru',
                'Tanggal' => Carbon::parse($attendance->tanggal)->format('d/m/Y'),
                'Waktu Masuk' => $attendance->waktu_masuk,
                'Waktu Pulang' => $attendance->waktu_pulang ?? '-',
                'Status' => ucfirst($attendance->status),
                'Metode' => ucfirst($attendance->metode_absen),
            ];
        });
    }

    public function headings(): array
    {
        return ['Nama', 'Tipe', 'Tanggal', 'Waktu Masuk', 'Waktu Pulang', 'Status', 'Metode'];
    }
}