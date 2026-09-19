<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Basic information
            $table->string('name');
            $table->string('animal_type');
            $table->string('breed')->nullable();
            $table->unsignedSmallInteger('birth_year')->nullable();
            $table->unsignedTinyInteger('birth_month')->nullable();
            $table->string('identification_number')->nullable();

            // Care & medical details
            $table->string('vet_clinic_name')->nullable();
            $table->string('vet_contact_person')->nullable();
            $table->string('vet_contact_number')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->text('dietary_needs')->nullable();
            $table->foreignId('insurance_company_id')->nullable()->constrained('insurance_companies')->nullOnDelete();
            $table->string('policy_number')->nullable();

            // Future care & final wishes
            $table->foreignId('designated_guardian_id')->nullable()->constrained('guardians')->nullOnDelete();
            $table->boolean('guardian_agreed')->default(false);
            $table->foreignId('alternative_guardian_id')->nullable()->constrained('guardians')->nullOnDelete();
            $table->text('final_wishes')->nullable();

            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
