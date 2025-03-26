<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    protected $fillable = ['name','members','devise'];

    public function members()
    {
        return $this->belongsToMany(User::class, 'groupe_user');
    }

    public function expenses()
    {
        return $this->belongsToMany(Expense::class, 'expense_groupe');
    }
}
