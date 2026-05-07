<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Exchange;
use App\Models\User;
use App\Support\ImageStorage;

class AdminController extends Controller
{
    public function indexUsers()
    {
        return User::latest()->get();
    }

    public function deleteUser(int $id)
    {
        $user = User::with(['books', 'posts'])->findOrFail($id);

        ImageStorage::deleteStoredImage($user->avatar);

        foreach ($user->books as $book) {
            ImageStorage::deleteStoredImage($book->image);
        }

        foreach ($user->posts as $post) {
            ImageStorage::deleteStoredImage($post->image);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted',
        ]);
    }

    public function indexBooks()
    {
        return Book::with('user')->latest()->get();
    }

    public function deleteBook(int $id)
    {
        $book = Book::findOrFail($id);

        ImageStorage::deleteStoredImage($book->image);

        $book->delete();

        return response()->json([
            'message' => 'Book deleted',
        ]);
    }

    public function indexExchanges()
    {
        return Exchange::with([
            'requester',
            'owner',
            'requestedBook',
            'offeredBook',
        ])->latest()->get();
    }
}
