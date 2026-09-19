<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farewell_drinks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('preference')->nullable();
            $table->text('location')->nullable();
            $table->text('guests')->nullable();
            $table->text('catering')->nullable();
            $table->text('arrangement')->nullable();
            $table->text('comments')->nullable();
            $table->text('other_wishes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farewell_drinks');
    }
};
