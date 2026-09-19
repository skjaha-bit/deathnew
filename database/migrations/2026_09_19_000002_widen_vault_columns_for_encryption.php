<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Widen Tier-1 vault columns to TEXT ahead of encrypting them: an
 * `encrypted` Eloquent cast's output (base64-wrapped JSON with IV+MAC)
 * commonly runs 150-350+ characters, too tight for the current varchar(255)
 * columns. Uses raw SQL rather than Blueprint::change() since this project
 * does not install doctrine/dbal. This migration only widens columns - it
 * does not touch data or add casts (see vault:encrypt-existing command and
 * the model changes that follow once existing plaintext has been backfilled).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE user_bank_accounts MODIFY account_number TEXT NULL');
        DB::statement('ALTER TABLE user_bank_accounts MODIFY swift_iban TEXT NULL');

        DB::statement('ALTER TABLE inheritance_matter_agents MODIFY social_security_number TEXT NULL');
        DB::statement('ALTER TABLE inheritance_matter_agents MODIFY address TEXT NULL');
        DB::statement('ALTER TABLE inheritance_matter_agents MODIFY phone TEXT NULL');

        DB::statement('ALTER TABLE inheritance_matters MODIFY will_location TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE user_bank_accounts MODIFY account_number VARCHAR(255) NULL');
        DB::statement('ALTER TABLE user_bank_accounts MODIFY swift_iban VARCHAR(255) NULL');

        DB::statement('ALTER TABLE inheritance_matter_agents MODIFY social_security_number VARCHAR(255) NULL');
        DB::statement('ALTER TABLE inheritance_matter_agents MODIFY address VARCHAR(255) NULL');
        DB::statement('ALTER TABLE inheritance_matter_agents MODIFY phone VARCHAR(255) NULL');

        DB::statement('ALTER TABLE inheritance_matters MODIFY will_location VARCHAR(255) NULL');
    }
};
