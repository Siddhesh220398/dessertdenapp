<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorRoleModel extends Model
{
    protected $guarded = ['id'];

    public function distributorsection() {
        return $this->belongsTo('App\Models\DistributorSectionModel');
    }
}
