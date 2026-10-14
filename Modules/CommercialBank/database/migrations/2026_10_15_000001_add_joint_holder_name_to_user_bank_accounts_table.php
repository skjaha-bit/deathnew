<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_bank_accounts', function (Blueprint $table) {
            // TEXT: the value is stored encrypted (see UserBankAccount casts).
            $table->text('joint_holder_name')->nullable()->after('is_joint');
        });
    }

    public function down(): void
    {
        Schema::table('user_bank_accounts', function (Blueprint $table) {
            $table->dropColumn('joint_holder_name');
        });
    }
};
