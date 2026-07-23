<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentIdToReCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('re_categories', 'parent_id')) {
            Schema::table('re_categories', function (Blueprint $table) {
                $table->foreignId('parent_id')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('re_categories', function (Blueprint $table) {
            $table->dropColumn('parent_id');
        });
    }
}
