<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $casts = [
    'clock_in' => 'datetime',
    'clock_out' => 'datetime',
];

    public function up(): void
    {

        Schema::table('overtime_clocks', function (Blueprint $table) {
            $table->timestamp('clock_in')->nullable()->after('overtime_request_id');
            $table->timestamp('clock_out')->nullable()->after('clock_in');
            $table->decimal('total_time_taken', 8, 2)->nullable()->after('clock_out');
        });
    }

    public function down(): void
    {
        Schema::table('overtime_clocks', function (Blueprint $table) {
            $table->dropColumn(['clock_in', 'clock_out', 'total_time_taken']);
        });
    }
};

