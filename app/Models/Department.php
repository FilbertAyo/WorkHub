<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'status'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function petties()
    {
        return $this->hasMany(Petty::class);
    }
}
