<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('overtime_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->foreignId('branch_id')
                ->constrained('branch')
                ->onDelete('cascade');

            $table->foreignId('department_id')
                ->constrained('departments')
                ->onDelete('cascade');

            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('reason')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamp('clocked_in_at')->nullable();

            $table->string('status')->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });

    }
};
