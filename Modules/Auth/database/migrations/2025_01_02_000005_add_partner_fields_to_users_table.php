<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('partner_id')->nullable()->after('country_id');
            $table->string('promo_code')->nullable()->after('partner_id');
            $table->string('account_type')->nullable()->after('promo_code');

            $table->index('partner_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['partner_id']);
            $table->dropColumn(['partner_id', 'promo_code', 'account_type']);
        });
    }
};
