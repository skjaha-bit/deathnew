<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relative_phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relative_id')->constrained()->cascadeOnDelete();
            $table->string('phone_code', 8);
            $table->string('phone_number', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relative_phones');
    }
};
