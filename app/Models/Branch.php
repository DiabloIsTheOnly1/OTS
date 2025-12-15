<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branch'; 

    protected $fillable = ['name'];

    public function overtimeRequests()
    {
        return $this->hasMany(OvertimeRequest::class);
    }

    /** Branch → Staff */
    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class, 'branch_id');
    }

    /** Branch ↔ Users (pivot: branch_user) */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'branch_user');
    }
}
