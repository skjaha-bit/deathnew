<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pension_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pension_fund_id')->nullable()->constrained()->nullOnDelete();
            $table->string('custom_fund_name')->nullable();
            $table->string('category');
            $table->string('from_year')->nullable();
            $table->string('to_year')->nullable();
            $table->boolean('still_in_payment')->default(false);
            $table->string('estimated_credit')->nullable();
            $table->text('special_rights_text')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pension_entries');
    }
};
