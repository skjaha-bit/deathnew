<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * Encrypts existing plaintext values in a column, bypassing Eloquent
 * entirely (raw DB reads/writes) so this can run BEFORE the model gets its
 * `encrypted` cast - avoids the chicken-and-egg problem where adding the
 * cast first would make Eloquent try to decrypt still-plaintext values.
 *
 * Idempotent: a value that already decrypts successfully is left alone, so
 * this is safe to re-run (e.g. after adding a new row) without double
 * encrypting.
 */
class EncryptExistingVaultData extends Command
{
    protected $signature = 'vault:encrypt-existing
        {table : The database table name}
        {columns : Comma-separated column names to encrypt}
        {--dry-run : Report what would change without writing anything}
        {--chunk=200 : Rows per batch}';

    protected $description = 'Encrypt existing plaintext values in the given table/columns using Laravel\'s encrypter.';

    public function handle(): int
    {
        $table = $this->argument('table');
        $columns = array_map('trim', explode(',', $this->argument('columns')));
        $dryRun = (bool) $this->option('dry-run');
        $chunkSize = (int) $this->option('chunk');

        $total = 0;
        $encrypted = 0;
        $alreadyEncrypted = 0;

        DB::table($table)->orderBy('id')->chunkById($chunkSize, function ($rows) use ($table, $columns, $dryRun, &$total, &$encrypted, &$alreadyEncrypted) {
            foreach ($rows as $row) {
                $total++;
                $updates = [];

                foreach ($columns as $column) {
                    $value = $row->{$column} ?? null;

                    if ($value === null || $value === '') {
                        continue;
                    }

                    if ($this->looksAlreadyEncrypted($value)) {
                        $alreadyEncrypted++;

                        continue;
                    }

                    $updates[$column] = Crypt::encryptString($value);
                }

                if (! empty($updates)) {
                    $encrypted++;

                    if (! $dryRun) {
                        DB::table($table)->where('id', $row->id)->update($updates);
                    }
                }
            }
        });

        activity()
            ->withProperties(['table' => $table, 'columns' => $columns, 'rows_scanned' => $total, 'rows_encrypted' => $encrypted])
            ->log('encryption.backfill');

        $this->info(($dryRun ? '[DRY RUN] ' : '')."Scanned {$total} row(s) in {$table}: encrypted {$encrypted}, already encrypted {$alreadyEncrypted}.");

        return self::SUCCESS;
    }

    protected function looksAlreadyEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);

            return true;
        } catch (DecryptException) {
            return false;
        }
    }
}
