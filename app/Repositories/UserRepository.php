<?php

namespace App\Repositories;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\ShardingService;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Search user by phone across all shards
     */
    public function findByPhone(string $phone)
    {
        $cleanPhone = ShardingService::sanitizePhone($phone);
        if (!$cleanPhone) return null;

        foreach (ShardingService::getAllShards() as $shard) {
            $user = DB::connection($shard)->table('users')
                ->where('phone', $cleanPhone)
                ->first();

            if ($user) {
                $user->profile = DB::connection($shard)->table('user_profiles')
                    ->where('user_id', $user->id)
                    ->first();
                return $user;
            }
        }
        return null;
    }

    /**
     * Search user by email across all shards
     */
    public function findByEmail(string $email)
    {
        foreach (ShardingService::getAllShards() as $shard) {
            $user = DB::connection($shard)->table('users')
                ->where('email', $email)
                ->first();

            if ($user) {
                $user->profile = DB::connection($shard)->table('user_profiles')
                    ->where('user_id', $user->id)
                    ->first();
                return $user;
            }
        }
        return null;
    }

    /**
     * Create a new user in the specific shard
     */
    public function create(array $data)
    {
        $cleanPhone = ShardingService::sanitizePhone($data['phone']);
        $shard = ShardingService::getShard($cleanPhone, $data['email']);

        return DB::connection($shard)->transaction(function () use ($shard, $data, $cleanPhone) {
            $userId = DB::connection($shard)->table('users')->insertGetId([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $cleanPhone,
                'shard_key' => $shard,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::connection($shard)->table('user_profiles')->insert([
                'user_id' => $userId,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $userId;
        });
    }

    /**
     * Update user in the specific shard
     */
    public function update(int $id, array $data, string $phone, string $email)
    {
        $shard = ShardingService::getShard($phone, $email);

        return DB::connection($shard)->table('users')
            ->where('id', $id)
            ->update(array_merge($data, ['updated_at' => now()]));
    }

    /**
     * Delete user from the specific shard
     */
    public function delete(int $id, string $phone, string $email)
    {
        $shard = ShardingService::getShard($phone, $email);

        return DB::connection($shard)->transaction(function () use ($shard, $id) {
            DB::connection($shard)->table('user_profiles')->where('user_id', $id)->delete();
            return DB::connection($shard)->table('users')->where('id', $id)->delete();
        });
    }
}
