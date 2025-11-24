<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overtime_clocks', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('overtime_request_id');

            $table->dateTime('clock_in')->nullable();
            $table->dateTime('clock_out')->nullable();
            $table->string('total_time_taken')->nullable(); // or integer (minutes)

            $table->timestamps();

            // FK
            $table->foreign('overtime_request_id')
                ->references('id')
                ->on('overtime_requests')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overtime_clocks');
    }
};
