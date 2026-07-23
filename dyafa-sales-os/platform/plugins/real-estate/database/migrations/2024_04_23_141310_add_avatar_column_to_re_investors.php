<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Botble\RealEstate\Models\Investor;
use Botble\Media\Models\MediaFile;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::table('re_investors', function (Blueprint $table) {
            $table->string('avatar')->nullable();
        });
        $investors = Investor::get();
        foreach ($investors as $investor) {
            $investor->avatar = $investor->avatar_id ? MediaFile::find($investor->avatar_id)->url : null;
            $investor->save();
        }
        Schema::table('re_investors', function (Blueprint $table) {
            $table->dropColumn('avatar_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('re_investors', function (Blueprint $table) {
            $table->integer('avatar_id')->unsigned()->nullable();
        });

        $investors = Investor::get();
        foreach ($investors as $investor) {
            $investors->avatar_id = MediaFile::query()
                ->where('url', $investor->avatar)
                ->value('id');
        }

        Schema::table('re_investors', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });
    }
};
