<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('overtime_requests', function (Blueprint $table) {
            $table->id();

            // Replace name/position with staff_id
            $table->unsignedBigInteger('staff_id');

            // Foreign keys
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('department_id');

            $table->date('date');

            // $table->time('start_time')->nullable();
            // $table->time('end_time')->nullable();

            $table->decimal('total_hours', 8, 2)->nullable()->after('end_time');
            $table->decimal('approved_hours', 8, 2)->nullable()->after('total_hours');
            $table->text('type_of_work')->nullable();
            $table->text('reg_no')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
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
