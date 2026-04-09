<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
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
        $faker = Faker::create();
        $totalUsers = 100000;
        $batchSize = 1000; // managing the batch size for better performance

        $this->command->info("🚀 Starting seed: $totalUsers users across shards...");

        for ($i = 0; $i < ($totalUsers / $batchSize); $i++) {
            foreach (range(1, $batchSize) as $j) {
                $email = $faker->unique()->safeEmail;
                $phone = "01" . mt_rand(100000000, 999999999);

                //1. Shard determine by code based logic
                //$shard = ShardingService::getShard($phone, $email);
                $shard = ShardingService::getShard($email);

                // 2. Connection the certain shard by using Eloquent Model
                $user = new User();
                $user->setConnection($shard);

                $createdAt = Carbon::now()->subMonths(rand(0, 18));

                $user->fill([
                    'name' => $faker->name,
                    'email' => $email,
                    'phone' => $phone,
                    'shard_key' => $shard,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                $user->save();

                // 3. ready the profile in the same shard
                $profile = new UserProfile();
                $profile->setConnection($shard);
                $profile->fill([
                    'user_id' => $user->id,
                    'address' => $faker->address,
                    'city' => $faker->city,
                    'created_at' => $createdAt,
                ]);
                $profile->save();
            }

            $this->command->comment("✔ Inserted " . (($i + 1) * $batchSize) . " users...");
        }

        $this->command->info("🎉 Seeding Completed!");
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
