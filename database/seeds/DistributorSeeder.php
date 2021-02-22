<?php

use Illuminate\Database\Seeder;

class DistributorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $distributor = [
            [
                'name' => 'Distributor',
                'email' => "distributor@dessertden.com",
                'password' => Hash::make('distributor@123'),
                'permissions' => serialize(getdistributorsPermissions('distributor')),
                'active' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
        ];

        DB::table('distributors')->insert($distributor);
    }
}
