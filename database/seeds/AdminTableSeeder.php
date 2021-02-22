<?php

use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admins = [
    		[
                'name' => 'Admin',
                'email' => "admin@dessertden.com",
                'password' => Hash::make('admin@123'),
                'permissions' => serialize(getdistributorsPermissions('distributors')),
                'type'=>'Admin',
                'active' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
    	];

        DB::table('admins')->insert($admins);
    }
}
