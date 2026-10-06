<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The same person may be a family member of many users, but only once per user.
        Schema::table('relatives', function (Blueprint $table) {
            $table->unique(['user_id', 'kennitala']);
        });
    }

    public function down(): void
    {
        Schema::table('relatives', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'kennitala']);
        });
    }
};
