<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_worship', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('morning_worship_id')->constrained()->onDelete('cascade');
            $table->timestamp('watched_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_worship');
    }
};
