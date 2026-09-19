<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_commercial_bank_id')->constrained()->cascadeOnDelete();
            $table->string('account_type')->nullable();
            $table->string('account_number')->nullable();
            $table->string('swift_iban')->nullable();
            $table->boolean('is_joint')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_bank_accounts');
    }
};
