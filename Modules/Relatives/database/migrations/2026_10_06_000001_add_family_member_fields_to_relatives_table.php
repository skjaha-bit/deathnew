<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('relatives', function (Blueprint $table) {
            $table->string('kennitala', 11)->nullable()->after('name');
            $table->boolean('read_access_after_death')->default(false)->after('relation');
            $table->text('estate_agent_notes')->nullable()->after('read_access_after_death');
            $table->text('personal_affairs_notes')->nullable()->after('estate_agent_notes');
        });
    }

    public function down(): void
    {
        Schema::table('relatives', function (Blueprint $table) {
            $table->dropColumn(['kennitala', 'read_access_after_death', 'estate_agent_notes', 'personal_affairs_notes']);
        });
    }
};
