<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class RandomFeedSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        $legacyMessages = [
            'Tik pabeidzu jaunu romanu, un beigas vel joprojam neliek mieru.',
            'Vai kadam ir labs detektivs nedelas nogalei?',
            'Sodien sakartoju savu gramatu plauktu un atradu tris aizmirstas perles.',
            'Mekleju fantazijas gramatu ar specigu pasaules uzbuvi.',
            'Kafija, lietus un laba gramata. Ideals vakars.',
            'Vai kads gribetu apmainit klasiku pret kadu musdienu romanu?',
            'Nesen izlasiju gramatu, kuru noteikti gribesu parlasit.',
            'Mana apmainas saraksta paradijas paris jaunas gramatas.',
            'Kura bija pedeja gramata, kas jus patiesam parsteidza?',
            'Sonedel meginu lasit vairak latviesu autoru.',
        ];

        $messages = [
            'Tikko pabeidzu jaunu romānu, un beigas vēl joprojām neliek mieru.',
            'Vai kādam ir labs detektīvs nedēļas nogalei?',
            'Šodien sakārtoju savu grāmatu plauktu un atradu trīs aizmirstas pērles.',
            'Meklēju fantāzijas grāmatu ar spēcīgu pasaules uzbūvi.',
            'Kafija, lietus un laba grāmata. Ideāls vakars.',
            'Vai kāds gribētu apmainīt klasiku pret kādu mūsdienu romānu?',
            'Nesen izlasīju grāmatu, kuru noteikti gribēšu pārlasīt.',
            'Manā apmaiņas sarakstā parādījās pāris jaunas grāmatas.',
            'Kura bija pēdējā grāmata, kas jūs patiešām pārsteidza?',
            'Šonedēļ mēģinu lasīt vairāk latviešu autoru.',
        ];

        $legacyComments = [
            'Labs ieteikums!',
            'So ari gribu izlasit.',
            'Man si tema loti patik.',
            'Varu piedavat kaut ko lidzigu.',
            'Izklausas pec manas nakamas gramatas.',
        ];

        $comments = [
            'Labs ieteikums!',
            'Šo arī gribu izlasīt.',
            'Man šī tēma ļoti patīk.',
            'Varu piedāvāt kaut ko līdzīgu.',
            'Izklausās pēc manas nākamās grāmatas.',
        ];

        foreach ($legacyMessages as $index => $legacyMessage) {
            Post::where('content', $legacyMessage)->update([
                'content' => $messages[$index],
            ]);
        }

        foreach ($legacyComments as $index => $legacyComment) {
            PostComment::where('content', $legacyComment)->update([
                'content' => $comments[$index],
            ]);
        }

        foreach ($messages as $message) {
            if (Post::where('content', $message)->exists()) {
                continue;
            }

            Post::create([
                'user_id' => $users->random()->id,
                'content' => $message,
                'created_at' => now()->subMinutes(rand(5, 900)),
                'updated_at' => now()->subMinutes(rand(1, 4)),
            ]);
        }

        $posts = Post::latest()->take(count($messages))->get();

        foreach ($posts->take(6) as $post) {
            $commenter = $users->where('id', '!=', $post->user_id)->random();

            PostComment::create([
                'post_id' => $post->id,
                'user_id' => $commenter->id,
                'content' => $comments[array_rand($comments)],
            ]);
        }
    }
}
