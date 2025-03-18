<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devise extends Model
{
    use HasFactory;
    protected $table = 'devises';

    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * Get the depensesGroupe that owns the devise.
     */
    public function depensesGroupe()
    {
        return $this->hasMany(DepensesGroupe::class);
    }

    /**
     * Get the members that owns the devise.
     */
    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
