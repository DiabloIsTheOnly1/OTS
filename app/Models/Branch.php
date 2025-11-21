<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branch'; // <-- FIXED

    protected $fillable = ['name'];

    public function overtimeRequests()
    {
        return $this->hasMany(OvertimeRequest::class);
    }
}
