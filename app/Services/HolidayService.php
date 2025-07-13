<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HolidayService
{
    protected $holidays = [
        // 2025
        '2025-01-01' => 'Tahun Baru Masehi',
        '2025-01-29' => 'Tahun Baru Imlek 2576',
        '2025-03-28' => 'Hari Raya Nyepi',
        '2025-04-18' => 'Wafat Isa Almasih',
        '2025-05-01' => 'Hari Buruh Internasional',
        '2025-05-29' => 'Kenaikan Isa Almasih',
        '2025-06-01' => 'Hari Lahir Pancasila',
        '2025-06-07' => 'Idul Fitri 1446 H',
        '2025-06-08' => 'Idul Fitri 1446 H',
        '2025-08-14' => 'Hari Pramuka',
        '2025-08-17' => 'Hari Kemerdekaan RI',
        '2025-09-05' => 'Maulid Nabi Muhammad SAW',
        '2025-12-25' => 'Hari Raya Natal',
        // 2026
        '2026-01-01' => 'Tahun Baru Masehi',
        '2026-02-17' => 'Tahun Baru Imlek 2577',
        '2026-03-18' => 'Hari Raya Nyepi',
        '2026-04-03' => 'Wafat Isa Almasih',
        '2026-05-01' => 'Hari Buruh Internasional',
        '2026-05-14' => 'Kenaikan Isa Almasih',
        '2026-06-01' => 'Hari Lahir Pancasila',
        '2026-05-28' => 'Idul Fitri 1447 H',
        '2026-05-29' => 'Idul Fitri 1447 H',
        '2026-08-14' => 'Hari Pramuka',
        '2026-08-17' => 'Hari Kemerdekaan RI',
        '2026-08-25' => 'Maulid Nabi Muhammad SAW',
        '2026-12-25' => 'Hari Raya Natal',
    ];

    public function isHoliday(Carbon $date): bool
    {
        $formattedDate = $date->toDateString();
        $isHoliday = $date->isWeekend() || array_key_exists($formattedDate, $this->holidays);

        Log::info('Checking holiday', [
            'date' => $formattedDate,
            'is_holiday' => $isHoliday,
            'holiday_name' => $isHoliday && !$date->isWeekend() ? $this->holidays[$formattedDate] : null,
        ]);

        return $isHoliday;
    }

    public function getNextNonHoliday(Carbon $date): Carbon
    {
        $nextDate = $date->copy()->addDay();
        while ($this->isHoliday($nextDate)) {
            $nextDate->addDay();
        }
        Log::info('Next non-holiday date', [
            'from_date' => $date->toDateString(),
            'next_date' => $nextDate->toDateString(),
        ]);
        return $nextDate;
    }
}