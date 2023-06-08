<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
  

    public function run()
    {

          
        Role::factory()->create([
            'title' => 'member',
        ]
        );
        Role::factory()->create([
            'title' => 'admin',
        ]
        );
        Role::factory()->create([
            'title' => 'owner',
        ]
        );

        DB::table('users')->insert([
            'username' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@argon.com',
            'password' => bcrypt('secret')
        ]);

        Category::factory()
        ->count(20)
        ->create();
    }
}
