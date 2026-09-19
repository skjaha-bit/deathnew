<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_contents', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('module_key')->nullable();
            $table->json('title');
            $table->json('body')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'module_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_contents');
    }
};
