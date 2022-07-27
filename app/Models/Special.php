<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Special extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'special_id',
        'date',
    ];

    public function user(){

        return $this->belongsToMany(User::class);

    }


}
