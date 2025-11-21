<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'password',
        'department_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * A user belongs to one department
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * A user can belong to many branches
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_user');
    }
}
