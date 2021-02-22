<?php

use Illuminate\Database\Seeder;

class DistributorSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sections = [
            [
                'name' => 'Dashboard',
                'image' => 'fa fa-home',
                'sequence' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            ];
        foreach($sections as $section){
            DB::table('distributor_section_models')->insert($section);
        }
    }
}
