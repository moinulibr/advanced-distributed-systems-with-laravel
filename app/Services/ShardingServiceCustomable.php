<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ShardingServiceCustomable
{
    /**
     * Handles all BD phone formats (+88, 88, 0088) and converts them to standard 11 digits.
     * Returns a valid 11-digit string or null if invalid.
     */
    public static function sanitizePhone($phone)
    {
        // 1. Remove all non-digit characters (e.g., +, -, spaces)
        $number = preg_replace('/[^0-9]/', '', $phone);

        // 2. If the number is longer than 11 digits, take only the last 11 digits
        // to extract the 01XXXXXXXXX part correctly.
        if (strlen($number) > 11) {
            $number = substr($number, -11);
        }

        // 3. If the number is 10 digits and starts with '1', prepend '0' (e.g., 17... to 017...)
        if (strlen($number) === 10 && str_starts_with($number, '1')) {
            $number = '0' . $number;
        }

        // 4. Final check: Must be exactly 11 digits and must start with '01'
        if (strlen($number) === 11 && str_starts_with($number, '01')) {
            return $number;
        }

        return null;
    }

    /**
     * Determine the database shard based on sanitized phone number and email address.
     */
    public static function getShard(string $cleanPhone, string $email): string
    {
        $prefix = substr($cleanPhone, 0, 3);
        $firstLetter = strtolower($email[0] ?? 'a');

        if (in_array($prefix, ['013', '017']) || ($firstLetter >= 'a' && $firstLetter <= 'j')) {
            return 'mysql_shard_1';
        }

        if (in_array($prefix, ['014', '019', '015']) || ($firstLetter >= 'k' && $firstLetter <= 'r')) {
            return 'mysql_shard_2';
        }

        return 'mysql_shard_3';
    }

    /**
     * Get a list of all available shard connections.
     */
    public static function getAllShards(): array
    {
        return ['mysql_shard_1', 'mysql_shard_2', 'mysql_shard_3'];
    }
}
