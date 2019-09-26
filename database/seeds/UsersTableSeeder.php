<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
