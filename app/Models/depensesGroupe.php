<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class depensesGroupe extends Model
{
    use HasFactory;
    protected $table = 'depenses_groupe';

    protected $fillable = [
        'name',
        'user_id',
        'devise_id',
    ];

    
    /**
     * Get the user that owns the depensesGroupe.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the devise that owns the depensesGroupe.
     */
    public function devise()
    {
        return $this->belongsTo(Devise::class);
    } 
}
