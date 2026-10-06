<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_commercial_banks', function (Blueprint $table) {
            $table->string('swift', 11)->nullable()->after('bank_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_commercial_banks', function (Blueprint $table) {
            $table->dropColumn('swift');
        });
    }
};
