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
     * Display User List
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $users = $this->userRepo->allUsers($perPage);

        // যদি রিকোয়েস্টটি AJAX বা API এর জন্য হয়
        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'users' => $users
            ]);
        }

        return view('users.index', compact('users'));
    }
    /**
     * Search user by phone (Global Search) or Email
     */
    public function search($identifier)
    {
        // যদি ইনপুটটি ইমেইল হয়
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = $this->userRepo->findByEmail($identifier);
        } else {
            $user = $this->userRepo->findByPhone($identifier);
        }

        if ($user) {
            return view('users.show', compact('user'));
            //return response()->json(['status' => 'success', 'data' => $user]);
        }

        return response()->json(['message' => 'User not found'], 404);
    }


    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a new user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'required|string',
            'address' => 'nullable|string',
            'city'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $userId = $this->userRepo->create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully on shard!',
            'user_id' => $userId
        ], 201);
    }

    /**
     * Search user by phone (Global Search) or Email
     */
    public function show($identifier)
    {
        // যদি ইনপুটটি ইমেইল হয়
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = $this->userRepo->findByEmail($identifier);
        } else {
            $user = $this->userRepo->findByPhone($identifier);
        }
    
        if ($user) {
            return view('users.show', compact('user'));
            //return response()->json(['status' => 'success', 'data' => $user]);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $updated = $this->userRepo->update(
            $id,
            $request->only('name'),
            $request->phone, // Shard locating
            $request->email  // Shard locating
        );

        return response()->json([
            'status' => $updated ? 'success' : 'error',
            'message' => $updated ? 'User updated' : 'Update failed'
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
