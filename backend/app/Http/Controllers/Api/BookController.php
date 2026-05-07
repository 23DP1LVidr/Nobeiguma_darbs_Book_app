<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Support\ImageStorage;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // Get books that belong to the currently authenticated user (library page)
    public function index(Request $request)
    {
        return $request->user()->books;
    }

    // Get books from OTHER users (exchange page)
    public function available(Request $request)
    {
        $query = Book::with('user')
            ->where('is_available', true);

        if ($request->user()) {
            $query->where('user_id', '!=', $request->user()->id);
        }

        return $query->latest()->get();
    }

    // Store a new book for the authenticated user
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:255'],
            'condition' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string'], // base64 image
        ]);

        $data['image'] = ImageStorage::storeDataUrl(
            $data['image'] ?? null,
            'books',
            $request->getSchemeAndHttpHost()
        );

        $book = $request->user()->books()->create($data);

        return response()->json($book, 201);
    }

    // Update an existing book
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

        $data['image'] = ImageStorage::storeDataUrl(
            $data['image'] ?? null,
            'books',
            $request->getSchemeAndHttpHost(),
            $book->image
        );

        $book->update($data);

        return response()->json($book);
    }

    // Delete book
    public function destroy(Request $request, Book $book)
    {
        if ($book->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        ImageStorage::deleteStoredImage($book->image);

        $book->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
