<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => User::query()->whereNot('id', auth()->id())->get(),
        ]);
    }

    public function show(User $user)
    {
        return response()->json([
            'data' => $user,
        ]);
    }

    public function me()
    {
        return response()->json([
            'data' => auth()->user(),
        ]);
    }
}
