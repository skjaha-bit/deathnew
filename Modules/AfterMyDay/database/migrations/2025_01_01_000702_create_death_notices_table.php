<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('death_notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('preference')->nullable();
            $table->string('obituary_media_preference')->nullable();
            $table->string('symbol')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('introduction')->nullable();
            $table->string('job_title')->nullable();
            $table->string('home_address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('body_text')->nullable();
            $table->string('alignment')->default('left');
            $table->string('layout')->default('single');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('death_notices');
    }
};
