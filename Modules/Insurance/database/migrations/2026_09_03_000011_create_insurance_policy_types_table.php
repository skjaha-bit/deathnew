<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_policy_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurance_policy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('insurance_type_id')->constrained()->cascadeOnDelete();
            $table->unique(['insurance_policy_id', 'insurance_type_id'], 'policy_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_policy_types');
    }
};
