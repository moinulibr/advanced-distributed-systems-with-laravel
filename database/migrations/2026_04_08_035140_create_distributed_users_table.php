<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ১. Users Table with Sharding (Partitioning logic)
        DB::statement("
            CREATE TABLE users (
                id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                password VARCHAR(255) NULL,
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

        // ২. User Profiles Table (Relational Data)
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->text('address')->nullable();
            $table->string('bio')->nullable();
            $table->string('city')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('users');
    }
};