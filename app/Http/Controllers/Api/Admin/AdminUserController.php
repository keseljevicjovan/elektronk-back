<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\User\{StoreUserRequest, UpdateUserRequest};
use App\Models\User;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class AdminUserController extends Controller
{
    /**
     * Displays a list of users.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return response()->json(
            User::paginate($request->input('per_page', 50))
        );
    }

    /**
     * Displays details of a specific user.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'data' => $user
        ]);
    }

    /**
     * Store a new user.
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = User::create($data);

        return response()->json([
            'data' => [
                'message' => 'User created successfully',
                'user' => $user
            ]
        ], 201);
    }

    /**
     * Update a specific user.
     *
     * @param UpdateUserRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validated();

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $user->update($data);

        return response()->json([
            'data' => [
                'message' => 'User updated successfully',
                'user' => $user
            ]
        ]);
    }

    /**
     * Delete a specific user.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->noContent();
    }
}
