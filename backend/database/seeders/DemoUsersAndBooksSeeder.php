<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersAndBooksSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['Anna', 'Kalniņa', 'anna.kalnina', 'Rīga'],
            ['Pēteris', 'Mārtins', 'peteris.martins', 'Liepāja'],
            ['Līga', 'Vītola', 'liga.vitola', 'Jelgava'],
            ['Māris', 'Riekstiņš', 'maris.riekstins', 'Valmiera'],
            ['Elīna', 'Liepa', 'elina.liepa', 'Daugavpils'],
            ['Jūlija', 'Roze', 'julija.roze', 'Ventspils'],
            ['Zane', 'Krūmiņa', 'zane.krumina', 'Cēsis'],
            ['Artūrs', 'Ozols', 'arturs.ozols', 'Ogre'],
            ['Laura', 'Bērziņa', 'laura.berzina', 'Tukums'],
            ['Roberts', 'Siliņš', 'roberts.silins', 'Rēzekne'],
        ];

        $books = [
            ['1984', 'Džordžs Orvels', 'Klasika'],
            ['Mazais princis', 'Antuāns de Sent-Ekziperī', 'Klasika'],
            ['Harijs Poters', 'Dž. K. Roulinga', 'Fantāzija'],
            ['Alķīmiķis', 'Paulu Koelju', 'Romāns'],
            ['Šerloks Holmss', 'Arturs Konans Doils', 'Detektīvs'],
            ['Sapiens', 'Juvals Noass Harari', 'Vēsture'],
            ['Atomiskie ieradumi', 'Džeimss Klīrs', 'Pašpalīdzība'],
            ['Mērnieku laiki', 'Reinis un Matīss Kaudzītes', 'Klasika'],
            ['Noziegums un sods', 'Fjodors Dostojevskis', 'Klasika'],
            ['Staburags', 'Valdis', 'Klasika'],
            ['Dune', 'Frenks Herberts', 'Fantāzija'],
            ['Zaļā jūdze', 'Stīvens Kings', 'Romāns'],
            ['Da Vinči kods', 'Dens Brauns', 'Detektīvs'],
            ['Dzīvnieku ferma', 'Džordžs Orvels', 'Klasika'],
            ['Hobits', 'Dž. R. R. Tolkīns', 'Fantāzija'],
            ['Domā ātri, domā lēnām', 'Daniels Kānemans', 'Zinātne'],
            ['Bagātais tētis, nabagais tētis', 'Roberts Kijosaki', 'Pašpalīdzība'],
            ['Zvaigžņu putekļi', 'Nīls Geimens', 'Fantāzija'],
            ['Meitene vilcienā', 'Paula Hokinsa', 'Detektīvs'],
            ['Kā iegūt draugus', 'Deils Kārnegijs', 'Pašpalīdzība'],
        ];

        foreach ($users as $index => [$name, $surname, $username, $city]) {
            $user = User::firstOrCreate(
                ['email' => $username . '@test.lv'],
                [
                    'name' => $name,
                    'surname' => $surname,
                    'username' => $username,
                    'city' => $city,
                    'about' => 'Man patīk lasīt un apmainīties ar grāmatām.',
                    'password' => Hash::make('password123'),
                ]
            );

            for ($i = 0; $i < 2; $i++) {
                $bookData = $books[$index * 2 + $i];

                Book::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'title' => $bookData[0],
                    ],
                    [
                        'author' => $bookData[1],
                        'genre' => $bookData[2],
                        'condition' => ['Jauna', 'Labā stāvoklī', 'Vidējā stāvoklī'][array_rand(['Jauna', 'Labā stāvoklī', 'Vidējā stāvoklī'])],
                        'image' => null,
                    ]
                );
            }
        }
    }
}