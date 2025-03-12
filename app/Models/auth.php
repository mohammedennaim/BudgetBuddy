<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class auth extends Model
{
    protected $table = 'auth';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['email', 'password'];
}
