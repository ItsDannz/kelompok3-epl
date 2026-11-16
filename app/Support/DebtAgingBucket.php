<?php

namespace App\Support;

// RF-011: Ekstraksi penentuan bucket aging.

use Carbon\CarbonInterface;

final class DebtAgingBucket
{
    public static function resolve(?string $status, ?CarbonInterface $dueDate): string
    {
        if ($status === 'paid') {
            return 'paid';
        }

        if (! $dueDate) {
            return 'no_due_date';
        }

        if (! now()->gt($dueDate)) {
            return 'current';
        }

        $daysOverdue = now()->diffInDays($dueDate, true);

        return match (true) {
            $daysOverdue <= 30 => '0-30',
            $daysOverdue <= 60 => '31-60',
            $daysOverdue <= 90 => '61-90',
            default => '90+',
        };
    }
}
