<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Services\ShardingService;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class DistributedUserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('en_US');
        $totalUsers = 100000;
        $batchSize = 2500; // Increased batch size for better performance

        // Data holders organized by shard
        $shards = [
            'mysql_shard_1' => ['users' => [], 'profiles' => []],
            'mysql_shard_2' => ['users' => [], 'profiles' => []],
            'mysql_shard_3' => ['users' => [], 'profiles' => []]
        ];

        $this->command->info("Starting massive data distribution (100k users)...");

        for ($i = 1; $i <= $totalUsers; $i++) {
            // 1. Generate a unique safe email
            $email = $faker->unique()->safeEmail;

            // 2. Generate a random BD phone number in various formats
            $rawPhone = $this->generateBDPhone($faker);

            // 3. Sanitize the phone number
            $cleanPhone = ShardingService::sanitizePhone($rawPhone);

            // Skip if the phone number format is still invalid after sanitization
            if (!$cleanPhone) {
                //Log::warning("Invalid format skipped: $rawPhone");
                continue;
            }

            // 4. Select the appropriate shard using the clean phone number
            $shard = ShardingService::getShard($cleanPhone, $email);

            // 5. Generate random date (between 2024-2026) for partitioning tests
            $createdAt = Carbon::now()->subMonths(rand(0, 36));

            // 6. Prepare User data
            $shards[$shard]['users'][] = [
                'id'         => $i,
                'name'       => $faker->name,
                'email'      => $email,
                'phone'      => $cleanPhone,
                'shard_key'  => $shard,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // 7. Prepare Profile data (Relational table)
            $shards[$shard]['profiles'][] = [
                'user_id'    => $i,
                'address'    => $faker->address,
                'bio'        => $faker->sentence,
                'city'       => $faker->city,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Batch insert logic to save memory and reduce DB calls
            if (count($shards[$shard]['users']) >= $batchSize) {
                $this->insertToShard($shard, $shards[$shard]);
                $shards[$shard] = ['users' => [], 'profiles' => []]; // Reset batch
            }
        }

        // Insert remaining records in the holders
        foreach ($shards as $shardName => $data) {
            $this->insertToShard($shardName, $data);
        }

        $this->command->info("100,000 Users and Profiles successfully distributed!");
    }

    /**
     * Insert Users and Profiles into a specific shard connection.
     */
    private function insertToShard($shard, $data)
    {
        if (!empty($data['users'])) {
            try {
                DB::connection($shard)->table('users')->insert($data['users']);
                DB::connection($shard)->table('user_profiles')->insert($data['profiles']);
            } catch (\Exception $e) {
                Log::error("Insert failed for shard $shard: " . $e->getMessage());
            }
        }
    }

    /**
     * Generate a random Bangladeshi phone number with different prefixes.
     */
    private function generateBDPhone($faker)
    {
        $prefixes = ['013', '017', '014', '019', '015', '016', '018', '011'];
        $selectedPrefix = $prefixes[array_rand($prefixes)];

        // Prefix is 3 digits, so we need 8 more random digits to make 11 digits total.
        // mt_rand ensures we always get exactly 8 digits.
        $remainingDigits = mt_rand(10000000, 99999999);

        $number = $selectedPrefix . $remainingDigits;

        // Return in various formats to test the sanitizer
        $formats = [
            $number,                // 01712345678
            '+88' . $number,        // +8801712345678
            '88' . $number,         // 8801712345678
            '0088' . $number,       // 008801712345678
        ];

        return $formats[array_rand($formats)];
    }
}
