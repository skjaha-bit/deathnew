<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_commercial_banks', function (Blueprint $table) {
            $table->dropColumn('bank_loan');
            $table->foreignId('loan_type_id')->nullable()->after('bank_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user_commercial_banks', function (Blueprint $table) {
            $table->dropForeign(['loan_type_id']);
            $table->dropColumn('loan_type_id');
            $table->string('bank_loan')->nullable();
        });
    }
};
