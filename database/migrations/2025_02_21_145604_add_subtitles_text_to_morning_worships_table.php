<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubtitlesTextToMorningWorshipsTable extends Migration
{
    public function up()
    {
        Schema::table('morning_worships', function (Blueprint $table) {
            $table->text('subtitles_text')->nullable()->after('subtitles');
        });
    }

    public function down()
    {
        Schema::table('morning_worships', function (Blueprint $table) {
            $table->dropColumn('subtitles_text');
        });
    }
}
