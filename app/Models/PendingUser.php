<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PendingUser extends Model
{
    use HasFactory ,Notifiable ;

    protected $fillable = [
        'email',
        'verification_code',
        'verification_code_expires_at',
        'email_verified_at'
    ];

}
