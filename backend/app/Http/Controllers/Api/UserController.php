<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = User::query();

        if ($request->has('search')) {
            $query->where('name', 'ilike', "%{$request->search}%")
                ->orWhere('email', 'ilike', "%{$request->search}%");
        }

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $perPage = $request->input('per_page', 15);
        $users = $query->paginate($perPage);

        return UserResource::collection($users);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:admin,field_officer'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'data' => new UserResource($user),
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($user),
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,' . $user->id],
            'password' => ['sometimes', 'string', 'min:8'],
            'role' => ['sometimes', 'string', 'in:admin,field_officer'],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => new UserResource($user),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        // Prevent deleting own account
        if ($user->id === auth('api')->id()) {
            return response()->json([
                'message' => 'Cannot delete your own account',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Password reset successfully',
        ]);
    }
}
