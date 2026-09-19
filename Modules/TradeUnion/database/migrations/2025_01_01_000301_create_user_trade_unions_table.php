<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_trade_unions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trade_union_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_month')->nullable();
            $table->string('from_year')->nullable();
            $table->string('to_month')->nullable();
            $table->string('to_year')->nullable();
            $table->boolean('still_in_payment')->default(false);
            $table->string('department_contact')->nullable();
            $table->text('rights_text')->nullable();
            $table->text('notes_text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_trade_unions');
    }
};
