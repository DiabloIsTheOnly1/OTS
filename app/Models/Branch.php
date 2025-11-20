<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branch';

    protected $fillable = [
        'branch_name',
    ];

    /**
     * Branch has many users
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'branch_user');
    }
}
