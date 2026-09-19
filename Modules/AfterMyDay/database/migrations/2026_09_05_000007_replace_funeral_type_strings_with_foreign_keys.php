<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('funerals', function (Blueprint $table) {
            $table->dropColumn(['ceremony', 'arrangement', 'funeral_services']);

            $table->foreignId('ceremony_type_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->foreignId('arrangement_type_id')->nullable()->after('ceremony_type_id')->constrained()->nullOnDelete();
            $table->foreignId('funeral_service_type_id')->nullable()->after('other_ceremony_options')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('funerals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ceremony_type_id');
            $table->dropConstrainedForeignId('arrangement_type_id');
            $table->dropConstrainedForeignId('funeral_service_type_id');

            $table->string('ceremony')->nullable();
            $table->string('arrangement')->nullable();
            $table->string('funeral_services')->nullable();
        });
    }
};
