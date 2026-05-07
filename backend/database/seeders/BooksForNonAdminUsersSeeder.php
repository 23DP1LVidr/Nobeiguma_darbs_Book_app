<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class BooksForNonAdminUsersSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'title' => 'Vēja pilsētas noslēpums',
                'author' => 'Marta Ozola',
                'genre' => 'Detektīvs',
                'condition' => 'Labā stāvoklī',
            ],
            [
                'title' => 'Zvaigžņu ceļinieki',
                'author' => 'Edgars Kalniņš',
                'genre' => 'Fantāzija',
                'condition' => 'Vidējā stāvoklī',
            ],
        ];

        User::where('role', '!=', 'admin')->get()->each(function (User $user) use ($templates) {
            foreach ($templates as $book) {
                $user->books()->create([
                    ...$book,
                    'image' => null,
                    'is_available' => true,
                ]);
            }
        });
    }
}
