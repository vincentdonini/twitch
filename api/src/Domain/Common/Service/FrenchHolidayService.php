<?php

namespace App\Domain\Common\Service;

final class FrenchHolidayService
{
    public function isFrenchHoliday(\DateTimeInterface $date): bool
    {
        $year     = (int)$date->format('Y');
        $dayMonth = $date->format('m-d');

        // Fixed public holidays
        // -------------------------------------------------------------------------------------------------------------
        $fixedHolidays = [
            '01-01',
            '05-01',
            '05-08',
            '07-14',
            '08-15',
            '11-01',
            '11-11',
            '12-25',
        ];

        if (in_array($dayMonth, $fixedHolidays, true)) {
            return true;
        }

        // Movable public holidays (Easter)
        // -------------------------------------------------------------------------------------------------------------
        $easter     = easter_date($year);
        $easterDate = (new \DateTimeImmutable())->setTimestamp($easter);

        $mobileHolidays = [
            $easterDate->modify('+1 day'),   // Easter Monday
            $easterDate->modify('+39 days'), // Ascension Day,
            $easterDate->modify('+50 days'), // Whit Monday
        ];

        foreach ($mobileHolidays as $holiday) {
            if ($holiday->format('Y-m-d') === $date->format('Y-m-d')) {
                return true;
            }
        }

        return false;
    }
}
