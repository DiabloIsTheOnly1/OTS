<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Relationship: Branch has many Overtime Requests
    public function overtimeRequests()
    {
        return $this->hasMany(OvertimeRequest::class);
    }
}
