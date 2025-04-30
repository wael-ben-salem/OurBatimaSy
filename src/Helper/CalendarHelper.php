<?php
namespace App\Helper;

class CalendarHelper
{
    public function generateCalendar(int $year, int $month, array $events): array
    {
        $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
        $daysInMonth = date('t', $firstDayOfMonth);
        $firstDayOfWeek = date('w', $firstDayOfMonth);
        
        $calendar = [];
        $dayCount = 1;
        
        // Create weeks
        for ($i = 0; $i < 6; $i++) {
            // Create days
            for ($j = 0; $j < 7; $j++) {
                if (($i === 0 && $j < $firstDayOfWeek) || $dayCount > $daysInMonth) {
                    $calendar[$i][$j] = null;
                } else {
                    $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $dayCount);
                    $calendar[$i][$j] = [
                        'day' => $dayCount,
                        'events' => array_filter($events, function($event) use ($currentDate) {
                            $eventDate = $event['date']->format('Y-m-d');
                            return $eventDate === $currentDate;
                        })
                    ];
                    $dayCount++;
                }
            }
        }
        
        return $calendar;
    }
}