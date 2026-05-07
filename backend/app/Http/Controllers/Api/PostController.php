<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostComment;
use App\Support\ImageStorage;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        return Post::with([
            'user',
            'comments.user',
        ])
            ->withCount('likedByUsers')
            ->latest()
            ->get()
            ->map(fn (Post $post) => $this->formatPost($post, $request));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
            'image' => ['nullable', 'string'],
        ]);

        $data['image'] = ImageStorage::storeDataUrl(
            $data['image'] ?? null,
            'posts',
            $request->getSchemeAndHttpHost()
        );

        $post = $request->user()->posts()->create($data);

        return response()->json(
            $this->formatPost(
                $post->load(['user', 'comments.user'])->loadCount('likedByUsers'),
                $request
            ),
            201
        );
    }

    public function toggleLike(Request $request, Post $post)
    {
        $user = $request->user();
        $isLiked = $post->likedByUsers()
            ->where('users.id', $user->id)
            ->exists();

        if ($isLiked) {
            $post->likedByUsers()->detach($user->id);
        } else {
            $post->likedByUsers()->attach($user->id);
        }

        return response()->json(
            $this->formatPost(
                $post->fresh()->load(['user', 'comments.user'])->loadCount('likedByUsers'),
                $request
            )
        );
    }

    public function comment(Request $request, Post $post)
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $data['content'],
        ]);

        return response()->json(
            $this->formatPost(
                $post->fresh()->load(['user', 'comments.user'])->loadCount('likedByUsers'),
                $request
            ),
            201
        );
    }

    public function destroy(Request $request, Post $post)
    {
        if (! $this->canModerate($request, $post->user_id)) {
            abort(403, 'Jums nav atļaujas dzēst šo ierakstu.');
        }

        ImageStorage::deleteStoredImage($post->image);

        $post->delete();

        return response()->noContent();
    }

    public function destroyComment(Request $request, Post $post, PostComment $comment)
    {
        if ($comment->post_id !== $post->id) {
            abort(404, 'Komentārs nepieder pie šī ieraksta.');
        }

        if (! $this->canModerate($request, $comment->user_id)) {
            abort(403, 'Jums nav atļaujas dzēst šo komentāru.');
        }

        $comment->delete();

        return response()->json(
            $this->formatPost(
                $post->fresh()->load(['user', 'comments.user'])->loadCount('likedByUsers'),
                $request
            )
        );
    }

    private function formatPost(Post $post, Request $request): array
    {
        $userId = $request->user()?->id;

        return [
            'id' => $post->id,
            'user_id' => $post->user_id,
            'content' => $post->content,
            'image' => $post->image,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
            'user' => $post->user,
            'comments' => $post->comments,
            'comments_count' => $post->comments->count(),
            'likes_count' => $post->liked_by_users_count,
            'liked_by_current_user' => $userId
                ? $post->likedByUsers()->where('users.id', $userId)->exists()
                : false,
        ];
    }

    private function canModerate(Request $request, int $ownerId): bool
    {
        return $request->user()->role === 'admin' || $request->user()->id === $ownerId;
    }
}
