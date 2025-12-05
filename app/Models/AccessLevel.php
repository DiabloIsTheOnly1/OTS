<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'access_level',
        'user',
        'branch_settings',
        'department_settings',
        'staff_settings',
        'manage_request',
        'hod_approval',
        'hq_approval',
    ];

    // Access Level has many users
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
