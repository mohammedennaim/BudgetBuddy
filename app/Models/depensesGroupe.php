<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepensesGroupe extends Model
{
    use HasFactory;
    protected $table = 'depenses_groupe';

    protected $fillable = [
        'name',
        'devise_id',
    ];

    public function members()
    {
        return $this->belongsToMany(User::class, 'members');
    }

    public function devise()
    {
        return $this->belongsTo(Devise::class);
    }
}
