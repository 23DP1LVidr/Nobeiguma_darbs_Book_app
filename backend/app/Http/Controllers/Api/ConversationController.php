<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    private function orderedUserIds(int $firstId, int $secondId): array
    {
        return $firstId < $secondId
            ? [$firstId, $secondId]
            : [$secondId, $firstId];
    }

    public function index(Request $request)
    {
        return Conversation::with([
            'userOne',
            'userTwo',
            'messages' => function ($query) {
                $query->latest()->limit(1);
            },
        ])
            ->where('user_one_id', $request->user()->id)
            ->orWhere('user_two_id', $request->user()->id)
            ->latest()
            ->get();
    }

    public function showOrCreate(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return response()->json([
                'message' => 'Jūs nevarat izveidot sarunu ar sevi.',
            ], 422);
        }

        [$userOneId, $userTwoId] = $this->orderedUserIds(
            $request->user()->id,
            $user->id
        );

        $conversation = Conversation::firstOrCreate([
            'user_one_id' => $userOneId,
            'user_two_id' => $userTwoId,
        ]);

        return response()->json(
            $conversation->load(['userOne', 'userTwo'])
        );
    }

    public function messages(Request $request, Conversation $conversation)
    {
        if (!$this->userBelongsToConversation($request, $conversation)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $conversation->messages()
            ->with('user')
            ->oldest()
            ->get();
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        if (!$this->userBelongsToConversation($request, $conversation)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = $conversation->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $data['message'],
        ]);

        return response()->json($message->load('user'), 201);
    }

    private function userBelongsToConversation(Request $request, Conversation $conversation): bool
    {
        return $conversation->user_one_id === $request->user()->id ||
            $conversation->user_two_id === $request->user()->id;
    }
}