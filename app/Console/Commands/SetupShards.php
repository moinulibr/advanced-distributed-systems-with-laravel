<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupShards extends Command
{
    protected $signature = 'shards:setup';
    protected $description = 'Creates tables on all shards without touching central DB';

    public function handle()
    {
        $shards = ['mysql_shard_1', 'mysql_shard_2', 'mysql_shard_3'];

        foreach ($shards as $shard) {
            $this->warn("Checking connection for $shard...");

            try {
                // সরাসরি শার্ডে টেবিল তৈরি
                $this->createUsersTable($shard);
                $this->createProfilesTable($shard);

                $this->info("✔ Shard $shard is ready.");
            } catch (\Exception $e) {
                $this->error("✘ Failed on $shard: " . $e->getMessage());
            }
        }
    }

    private function createUsersTable($connection)
    {
        DB::connection($connection)->statement("
            CREATE TABLE IF NOT EXISTS users (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                shard_key VARCHAR(50) NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id, created_at),
                INDEX idx_phone (phone)
            ) ENGINE=InnoDB
            PARTITION BY RANGE (UNIX_TIMESTAMP(created_at)) (
                PARTITION p_2025 VALUES LESS THAN (UNIX_TIMESTAMP('2026-01-01 00:00:00')),
                PARTITION p_2026 VALUES LESS THAN (UNIX_TIMESTAMP('2027-01-01 00:00:00')),
                PARTITION p_future VALUES LESS THAN MAXVALUE
            );
        ");
    }

    private function createProfilesTable($connection)
    {
        DB::connection($connection)->statement("
            CREATE TABLE IF NOT EXISTS user_profiles (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                address TEXT NULL,
                city VARCHAR(255) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_user_id (user_id)
            ) ENGINE=InnoDB;
        ");
    }
}