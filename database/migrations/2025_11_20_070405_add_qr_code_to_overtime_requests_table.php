<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

     
  public function up(): void
{
    Schema::table('overtime_requests', function (Blueprint $table) {
        $table->string('qr_code')->nullable()->after('work_description');
    });
}

public function down(): void
{
    Schema::table('overtime_requests', function (Blueprint $table) {
        $table->dropColumn('qr_code');
    });
}

};
