<?php

namespace Modules\Auth\Exceptions;

use RuntimeException;

class NationalRegistryUnavailableException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('National registry is unavailable. Please try again later.');
    }
}
