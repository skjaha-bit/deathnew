<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * Encrypts the remaining personal free-text columns of user-owned tables at
 * rest (the models carry matching `encrypted` casts).
 *
 * Deliberately NOT encrypted: ids/foreign keys, booleans, dates, numbers,
 * status/enum columns used in queries, file paths, and columns used for
 * login, uniqueness or admin search (users.name/email/phone/kennitala,
 * user_emails, user_phones, relatives.name/kennitala) - an `encrypted` value
 * is randomly salted, so it can't be matched with WHERE/LIKE/UNIQUE.
 *
 * Each entry maps column => its original definition when it was a VARCHAR
 * that must be widened (encrypted payloads run 150-350+ chars), or null when
 * it is already TEXT. Backfill reuses vault:encrypt-existing, which is
 * idempotent, so already-encrypted values are left alone.
 */
return new class extends Migration
{
    private const COLUMNS = [
        'users' => [
            'address' => 'VARCHAR(255) NULL',
            'house_number' => 'VARCHAR(255) NULL',
            'city' => 'VARCHAR(255) NULL',
            'postal_code' => 'VARCHAR(255) NULL',
            'marital_status' => 'VARCHAR(255) NULL',
            'gender' => 'VARCHAR(255) NULL',
            'promo_code' => 'VARCHAR(255) NULL',
        ],
        'funerals' => [
            'other_ceremony_options' => null,
            'other_thing' => 'VARCHAR(255) NULL',
            'location' => null,
            'laying_place' => null,
            'clothing_request' => null,
            'event_manager' => 'VARCHAR(255) NULL',
            'guests' => null,
            'pallbearer_request' => null,
            'coffin_design' => null,
            'coffin_decorations_detail' => null,
            'charity_case' => 'VARCHAR(255) NULL',
            'another_thing' => null,
            'notes' => null,
        ],
        'funeral_music' => [
            'description' => 'VARCHAR(255) NOT NULL',
        ],
        'death_notices' => [
            'introduction' => 'VARCHAR(255) NULL',
            'job_title' => 'VARCHAR(255) NULL',
            'home_address' => 'VARCHAR(255) NULL',
            'body_text' => null,
        ],
        'death_notice_relatives' => [
            'name' => 'VARCHAR(255) NOT NULL',
        ],
        'memories' => [
            'name' => 'VARCHAR(255) NULL',
        ],
        'memory_documents' => [
            'original_name' => 'VARCHAR(255) NULL',
        ],
        'farewell_drinks' => [
            'location' => null,
            'guests' => null,
            'catering' => null,
            'arrangement' => null,
            'comments' => null,
            'other_wishes' => null,
        ],
        'lifetime_achievements' => [
            'full_name' => 'VARCHAR(255) NULL',
        ],
        'lifetime_achievement_educations' => [
            'school_or_university' => 'VARCHAR(255) NULL',
            'degree' => 'VARCHAR(255) NULL',
            'year' => 'VARCHAR(4) NULL',
        ],
        'lifetime_achievement_spouses' => [
            'name' => 'VARCHAR(255) NOT NULL',
        ],
        'lifetime_achievement_children' => [
            'name' => 'VARCHAR(255) NOT NULL',
            'spouse_name' => 'VARCHAR(255) NULL',
        ],
        'lifetime_achievement_grandchildren' => [
            'name' => 'VARCHAR(255) NOT NULL',
            'parent_name' => 'VARCHAR(255) NULL',
        ],
        'user_commercial_banks' => [
            'swift' => 'VARCHAR(11) NULL',
        ],
        'inheritance_matter_agents' => [
            'name' => 'VARCHAR(255) NOT NULL',
            'email' => 'VARCHAR(255) NULL',
        ],
        'insurance_policies' => [
            'other_type_text' => 'VARCHAR(255) NULL',
        ],
        'insurance_documents' => [
            'original_name' => 'VARCHAR(255) NOT NULL',
        ],
        'pension_entries' => [
            'custom_fund_name' => 'VARCHAR(255) NULL',
            'from_year' => 'VARCHAR(255) NULL',
            'to_year' => 'VARCHAR(255) NULL',
            'estimated_credit' => 'VARCHAR(255) NULL',
        ],
        'properties' => [
            'name' => 'VARCHAR(255) NULL',
            'location' => 'VARCHAR(255) NULL',
            'asset_registration_number' => 'VARCHAR(255) NULL',
            'co_owner_name' => 'VARCHAR(255) NULL',
            'co_owner_contact' => 'VARCHAR(255) NULL',
            'deeds_location' => 'VARCHAR(255) NULL',
            'lender' => 'VARCHAR(255) NULL',
            'loan_number' => 'VARCHAR(255) NULL',
            'policy_number' => 'VARCHAR(255) NULL',
            'notes_text' => null,
        ],
        'relatives' => [
            'email' => 'VARCHAR(255) NULL',
            'phone' => 'VARCHAR(255) NULL',
            'relation' => 'VARCHAR(255) NULL',
            'estate_agent_notes' => null,
            'personal_affairs_notes' => null,
        ],
        'relative_emails' => [
            'email' => 'VARCHAR(255) NOT NULL',
        ],
        'relative_phones' => [
            'phone_code' => 'VARCHAR(8) NOT NULL',
            'phone_number' => 'VARCHAR(20) NOT NULL',
        ],
        'workplaces' => [
            'company_name' => 'VARCHAR(255) NOT NULL',
            'contact_name' => 'VARCHAR(255) NOT NULL',
            'contact_title' => 'VARCHAR(255) NULL',
            'department' => 'VARCHAR(255) NULL',
            'message_to_contact' => null,
            'additional_note' => null,
        ],
        'workplace_emails' => [
            'email' => 'VARCHAR(255) NOT NULL',
        ],
        'workplace_phones' => [
            'phone_code' => 'VARCHAR(8) NOT NULL',
            'phone_number' => 'VARCHAR(20) NOT NULL',
        ],
        'pets' => [
            'name' => 'VARCHAR(255) NOT NULL',
            'animal_type' => 'VARCHAR(255) NOT NULL',
            'breed' => 'VARCHAR(255) NULL',
            'identification_number' => 'VARCHAR(255) NULL',
            'vet_clinic_name' => 'VARCHAR(255) NULL',
            'vet_contact_person' => 'VARCHAR(255) NULL',
            'vet_contact_number' => 'VARCHAR(255) NULL',
            'medical_conditions' => null,
            'dietary_needs' => null,
            'policy_number' => 'VARCHAR(255) NULL',
            'final_wishes' => null,
        ],
        'death_reports' => [
            'reporter_name' => 'VARCHAR(255) NOT NULL',
            'reporter_email' => 'VARCHAR(255) NULL',
            'reporter_phone' => 'VARCHAR(255) NULL',
            'relationship' => 'VARCHAR(255) NULL',
            'notes' => null,
        ],
        'user_trade_unions' => [
            'union_name' => 'VARCHAR(255) NULL',
            'from_month' => 'VARCHAR(255) NULL',
            'from_year' => 'VARCHAR(255) NULL',
            'to_month' => 'VARCHAR(255) NULL',
            'to_year' => 'VARCHAR(255) NULL',
        ],
        'user_subscriptions' => [
            'name' => 'VARCHAR(255) NOT NULL',
            'associated_email' => 'VARCHAR(255) NULL',
        ],
    ];

    public function up(): void
    {
        foreach (self::COLUMNS as $table => $columns) {
            foreach ($columns as $column => $original) {
                if ($original !== null) {
                    $nullability = str_contains($original, 'NOT NULL') ? 'NOT NULL' : 'NULL';
                    DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` TEXT {$nullability}");
                }
            }

            Artisan::call('vault:encrypt-existing', [
                'table' => $table,
                'columns' => implode(',', array_keys($columns)),
            ]);
        }
    }

    public function down(): void
    {
        foreach (self::COLUMNS as $table => $columns) {
            $names = array_keys($columns);

            DB::table($table)->orderBy('id')->chunkById(200, function ($rows) use ($table, $names) {
                foreach ($rows as $row) {
                    $updates = [];

                    foreach ($names as $column) {
                        $value = $row->{$column} ?? null;

                        if ($value === null || $value === '') {
                            continue;
                        }

                        try {
                            $updates[$column] = Crypt::decryptString($value);
                        } catch (DecryptException) {
                            // Already plaintext.
                        }
                    }

                    if ($updates) {
                        DB::table($table)->where('id', $row->id)->update($updates);
                    }
                }
            });

            foreach ($columns as $column => $original) {
                if ($original !== null) {
                    DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` {$original}");
                }
            }
        }
    }
};
