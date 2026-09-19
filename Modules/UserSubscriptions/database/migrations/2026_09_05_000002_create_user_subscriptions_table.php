<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('associated_email')->nullable();
            $table->string('billing_cycle')->default('monthly');
            $table->decimal('amount', 15, 2)->nullable();
            $table->boolean('power_of_attorney_termination')->default(false);
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
