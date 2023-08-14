<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch', 'district', 'phone',
        // List other fields specific to the registered members
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
