<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('overtime_clocks', function (Blueprint $table) {
            $table->id();

            // Foreign key to overtime_requests table
            $table->foreignId('overtime_request_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Event type: clock-in or clock-out
            $table->enum('type', ['in', 'out']);

            // Actual timestamp of scan
            $table->timestamp('scanned_at')->nullable();

            // Additional metadata
            $table->string('scanned_by')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('device')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtime_clocks');
    }
};
