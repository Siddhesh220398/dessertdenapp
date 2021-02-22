<?php

namespace App;

use App\Notifications\FranchiseResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Franchise extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $guarded = ['id'];

    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function city() {
        return $this->belongsTo('\App\Models\City');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new FranchiseResetPassword($token));
    }
}
