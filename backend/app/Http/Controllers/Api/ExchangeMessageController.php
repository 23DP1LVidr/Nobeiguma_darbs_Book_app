<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exchange;
use Illuminate\Http\Request;

class ExchangeMessageController extends Controller
{
    public function index(Request $request, Exchange $exchange)
    {
        if (
            $exchange->requester_id !== $request->user()->id &&
            $exchange->owner_id !== $request->user()->id
        ) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $exchange->messages()
            ->with('user')
            ->oldest()
            ->get();
    }

    public function store(Request $request, Exchange $exchange)
    {
        if (
            $exchange->requester_id !== $request->user()->id &&
            $exchange->owner_id !== $request->user()->id
        ) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = $exchange->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $data['message'],
        ]);

        return response()->json($message->load('user'), 201);
    }
}