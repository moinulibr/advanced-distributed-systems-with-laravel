<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Get list of users from all shards
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $users = $this->userRepo->allUsers($perPage);
        return view('users.index',compact('users',$users));
        return response()->json([
            'status' => 'success',
            'message' => 'User Lists',
            'users' => $users
        ], 201);
        //return $this->sendResponse($users, 'Users retrieved successfully from all shards.');
    }
    
    /**
     * Get list of users from all shards
     */
    public function create(Request $request)
    {
        return view('users.create');
        $perPage = $request->get('per_page', 10);
        $users = $this->userRepo->allUsers($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'User Lists',
            'users' => $users
        ], 201);
        //return $this->sendResponse($users, 'Users retrieved successfully from all shards.');
    }

    /**
     * Store a new user in the correct shard
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $userId = $this->userRepo->create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user_id' => $userId
        ], 201);
    }

    /**
     * Search user by phone
     */
    public function show($phone)
    {
        $user = $this->userRepo->findByPhone($phone);

        if ($user) {
            return response()->json([
                'status' => 'success',
                'data' => $user
            ]);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    /**
     * Update user (Requires current phone and email to find the shard)
     */
    public function update(Request $request, $id)
    {
        // Sharding update requires knowing which shard they are in
        // So we need phone and email to calculate the shard
        $updated = $this->userRepo->update(
            $id,
            $request->only('name'),
            $request->phone,
            $request->email
        );

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully'
        ]);
    }

    /**
     * Delete user
     */
    public function destroy(Request $request, $id)
    {
        $this->userRepo->delete($id, $request->phone, $request->email);

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted from shard'
        ]);
    }
}
