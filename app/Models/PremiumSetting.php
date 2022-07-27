<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PremiumSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'instagram_url',
        'facebook_url',
        'twitter_url',
        'custom_link',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
