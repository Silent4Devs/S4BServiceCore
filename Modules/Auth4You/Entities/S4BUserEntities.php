<?php

namespace Modules\Auth4You\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class S4BUserEntities extends Authenticatable
{
    use HasFactory;

    protected $connection = 'auth_db';
    protected $fillable = ['name', 'email', 'password'];
}
