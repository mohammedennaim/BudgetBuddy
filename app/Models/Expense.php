<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['amountTotal', 'description', 'date', 'type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'expense_tag')
            ->withTimestamps();
    }

    public function groupes()
    {
        return $this->belongsToMany(Groupe::class, 'groupes');
    }

    public function splits()
    {
        return $this->hasMany(ExpenseSplit::class);
    }
}