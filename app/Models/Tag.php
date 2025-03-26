<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expenses()
    {
        return $this->belongsToMany(Expense::class, 'expense_tag');
    }

    public function groupes()
    {
        return $this->belongsToMany(Groupe::class, 'groupes');
    }
}