<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Widen the two Tier-2 vault columns that are still varchar(255) - the rest
 * are already TEXT. See 2026_09_19_000002_widen_vault_columns_for_encryption
 * for why raw SQL is used instead of Blueprint::change().
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE user_trade_unions MODIFY department_contact TEXT NULL');
        DB::statement('ALTER TABLE lifetime_achievements MODIFY place_of_birth TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE user_trade_unions MODIFY department_contact VARCHAR(255) NULL');
        DB::statement('ALTER TABLE lifetime_achievements MODIFY place_of_birth VARCHAR(255) NULL');
    }
};
