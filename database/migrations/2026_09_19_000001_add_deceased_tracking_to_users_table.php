<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('deceased_at')->nullable()->after('status');
            $table->foreignId('deceased_confirmed_by')->nullable()->after('deceased_at')
                ->constrained('users')->nullOnDelete();
            $table->string('deceased_source')->nullable()->after('deceased_confirmed_by');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('deceased_confirmed_by');
            $table->dropColumn(['deceased_at', 'deceased_source']);
        });
    }
};
