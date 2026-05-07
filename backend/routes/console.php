<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Book;
use App\Models\Post;
use App\Models\User;
use App\Support\ImageStorage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('images:convert-base64 {--base-url=http://127.0.0.1:8000}', function () {
    $baseUrl = $this->option('base-url');
    $converted = 0;

    User::where('avatar', 'like', 'data:image/%')->each(function (User $user) use ($baseUrl, &$converted) {
        $user->forceFill([
            'avatar' => ImageStorage::storeDataUrl($user->avatar, 'avatars', $baseUrl),
        ])->save();

        $converted++;
    });

    Book::where('image', 'like', 'data:image/%')->each(function (Book $book) use ($baseUrl, &$converted) {
        $book->forceFill([
            'image' => ImageStorage::storeDataUrl($book->image, 'books', $baseUrl),
        ])->save();

        $converted++;
    });

    Post::where('image', 'like', 'data:image/%')->each(function (Post $post) use ($baseUrl, &$converted) {
        $post->forceFill([
            'image' => ImageStorage::storeDataUrl($post->image, 'posts', $baseUrl),
        ])->save();

        $converted++;
    });

    $this->info("Converted {$converted} image(s).");
})->purpose('Convert Base64 image fields to public storage URLs');

Artisan::command('books:fill-cover-images {--base-url=http://127.0.0.1:8000}', function () {
    $baseUrl = rtrim($this->option('base-url'), '/');
    $updated = 0;

    $existingCovers = Book::whereNotNull('image')
        ->where('image', '!=', '')
        ->get()
        ->keyBy(fn (Book $book) => Str::lower($book->title.'|'.$book->author));

    Book::where(function ($query) {
        $query->whereNull('image')->orWhere('image', '');
    })->get()->each(function (Book $book) use ($existingCovers, $baseUrl, &$updated) {
        $matchingCover = $existingCovers->get(Str::lower($book->title.'|'.$book->author));

        if ($matchingCover?->image) {
            $book->forceFill(['image' => $matchingCover->image])->save();
            $updated++;
            return;
        }

        $genre = $book->genre ?: 'Grāmata';
        $genreSvg = e($genre);
        $palette = match ($genre) {
            'Fantāzija' => ['#26324f', '#8fb3ff'],
            'Detektīvs' => ['#2c2c2c', '#e06f6f'],
            'Romāns' => ['#4b2e3f', '#f0a6c1'],
            'Klasika' => ['#3b3429', '#d8bd7f'],
            'Zinātne' => ['#1f3d3b', '#8fd8ca'],
            'Vēsture' => ['#493326', '#d4a373'],
            'Biogrāfija' => ['#26384a', '#9fc5e8'],
            'Dzeja' => ['#3d2d4f', '#c9a7ff'],
            default => ['#2f3a3f', '#9fb7c2'],
        };

        $titleLines = wordwrap($book->title, 18, "\n", true);
        $authorLines = wordwrap($book->author, 24, "\n", true);
        $titleSvg = collect(explode("\n", $titleLines))->take(4)->map(function ($line, $index) {
            return '<text x="40" y="'.(150 + ($index * 42)).'" fill="#ffffff" font-size="34" font-family="Arial, sans-serif" font-weight="700">'.e($line).'</text>';
        })->implode('');
        $authorSvg = collect(explode("\n", $authorLines))->take(2)->map(function ($line, $index) {
            return '<text x="40" y="'.(380 + ($index * 28)).'" fill="#f4f4f4" font-size="22" font-family="Arial, sans-serif">'.e($line).'</text>';
        })->implode('');

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="420" height="620" viewBox="0 0 420 620">
  <rect width="420" height="620" rx="28" fill="{$palette[0]}"/>
  <rect x="28" y="28" width="364" height="564" rx="20" fill="none" stroke="{$palette[1]}" stroke-width="6"/>
  <circle cx="330" cy="104" r="58" fill="{$palette[1]}" opacity="0.22"/>
  <path d="M92 82h54c22 0 40 18 40 40v72c0 8-8 13-15 9-9-5-19-8-30-8H92V82z" fill="#ffffff" opacity="0.92"/>
  <path d="M196 122c0-22 18-40 40-40h54v113h-49c-11 0-21 3-30 8-7 4-15-1-15-9v-72z" fill="#ffffff" opacity="0.92"/>
  <rect x="40" y="456" width="150" height="34" rx="17" fill="{$palette[1]}" opacity="0.9"/>
  <text x="58" y="479" fill="#15202b" font-size="18" font-family="Arial, sans-serif" font-weight="700">{$genreSvg}</text>
  {$titleSvg}
  {$authorSvg}
</svg>
SVG;

        $path = 'books/generated/'.Str::uuid().'.svg';
        Storage::disk('public')->put($path, $svg);

        $book->forceFill([
            'image' => $baseUrl.'/storage/'.$path,
        ])->save();

        $updated++;
    });

    $this->info("Updated {$updated} book cover image(s).");
})->purpose('Fill missing book cover images');
