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
//        $this->call(SectionTableSeeder::class);
//        $this->call(RoleTableSeeder::class);
//        $this->call(AdminTableSeeder::class);
//        $this->call(DistributorSectionSeeder::class);
//        $this->call(DistributorRoleSeeder::class);
        $this->call(DistributorSeeder::class);
        // $this->call(UserTableSeeder::class);
    }
}
