<?php

use Illuminate\Database\Seeder;

class DistributorRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = array(
            [
                'distributor_section_model_id' => 'Dashboard',
                'title' => 'Dashboard',
                'route' => 'distributor.dashboard.index',
                'image' => 'fa fa-home',
                'sequence' => 1,
                'permissions' => 'access',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
          );
        foreach ($menus as $role) {
            $section_id = DB::table('distributor_section_models')->where('name', $role['distributor_section_model_id'])->value('id');

            $role['distributor_section_model_id'] = ($section_id > 0 ? $section_id : 1);

            DB::table('distributor_role_models')->insert($role);
        }
    }
}
