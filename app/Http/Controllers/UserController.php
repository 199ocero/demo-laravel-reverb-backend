<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    public function me()
    {
        return response()->json([
            'data' => auth()->user(),
        ]);
    }
}
