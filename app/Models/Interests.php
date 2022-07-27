<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interests extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'age',
      'gender',
      'country',
      'city',
      'height',
      'body_type',
      'drink',
      'smoke',
      'employment_status',
      'living_status',
      'relation_status',
      'education_status',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
