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
            $table->string('position');

            // Foreign keys
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('department_id');

            $table->date('date');
            $table->text('reason')->nullable();

            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->unsignedBigInteger('approved_by')->nullable();

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('branch_id')->references('id')->on('branch')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overtime_requests');
    }
};
