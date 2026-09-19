<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funerals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('ceremony')->nullable();
            $table->string('arrangement')->nullable();
            $table->text('other_ceremony_options')->nullable();
            $table->string('funeral_services')->nullable();
            $table->string('other_thing')->nullable();
            $table->text('location')->nullable();
            $table->text('laying_place')->nullable();
            $table->text('clothing_request')->nullable();
            $table->string('event_manager')->nullable();
            $table->text('guests')->nullable();
            $table->text('pallbearer_request')->nullable();
            $table->text('coffin_design')->nullable();
            $table->boolean('coffin_decorations')->default(false);
            $table->text('coffin_decorations_detail')->nullable();
            $table->string('charity_case')->nullable();
            $table->text('another_thing')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funerals');
    }
};
