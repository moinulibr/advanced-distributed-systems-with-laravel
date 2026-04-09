<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Services\ShardingService;
return new class extends Migration
{
    public function up(): void
    {
        $shards =  ['mysql_shard_1', 'mysql_shard_2', 'mysql_shard_3'];
        //$shards = ShardingService::getAllShards(); // ['mysql_shard_1', 'mysql_shard_2', 'mysql_shard_3'];

        foreach ($shards as $shard) {
            $this->createTablesOnShard($shard);
        }
    }

    private function createTablesOnShard($connection)
    {
        // ১. Users Table with Partitioning
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
                INDEX idx_email (email),
                INDEX idx_phone (phone),
                INDEX idx_shard (shard_key)
            ) ENGINE=InnoDB
            PARTITION BY RANGE (UNIX_TIMESTAMP(created_at)) (
                PARTITION p_old VALUES LESS THAN (UNIX_TIMESTAMP('2025-01-01 00:00:00')),
                PARTITION p_2025 VALUES LESS THAN (UNIX_TIMESTAMP('2026-01-01 00:00:00')),
                PARTITION p_2026 VALUES LESS THAN (UNIX_TIMESTAMP('2027-01-01 00:00:00')),
                PARTITION p_future VALUES LESS THAN MAXVALUE
            );
        ");

        // ২. User Profiles Table
        DB::connection($connection)->statement("
            CREATE TABLE IF NOT EXISTS user_profiles (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                address TEXT NULL,
                bio VARCHAR(255) NULL,
                city VARCHAR(255) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_user_id (user_id)
            ) ENGINE=InnoDB;
        ");
    }

    public function down(): void
    {
        $shards = ['mysql_shard_1', 'mysql_shard_2', 'mysql_shard_3'];
        foreach ($shards as $shard) {
            DB::connection($shard)->statement("DROP TABLE IF EXISTS user_profiles;");
            DB::connection($shard)->statement("DROP TABLE IF EXISTS users;");
        }
    }
};
