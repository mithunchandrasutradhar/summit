<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class ReferenceNumber
{
    /**
     * Generate a reference number like "REG-2026-000123" for the given
     * table, unique on retry in the (extremely unlikely) event of a
     * collision. $prefix identifies the workflow (REG, AWD, SPN, EXH, FRM).
     */
    public static function generate(string $table, string $prefix): string
    {
        $year = now()->year;

        do {
            $sequence = random_int(1, 999999);
            $candidate = sprintf('%s-%d-%06d', $prefix, $year, $sequence);
        } while (DB::table($table)->where('reference_no', $candidate)->exists());

        return $candidate;
    }
}
