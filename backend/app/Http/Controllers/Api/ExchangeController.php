<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Exchange;
use App\Models\Conversation;

class ExchangeController extends Controller
{
    public function index(Request $request)
    {
        return Exchange::with([
            'requester',
            'owner',
            'requestedBook',
            'offeredBook',
            'conversation.messages' => function ($query) {
                $query->latest()->limit(1);
            },
        ])
            ->where('requester_id', $request->user()->id)
            ->orWhere('owner_id', $request->user()->id)
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'requested_book_id' => ['required', 'exists:books,id'],
            'offered_book_id' => ['nullable', 'exists:books,id'],
        ]);

        $requestedBook = Book::findOrFail($data['requested_book_id']);

        if ($requestedBook->user_id === $request->user()->id) {
            return response()->json([
                'message' => 'Jūs nevarat apmainīties ar savu grāmatu.',
            ], 422);
        }

        if (!$requestedBook->is_available) {
            return response()->json([
                'message' => 'Šī grāmata vairs nav pieejama apmaiņai.',
            ], 422);
        }

        if (!empty($data['offered_book_id'])) {
            $offeredBook = Book::findOrFail($data['offered_book_id']);

            if ($offeredBook->user_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'Piedāvātā grāmata nepieder jums.',
                ], 403);
            }

            if (!$offeredBook->is_available) {
                return response()->json([
                    'message' => 'Piedāvātā grāmata vairs nav pieejama apmaiņai.',
                ], 422);
            }
        }

        $userOneId = min($request->user()->id, $requestedBook->user_id);
        $userTwoId = max($request->user()->id, $requestedBook->user_id);

        $conversation = Conversation::firstOrCreate([
            'user_one_id' => $userOneId,
            'user_two_id' => $userTwoId,
        ]);

        $exchange = Exchange::create([
            'requester_id' => $request->user()->id,
            'owner_id' => $requestedBook->user_id,
            'requested_book_id' => $requestedBook->id,
            'offered_book_id' => $data['offered_book_id'] ?? null,
            'status' => 'pending',
            'conversation_id' => $conversation->id,
        ]);

        return response()->json($exchange->load([
            'requester',
            'owner',
            'requestedBook',
            'offeredBook',
            'conversation',
        ]), 201);
    }

    public function accept(Request $request, Exchange $exchange)
    {
        if ($exchange->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($exchange->status !== 'pending') {
            return response()->json([
                'message' => 'Šis apmaiņas pieprasījums vairs nav aktīvs.',
            ], 422);
        }

        $exchange->loadMissing([
            'conversation',
            'requestedBook',
            'offeredBook',
        ]);

        if (!$exchange->requestedBook?->is_available) {
            $exchange->update([
                'status' => 'rejected',
            ]);

            return response()->json([
                'message' => 'Šī grāmata vairs nav pieejama apmaiņai.',
            ], 422);
        }

        if ($exchange->offered_book_id && !$exchange->offeredBook?->is_available) {
            $exchange->update([
                'status' => 'rejected',
            ]);

            return response()->json([
                'message' => 'Piedāvātā grāmata vairs nav pieejama apmaiņai.',
            ], 422);
        }

        $exchange->update([
            'status' => 'accepted',
        ]);

        $exchange->requestedBook()->update([
            'is_available' => false,
        ]);

        if ($exchange->offered_book_id) {
            $exchange->offeredBook()->update([
                'is_available' => false,
            ]);
        }

        if ($exchange->conversation) {
            $exchange->conversation->messages()->create([
                'user_id' => null,
                'message' => 'Apmaiņa apstiprināta. Vienojieties par detaļām.',
            ]);
        }

        $bookIds = [$exchange->requested_book_id];

        if ($exchange->offered_book_id) {
            $bookIds[] = $exchange->offered_book_id;
        }

        $rejectedExchanges = Exchange::with('conversation')
            ->where('id', '!=', $exchange->id)
            ->where('status', 'pending')
            ->where(function ($query) use ($bookIds) {
                $query->whereIn('requested_book_id', $bookIds)
                    ->orWhereIn('offered_book_id', $bookIds);
            })
            ->get();

        foreach ($rejectedExchanges as $rejectedExchange) {
            $rejectedExchange->update([
                'status' => 'rejected',
            ]);

            if ($rejectedExchange->conversation) {
                $rejectedExchange->conversation->messages()->create([
                    'user_id' => null,
                    'message' => 'Apmaiņa tika noraidīta, jo grāmata vairs nav pieejama.',
                ]);
            }
        }

        return response()->json($exchange->load([
            'requester',
            'owner',
            'requestedBook',
            'offeredBook',
            'conversation',
        ]));
    }

    public function reject(Request $request, Exchange $exchange)
    {
        if ($exchange->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $exchange->update([
            'status' => 'rejected',
        ]);

        if ($exchange->conversation) {
            $exchange->conversation->messages()->create([
                'user_id' => null,
                'message' => 'Apmaiņas pieprasījums tika noraidīts.',
            ]);
        }

        return response()->json($exchange);
    }

    public function complete(Request $request, Exchange $exchange)
    {
        if (
            $exchange->owner_id !== $request->user()->id &&
            $exchange->requester_id !== $request->user()->id
        ) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $exchange->update([
            'status' => 'completed',
        ]);

        if ($exchange->conversation) {
            $exchange->conversation->messages()->create([
                'user_id' => null,
                'message' => 'Apmaiņa pabeigta.',
            ]);
        }

        return response()->json($exchange);
    }

    public function destroy(Request $request, Exchange $exchange)
    {
        if (
            $exchange->owner_id !== $request->user()->id &&
            $exchange->requester_id !== $request->user()->id
        ) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($exchange->conversation) {
            $exchange->conversation->messages()->create([
                'user_id' => null,
                'message' => 'Apmaiņas pieprasījums tika atcelts.',
            ]);
        }

        $exchange->delete();

        return response()->json([
            'message' => 'Exchange deleted',
        ]);
    }
}