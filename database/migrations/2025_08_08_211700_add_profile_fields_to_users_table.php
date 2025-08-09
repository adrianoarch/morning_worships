<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('receives_email_notification');
            $table->string('timezone')->nullable()->default(config('app.timezone'))->after('phone');
            $table->string('language', 10)->nullable()->default(config('app.locale'))->after('timezone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'timezone', 'language']);
        });
    }
};
