<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Premium extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'started',
        'ends',
        'type',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
