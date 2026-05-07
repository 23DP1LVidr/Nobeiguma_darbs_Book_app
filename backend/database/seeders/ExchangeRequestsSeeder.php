<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Conversation;
use App\Models\Exchange;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExchangeRequestsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', '!=', 'admin')
            ->whereHas('books')
            ->with('books')
            ->get();

        if ($users->count() < 2) {
            return;
        }

        $created = 0;

        foreach ($users as $requester) {
            $owner = $users
                ->where('id', '!=', $requester->id)
                ->filter(fn (User $user) => $user->books->isNotEmpty())
                ->random();

            $requestedBook = $owner->books->where('is_available', true)->random();
            $offeredBook = $requester->books->where('is_available', true)->first();

            if (!$requestedBook) {
                continue;
            }

            $conversation = Conversation::firstOrCreate([
                'user_one_id' => min($requester->id, $owner->id),
                'user_two_id' => max($requester->id, $owner->id),
            ]);

            Exchange::create([
                'requester_id' => $requester->id,
                'owner_id' => $owner->id,
                'requested_book_id' => $requestedBook->id,
                'offered_book_id' => $offeredBook?->id,
                'status' => 'pending',
                'conversation_id' => $conversation->id,
            ]);

            $created++;

            if ($created >= 8) {
                break;
            }
        }
    }
}
