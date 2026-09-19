<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('address')->nullable()->after('kennitala');
            $table->string('house_number')->nullable()->after('address');
            $table->string('city')->nullable()->after('house_number');
            $table->string('postal_code')->nullable()->after('city');
            $table->string('country')->nullable()->after('postal_code');
            $table->string('marital_status')->nullable()->after('country');
            $table->string('gender')->nullable()->after('marital_status');
            $table->boolean('newsletter_opt_in')->default(false)->after('gender');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'house_number',
                'city',
                'postal_code',
                'country',
                'marital_status',
                'gender',
                'newsletter_opt_in',
            ]);
        });
    }
};
