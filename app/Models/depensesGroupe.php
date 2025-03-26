<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepensesGroupe extends Model
{
    use HasFactory;
    protected $table = 'expense_groupe';
    protected $fillable = [
        'groupe_id',
        'expense_id',
    ];

    public function expenses()
    {
        return $this->belongsToMany(Expense::class, 'expenses');
    }

    public function groupes()
    {
        return $this->belongsToMany(Groupe::class, 'groupes');
    }
}
