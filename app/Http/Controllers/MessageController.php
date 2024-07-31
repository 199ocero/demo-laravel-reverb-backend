<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function show(int $id)
    {
        return response()->json([
            'data' => Message::query()
                ->where(function ($query) use ($id) {
                    $query->where('sender_id', auth()->user()->id)
                        ->where('receiver_id', $id);
                })
                ->orWhere(function ($query) use ($id) {
                    $query->where('sender_id', $id)
                        ->where('receiver_id', auth()->user()->id);
                })
                ->latest()
                ->paginate(15),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $receiver = User::find($request->receiver_id);

        if (! $receiver) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        $message = Message::create([
            'sender_id' => auth()->user()->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        broadcast(new \App\Events\MessageSent(
            $receiver,
            auth()->user(),
            $message
        ));

        return \response()->json([
            'message' => 'Message sent.',
            'data' => $message,
        ], 201);
    }
}
