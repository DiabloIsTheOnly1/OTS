<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('overtime_requests', function (Blueprint $table) {
        $table->decimal('total_hours', 8, 2)->nullable()->after('date');
    });
}

public function down()
{
    Schema::table('overtime_requests', function (Blueprint $table) {
        $table->dropColumn('total_hours');
    });
}
};
