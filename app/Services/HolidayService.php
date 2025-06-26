<?php

namespace App\Services;

use Carbon\Carbon;
use Spatie\Holidays\Holidays;
use Illuminate\Support\Facades\Log;

class HolidayService
{
    protected $holidays;

    public function __construct()
    {
        try {
            $this->holidays = Holidays::for(country: 'ID')->get(year: 2025);
        } catch (\Exception $e) {
            Log::error('Failed to initialize Spatie Holidays for Indonesia: ' . $e->getMessage(), [
                'year' => 2025,
                'country' => 'ID',
                'trace' => $e->getTraceAsString(),
            ]);
            $this->holidays = []; // Fallback to empty array
        }
    }

    public function isHoliday(Carbon $date): bool
    {
        if (empty($this->holidays)) {
            return $date->isWeekend(); // Fallback to only check weekends
        }

        $holidayDates = array_column($this->holidays, 'date');
        $formattedDate = $date->toDateString();

        return $date->isWeekend() || in_array($formattedDate, $holidayDates);
    }

    public function getNextNonHoliday(Carbon $date): Carbon
    {
        $nextDate = $date->copy()->addDay();
        while ($this->isHoliday($nextDate)) {
            $nextDate->addDay();
        }
        return $nextDate;
    }
}