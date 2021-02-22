<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorSectionModel extends Model
{

    protected $guarded = ['id'];

    public function distributorroles() {
        return $this->hasMany('App\Models\DistributorRoleModel')->where('active', '=', 1)->orderBy('sequence', 'asc');
    }
}
