<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Listing;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * @throws \Exception
     */
    public function run(): void
    {
        // create tags, users, and listings
        $tags = Tag::factory(10)->create();
        User::factory(5)->create()->each(static function (User $user) use ($tags) {
            Listing::factory(random_int(1, 4))->create([
                'user_id' => $user->id
            ])->each(static function (Listing $listing) use ($tags) {
                $listing->tags()->attach($tags->random(2));
            });
        });

        // User::factory(10)->create();
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
