<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
        	[
        		'first_name' => 'dessertden',
        		'last_name' => 'den',
        		'email'=>'user@dessertden.com',
        		'mobile_no'=>'8849760310',
        		'password' => Hash::make('user@123'),
        		'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
        	],
        ];

        DB::table('users')->insert($users);
    }
}
