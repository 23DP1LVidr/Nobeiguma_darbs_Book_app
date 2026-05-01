<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\User;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            return;
        }

        $books = [
            ["title" => "1984", "author" => "Džordžs Orvels", "genre" => "Klasika", "condition" => "Labā stāvoklī"],
            ["title" => "Staburags", "author" => "Valdis", "genre" => "Klasika", "condition" => "Jauna"],
            ["title" => "Mērnieku laiki", "author" => "Reinis un Matīss Kaudzītes", "genre" => "Klasika", "condition" => "Vidējā stāvoklī"],
            ["title" => "Mazais princis", "author" => "Antuāns de Sent-Ekziperī", "genre" => "Klasika", "condition" => "Labā stāvoklī"],
            ["title" => "Harijs Poters", "author" => "Dž. K. Roulinga", "genre" => "Fantāzija", "condition" => "Jauna"],
            ["title" => "Alķīmiķis", "author" => "Paulu Koelju", "genre" => "Romāns", "condition" => "Labā stāvoklī"],
            ["title" => "Noziegums un sods", "author" => "Fjodors Dostojevskis", "genre" => "Klasika", "condition" => "Vidējā stāvoklī"],
            ["title" => "Šerloks Holmss", "author" => "Arturs Konans Doils", "genre" => "Detektīvs", "condition" => "Labā stāvoklī"],
            ["title" => "Sapiens", "author" => "Juvals Noass Harari", "genre" => "Vēsture", "condition" => "Jauna"],
            ["title" => "Atomiskie ieradumi", "author" => "Džeimss Klīrs", "genre" => "Pašpalīdzība", "condition" => "Labā stāvoklī"],
        ];

        foreach ($books as $book) {
            Book::create([
                ...$book,
                "user_id" => $user->id,
                "image" => null,
            ]);
        }
    }
}