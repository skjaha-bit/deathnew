<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_trade_unions', function (Blueprint $table) {
            $table->string('union_name')->nullable()->after('trade_union_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_trade_unions', function (Blueprint $table) {
            $table->dropColumn('union_name');
        });
    }
};
