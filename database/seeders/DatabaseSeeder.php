<?php

namespace Database\Seeders;

use App\Models\Admin;
// use App\Models\User;
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
        Admin::firstOrCreate(['email' => 'network@wh40k.test'], [
            'name' => 'Network Admin',
            'password' => Hash::make('password'),
            'role' => 'network_admin',
        ]);

        Admin::firstOrCreate(['email' => 'webshop@wh40k.test'], [
            'name' => 'Webshop Admin',
            'password' => Hash::make('password'),
            'role' => 'webshop_admin',
        ]);

        Admin::firstOrCreate(['email' => 'db@wh40k.test'], [
            'name' => 'DB Admin',
            'password' => Hash::make('password'),
            'role' => 'db_admin',
        ]);

        $this->call(ShopSeeder::class);
    }
}
