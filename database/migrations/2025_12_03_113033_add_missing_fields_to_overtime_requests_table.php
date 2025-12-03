<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            // Add only if not exists
            if (!Schema::hasColumn('overtime_requests', 'reg_no')) {
                $table->string('reg_no')->nullable()->after('position');
            }

            if (!Schema::hasColumn('overtime_requests', 'start_time')) {
                $table->time('start_time')->nullable()->after('date');
            }

            if (!Schema::hasColumn('overtime_requests', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }

            if (!Schema::hasColumn('overtime_requests', 'total_hours')) {
                $table->decimal('total_hours', 5, 2)->nullable()->after('end_time'); // e.g. 8.50
            }
        });
    }

    public function down(): void
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            $table->dropColumn(['reg_no', 'start_time', 'end_time', 'total_hours']);
        });
    }
};