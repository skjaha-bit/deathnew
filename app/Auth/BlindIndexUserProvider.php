<?php

namespace App\Auth;

use App\Support\BlindIndex;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Eloquent user provider that looks users up by `email` through the
 * email_hash blind index, since users.email is encrypted at rest. Used by
 * the admin login (Auth::attempt) and the password broker.
 */
class BlindIndexUserProvider extends EloquentUserProvider
{
    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials): ?Authenticatable
    {
        if (array_key_exists('email', $credentials)) {
            $credentials['email_hash'] = BlindIndex::email($credentials['email']);
            unset($credentials['email']);
        }

        return parent::retrieveByCredentials($credentials);
    }
}
