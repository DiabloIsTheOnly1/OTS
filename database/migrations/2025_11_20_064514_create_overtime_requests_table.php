<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
{
    Schema::create('overtime_requests', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('position')->nullable();
        $table->foreignId('branch_id')->constrained()->onDelete('cascade');
        $table->foreignId('department_id')->constrained()->onDelete('cascade');
        $table->date('date');
        $table->text('work_description');
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->timestamps();
        $table->timestamp('clock_in_time')->nullable();
        $table->timestamp('clock_out_time')->nullable();
        $table->string('qr_code')->nullable()->unique();
    });
}

};
