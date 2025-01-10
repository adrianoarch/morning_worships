<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_watched_worships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('morning_worship_id')->constrained()->onDelete('cascade');
            $table->timestamp('watched_at');
            // Campo opcional para notas/comentários do usuário
            $table->text('notes')->nullable();
            $table->timestamps();

            // Garante que um usuário não possa marcar o mesmo vídeo mais de uma vez
            $table->unique(['user_id', 'morning_worship_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_watched_worships');
    }
};
