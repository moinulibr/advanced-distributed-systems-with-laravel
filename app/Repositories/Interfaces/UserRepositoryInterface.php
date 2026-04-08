<?php

namespace App\Repositories\Interfaces;

/**
 * Interface for distributed User Management
 * Handles CRUD operations across multiple shards
 */
interface UserRepositoryInterface
{
    public function findByPhone(string $phone);
    public function findByEmail(string $email);
    public function create(array $data);
    public function update(int $id, array $data, string $phone, string $email);
    public function delete(int $id, string $phone, string $email);
}
