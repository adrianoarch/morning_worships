<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddFulltextIndexToMorningWorshipsTable extends Migration
{
    public function up()
    {
        // Adicionando índice FULLTEXT
        DB::statement('ALTER TABLE morning_worships ADD FULLTEXT INDEX subtitles_text_index (subtitles_text)');
    }

    public function down()
    {
        // Removendo índice FULLTEXT
        DB::statement('ALTER TABLE morning_worships DROP INDEX subtitles_text_index');
    }
}
