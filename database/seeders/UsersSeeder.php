<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            ['name' => 'Admin','email'=> 'Admin@admin.com','password'=> Hash::make('administrator'),'role' => 'Admin'],
            ['name' => 'Lawyer','email'=> 'Lawyer@admin.com','password'=> Hash::make('lawyer'),'role' => 'lawyer']
        ];

        foreach($users as $user)
        {
            User::updateOrCreate(['name' => $user['name'],'email'=> $user['email'],'password'=> $user['password'],'role' => $user['role']]);
        }
    }
}
