<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_funds', function (Blueprint $table) {
            $table->dropColumn('label');
            $table->foreignId('fund_type_id')->nullable()->after('user_commercial_bank_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user_funds', function (Blueprint $table) {
            $table->dropForeign(['fund_type_id']);
            $table->dropColumn('fund_type_id');
            $table->string('label')->nullable();
        });
    }
};
