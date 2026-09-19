<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phone_code', 8);
            $table->string('phone_number', 20);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['phone_code', 'phone_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_phones');
    }
};
