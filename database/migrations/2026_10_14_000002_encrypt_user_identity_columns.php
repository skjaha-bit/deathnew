<?php

use App\Support\BlindIndex;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Encrypts the identity columns (name, email, phone, kennitala) of users,
 * user_emails and user_phones.
 *
 * Encrypted values can't be matched by WHERE/UNIQUE, so each looked-up value
 * gets a blind-index column (*_hash, see App\Support\BlindIndex) that takes
 * over the unique indexes and lookups. Order: add + fill hashes from the
 * current plaintext, move unique indexes to them, widen to TEXT, encrypt.
 */
return new class extends Migration
{
    /** table => [column => original definition] */
    private const COLUMNS = [
        'users' => [
            'name' => 'VARCHAR(255) NOT NULL',
            'email' => 'VARCHAR(255) NOT NULL',
            'phone_code' => 'VARCHAR(8) NULL',
            'phone_number' => 'VARCHAR(20) NULL',
            'kennitala' => 'VARCHAR(20) NULL',
        ],
        'user_emails' => [
            'email' => 'VARCHAR(255) NOT NULL',
        ],
        'user_phones' => [
            'phone_code' => 'VARCHAR(8) NOT NULL',
            'phone_number' => 'VARCHAR(20) NOT NULL',
        ],
    ];

    public function up(): void
    {
        // MySQL DDL isn't transactional: skip columns left by a run that stopped
        // at the duplicate check below, so the migration can simply be re-run.
        $this->addHashColumn('users', 'email_hash', 'email');
        $this->addHashColumn('users', 'kennitala_hash', 'kennitala');
        $this->addHashColumn('users', 'phone_hash', 'phone_number');
        $this->addHashColumn('user_emails', 'email_hash', 'email');
        $this->addHashColumn('user_phones', 'phone_hash', 'phone_number');

        $this->fillHashes('users', fn ($row) => [
            'email_hash' => BlindIndex::email($this->plain($row->email)),
            'kennitala_hash' => BlindIndex::kennitala($this->plain($row->kennitala)),
            'phone_hash' => BlindIndex::phone($this->plain($row->phone_code), $this->plain($row->phone_number)),
        ]);
        $this->fillHashes('user_emails', fn ($row) => [
            'email_hash' => BlindIndex::email($this->plain($row->email)),
        ]);
        $this->fillHashes('user_phones', fn ($row) => [
            'phone_hash' => BlindIndex::phone($this->plain($row->phone_code), $this->plain($row->phone_number)),
        ]);

        // The hashes normalize (lowercase email, digits-only kennitala), so
        // values that differed only in case or a dash would now collide.
        $this->assertNoDuplicates('users', 'email_hash');
        $this->assertNoDuplicates('users', 'kennitala_hash');
        $this->assertNoDuplicates('user_emails', 'email_hash');
        $this->assertNoDuplicates('user_phones', 'phone_hash');

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->dropUnique(['kennitala']);
            $table->unique('email_hash');
            $table->unique('kennitala_hash');
            $table->index('phone_hash');
        });
        Schema::table('user_emails', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->unique('email_hash');
        });
        Schema::table('user_phones', function (Blueprint $table) {
            $table->dropUnique(['phone_code', 'phone_number']);
            $table->unique('phone_hash');
        });

        foreach (self::COLUMNS as $table => $columns) {
            foreach ($columns as $column => $original) {
                $nullability = str_contains($original, 'NOT NULL') ? 'NOT NULL' : 'NULL';
                DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` TEXT {$nullability}");
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
                        if ($row->{$column} !== null && $row->{$column} !== '') {
                            $updates[$column] = $this->plain($row->{$column});
                        }
                    }

                    if ($updates) {
                        DB::table($table)->where('id', $row->id)->update($updates);
                    }
                }
            });

            foreach ($columns as $column => $original) {
                DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` {$original}");
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email_hash']);
            $table->dropUnique(['kennitala_hash']);
            $table->dropIndex(['phone_hash']);
            $table->dropColumn(['email_hash', 'kennitala_hash', 'phone_hash']);
            $table->unique('email');
            $table->unique('kennitala');
        });
        Schema::table('user_emails', function (Blueprint $table) {
            $table->dropUnique(['email_hash']);
            $table->dropColumn('email_hash');
            $table->unique('email');
        });
        Schema::table('user_phones', function (Blueprint $table) {
            $table->dropUnique(['phone_hash']);
            $table->dropColumn('phone_hash');
            $table->unique(['phone_code', 'phone_number']);
        });
    }

    private function addHashColumn(string $table, string $column, string $after): void
    {
        if (! Schema::hasColumn($table, $column)) {
            Schema::table($table, fn (Blueprint $t) => $t->char($column, 64)->nullable()->after($after));
        }
    }

    private function fillHashes(string $table, Closure $hashes): void
    {
        DB::table($table)->orderBy('id')->chunkById(200, function ($rows) use ($table, $hashes) {
            foreach ($rows as $row) {
                DB::table($table)->where('id', $row->id)->update($hashes($row));
            }
        });
    }

    private function assertNoDuplicates(string $table, string $column): void
    {
        $duplicates = DB::table($table)
            ->select($column, DB::raw('GROUP_CONCAT(id) as ids'))
            ->whereNotNull($column)
            ->groupBy($column)
            ->havingRaw('COUNT(*) > 1')
            ->pluck('ids')
            ->all();

        if ($duplicates) {
            throw new RuntimeException(
                "Cannot add a unique index on {$table}.{$column}: rows with the same normalized value (ids: "
                .implode(' / ', $duplicates).'). Merge or fix these rows, then run the migration again.'
            );
        }
    }

    /** The plaintext of a value, whether or not it is already encrypted. */
    private function plain(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            return $value;
        }
    }
};
