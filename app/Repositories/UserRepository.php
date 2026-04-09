<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserProfile;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\ShardingService;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Aggregates users from all shards with pagination logic
     */
    public function allUsers($perPage = 15)
    {
        $allUsers = collect();

        foreach (ShardingService::getAllShards() as $shard) {
            // Eloquent 'on' method ব্যবহার করে প্রতিটি শার্ড থেকে ডেটা আনা
            $users = User::on($shard)
                ->with('profile')
                ->orderBy('created_at', 'desc')
                ->limit($perPage)
                ->get();

            $allUsers = $allUsers->concat($users);
        }

        // গ্লোবালি সর্ট করে ফাইনাল ডেটা রিটার্ন
        return $allUsers->sortByDesc('created_at')->values()->take($perPage)->all();
    }

    /**
     * Search user by email (Direct lookup - FAST)
     */
    public function findByEmail(string $email)
    {
        // যেহেতু ইমেইল আমাদের বেস, আমরা সরাসরি শার্ড ক্যালকুলেট করছি
        $shard = ShardingService::getShard($email);

        return User::on($shard)
            ->where('email', $email)
            ->with('profile')
            ->first();
    }

    /**
     * Search user by phone (Global lookup - Cross-shard)
     */
    public function findByPhone(string $phone)
    {
        $cleanPhone = ShardingService::sanitizePhone($phone);
        if (!$cleanPhone) return null;

        foreach (ShardingService::getAllShards() as $shard) {
            $user = User::on($shard)
                ->where('phone', $cleanPhone)
                ->with('profile')
                ->first();

            if ($user) return $user;
        }
        return null;
    }

    /**
     * Create a new user in the specific shard using Eloquent
     */
    public function create(array $data)
    {
        $shard = ShardingService::getShard($data['email']);

        return DB::connection($shard)->transaction(function () use ($shard, $data) {
            // User Instance
            $user = new User();
            $user->setConnection($shard);

            $user->fill([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'phone'     => ShardingService::sanitizePhone($data['phone']),
                'shard_key' => $shard,
                'created_at' => now(),
            ]);
            $user->save();

            // Profile Create using Eloquent Relationship
            $user->profile()->create([
                'address' => $data['address'] ?? null,
                'city'    => $data['city'] ?? null,
            ]);

            return $user->id;
        });
    }

    /**
     * Update user - Shard identified by EMAIL
     */
    public function update(int $id, array $data, string $phone, string $email)
    {
        $shard = ShardingService::getShard($email);

        $user = User::on($shard)->find($id);
        if ($user) {
            return $user->update($data);
        }
        return false;
    }

    /**
     * Delete user from the specific shard
     */
    public function delete(int $id, string $phone, string $email)
    {
        $shard = ShardingService::getShard($email);

        return DB::connection($shard)->transaction(function () use ($shard, $id) {
            $user = User::on($shard)->find($id);
            if ($user) {
                // রিলেশনসহ ডিলিট
                $user->profile()->delete();
                return $user->delete();
            }
            return false;
        });
    }
}
