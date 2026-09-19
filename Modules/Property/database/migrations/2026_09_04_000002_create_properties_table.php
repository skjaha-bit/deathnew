<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('location')->nullable();
            $table->string('asset_registration_number')->nullable();
            $table->string('ownership')->default('own_by_me');
            $table->string('co_owner_name')->nullable();
            $table->string('co_owner_contact')->nullable();
            $table->string('deeds_location')->nullable();
            $table->boolean('has_will')->default(false);

            // Loan information
            $table->string('lender')->nullable();
            $table->string('loan_number')->nullable();
            $table->decimal('loan_outstanding_balance', 15, 2)->nullable();
            $table->foreignId('loan_type_id')->nullable()->constrained('loan_types')->nullOnDelete();
            $table->foreignId('insurance_company_id')->nullable()->constrained('insurance_companies')->nullOnDelete();
            $table->string('policy_number')->nullable();
            $table->boolean('has_loan_protection')->default(false);

            $table->text('notes_text')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
