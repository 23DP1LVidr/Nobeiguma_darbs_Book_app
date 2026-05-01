<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->books;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:255'],
            'condition' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string'],
        ]);

        $book = $request->user()->books()->create($data);

        return response()->json($book, 201);
    }

    public function update(Request $request, Book $book)
    {
        if ($book->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:255'],
            'condition' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string'],
        ]);

        $book->update($data);

        return response()->json($book);
    }

    public function destroy(Request $request, Book $book)
    {
        if ($book->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $book->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function available(Request $request)
    {
        return Book::with('user')
            ->where('user_id', '!=', $request->user()->id)
            ->latest()
            ->get();
    }
}