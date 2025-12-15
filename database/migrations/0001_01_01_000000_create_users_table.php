<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. DEPARTMENTS
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('department_name');
            $table->timestamps();
        });

        // 2. BRANCH
        Schema::create('branch', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('access_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            // Permission flags
            $table->boolean('access_level')->default(0);
            $table->boolean('user')->default(0);
            $table->boolean('branch_settings')->default(0);
            $table->boolean('department_settings')->default(0);
            $table->boolean('staff_settings')->default(0);
            $table->boolean('manage_request')->default(0);
            $table->boolean('hod_approval')->default(0);
            $table->boolean('hq_approval')->default(0);

            $table->timestamps();
        });

        // 3. USERS (department_id now works)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique('username');
            $table->string('password');

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->boolean('access_all_departments')->default(false);

            $table->foreignId('access_level_id')
                ->nullable()
                ->constrained('access_levels')
                ->nullOnDelete();

            $table->rememberToken();
            $table->timestamps();
        });

        // 4. USER ↔ BRANCH Pivot
        Schema::create('branch_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branch')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 5. PASSWORD RESET TOKENS
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('staff_name');
            $table->string('position');

            // Relationships
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('department_id');

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('branch_id')->references('id')->on('branch')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });

        // 6. SESSIONS
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_user');
        Schema::dropIfExists('branch');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('access_levels');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('staff');
    }
};
