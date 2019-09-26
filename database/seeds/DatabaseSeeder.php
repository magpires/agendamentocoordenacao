<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'sobrenome' => 'Admin',
            'email' => 'admin@admin.com',
            'telefone' => '(00) 00000-0000',
            'tipo' => 'Secretario',
            'password' => Hash::make('admin'),
        ]);
    }
}
