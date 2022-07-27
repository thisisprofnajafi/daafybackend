<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_path',
        'passport_path',
    ];


    public function user(){

        return $this->belongsTo(User::class);

    }
}
