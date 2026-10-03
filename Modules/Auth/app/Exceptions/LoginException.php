<?php

namespace Modules\Auth\Exceptions;

use RuntimeException;

/**
 * A login attempt that must stop with a specific HTTP status (deceased person,
 * invalid kennitala, registry outage, ...).
 */
class LoginException extends RuntimeException
{
    public function __construct(string $message, public readonly int $status, public readonly mixed $data = null)
    {
        parent::__construct($message);
    }
}
