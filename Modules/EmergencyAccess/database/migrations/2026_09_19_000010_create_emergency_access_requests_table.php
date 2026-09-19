<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_access_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('status')->default('pending'); // pending|approved|denied|expired|revoked
            $table->text('reason');
            $table->text('decision_reason')->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('decided_at')->nullable();
            $table->timestamp('access_expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('request_ip', 45)->nullable();
            $table->string('request_user_agent')->nullable();
            $table->string('decision_ip', 45)->nullable();
            $table->string('decision_user_agent')->nullable();
            $table->timestamps();

            $table->index(['target_user_id', 'status']);
            $table->index(['requested_by', 'status']);
        });

        // Self-approval must be structurally impossible, not just discouraged
        // in the UI - enforce it at the database level as well as in the
        // application (VaultAccessService / controllers).
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE emergency_access_requests ADD CONSTRAINT chk_requester_not_approver CHECK (approved_by IS NULL OR approved_by <> requested_by)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_access_requests');
    }
};
