<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RollPermissionSeeder::class,
        ]);
        //creating admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('12345678'),
        ]);
        $author = User::factory()->create([
            'name' => 'Author User',
            'email' => 'author@example.com',
            'password' => Hash::make('12345678'),
        ]);
        $editor = User::factory()->create([
            'name' => 'Editor User',
            'email' => 'editor@example.com',
        ]);
        $subscriber = User::factory()->create([
            'name' => 'Subscriber User',
            'email' => 'subscriber@example.com',
            'password' => Hash::make('12345678'),
        ]);

        $admin->assignRole('admin');
        $author->assignRole('author');
        $editor->assignRole('editor');
        $subscriber->assignRole('subscriber');
    }
}
