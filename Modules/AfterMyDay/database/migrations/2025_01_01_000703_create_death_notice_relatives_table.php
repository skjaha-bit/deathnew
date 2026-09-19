<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('death_notice_relatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('death_notice_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('column_position')->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('death_notice_relatives');
    }
};
