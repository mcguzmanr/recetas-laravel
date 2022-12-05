<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Peter Parker',
            'email' => 'peter@parker.com',
            'password' => Hash::make('spiderman'),
            'url' => 'https://es.wikipedia.org/wiki/Spider-Man',
        ]);

        $user2 = User::create([
            'name' => 'Clark Kent',
            'email' => 'clark@kent.com',
            'password' => Hash::make('superman'),
            'url' => 'https://es.wikipedia.org/wiki/Superman',
        ]);


    }
}
