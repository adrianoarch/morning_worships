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
        Schema::table('morning_worships', function (Blueprint $table) {
            $table->string('natural_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('morning_worships', function (Blueprint $table) {
            $table->dropColumn('natural_key');
        });
    }
};
