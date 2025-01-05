<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('morning_worships', function (Blueprint $table) {
            $table->id();
            $table->string('guid')->unique();
            $table->string('title');
            $table->text('description');
            $table->dateTime('first_published');
            $table->integer('duration');
            $table->string('duration_formatted');
            $table->string('video_url')->nullable();
            $table->string('image_url')->nullable();
            $table->json('subtitles')->nullable();
            $table->dateTime('watched_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('morning_worships');
    }
};
