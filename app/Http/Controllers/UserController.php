<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Test search by phone
     */
    public function searchByPhone($phone)
    {
        
        $user = $this->userRepo->findByPhone($phone);
        

        if ($user) {
            return response()->json([
                'status' => 'success',
                'message' => 'User found from shard: ' . $user->shard_key,
                'data' => $user
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
    }
}
