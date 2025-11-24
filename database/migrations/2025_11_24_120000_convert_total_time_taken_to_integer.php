<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        // If column doesn't exist, nothing to do
        if (!Schema::hasColumn('overtime_clocks', 'total_time_taken')) {
            return;
        }

        // Add temporary integer column
        Schema::table('overtime_clocks', function (Blueprint $table) {
            $table->integer('total_time_taken_int')->nullable()->after('clock_out');
        });

        // Migrate values from old column to new integer column
        $rows = DB::table('overtime_clocks')->select('id', 'total_time_taken')->get();

        foreach ($rows as $row) {
            $val = $row->total_time_taken;
            $seconds = null;

            if ($val === null || $val === '') {
                $seconds = null;
            } elseif (is_numeric($val)) {
                $seconds = (int) $val;
            } elseif (strpos($val, ':') !== false) {
                // Parse formats like HH:MM:SS or H:MM
                $parts = array_map('intval', explode(':', $val));
                if (count($parts) === 3) {
                    $seconds = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
                } elseif (count($parts) === 2) {
                    // Treat as H:MM
                    $seconds = $parts[0] * 3600 + $parts[1] * 60;
                } else {
                    $seconds = (int) $parts[0];
                }
            } else {
                // Fallback: extract integer portion (e.g. "3600s" or mixed text)
                $filtered = filter_var($val, FILTER_SANITIZE_NUMBER_INT);
                if ($filtered !== '') {
                    $seconds = (int) $filtered;
                } else {
                    // Last resort: try Carbon parsing as time and compute seconds since midnight
                    try {
                        $dt = Carbon::parse($val);
                        $seconds = $dt->diffInSeconds(Carbon::createFromTime(0, 0, 0));
                    } catch (\Throwable $e) {
                        $seconds = null;
                    }
                }
            }

            DB::table('overtime_clocks')->where('id', $row->id)->update([
                'total_time_taken_int' => $seconds,
            ]);
        }

        // Drop old column and rename temporary one
        Schema::table('overtime_clocks', function (Blueprint $table) {
            // dropColumn may require DBAL for some drivers; this is the safest approach
            $table->dropColumn('total_time_taken');
        });

        Schema::table('overtime_clocks', function (Blueprint $table) {
            $table->renameColumn('total_time_taken_int', 'total_time_taken');
        });
    }

    public function down(): void
    {
        // If integer column doesn't exist, nothing to do
        if (!Schema::hasColumn('overtime_clocks', 'total_time_taken')) {
            return;
        }

        // Add temporary string column
        Schema::table('overtime_clocks', function (Blueprint $table) {
            $table->string('total_time_taken_str')->nullable()->after('clock_out');
        });

        // Convert seconds back to HH:MM:SS strings
        $rows = DB::table('overtime_clocks')->select('id', 'total_time_taken')->get();
        foreach ($rows as $row) {
            $seconds = $row->total_time_taken;
            if ($seconds === null) {
                $str = null;
            } else {
                $s = (int) $seconds;
                $h = floor($s / 3600);
                $m = floor(($s % 3600) / 60);
                $sec = $s % 60;
                $str = sprintf('%02d:%02d:%02d', $h, $m, $sec);
            }

            DB::table('overtime_clocks')->where('id', $row->id)->update([
                'total_time_taken_str' => $str,
            ]);
        }

        Schema::table('overtime_clocks', function (Blueprint $table) {
            $table->dropColumn('total_time_taken');
        });

        Schema::table('overtime_clocks', function (Blueprint $table) {
            $table->renameColumn('total_time_taken_str', 'total_time_taken');
        });
    }
};
