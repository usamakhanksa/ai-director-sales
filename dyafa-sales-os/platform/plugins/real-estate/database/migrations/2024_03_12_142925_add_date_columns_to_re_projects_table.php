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
        Schema::table('re_projects', function (Blueprint $table) {
            if (!Schema::hasColumn('re_projects', 'date_finish')) {
                Schema::table('re_projects', function (Blueprint $table) {
                    $table->date('date_finish')->nullable();
                });
            }
            if (!Schema::hasColumn('re_projects', 'date_sell')) {
                Schema::table('re_projects', function (Blueprint $table) {
                    $table->date('date_sell')->nullable();
                });
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('re_projects', function (Blueprint $table) {
            if (Schema::hasColumn('re_projects', 'date_finish')) {
                Schema::table('re_projects', function (Blueprint $table) {
                    $table->dropColumn('date_finish');
                });
            }
            if (Schema::hasColumn('re_projects', 'date_sell')) {
                Schema::table('re_projects', function (Blueprint $table) {
                    $table->dropColumn('date_sell');
                });
            }
        });
    }
};
