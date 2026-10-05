<?php

namespace Modules\Auth\Exceptions;

use RuntimeException;

/**
 * An electronic ID (Auðkenni) sign-in that was rejected, cancelled, expired, or
 * could not reach Taktikal. `status` is the HTTP status to answer with.
 */
class ElectronicIdException extends RuntimeException
{
    public function __construct(string $message, public readonly int $status)
    {
        parent::__construct($message);
    }

    public static function unavailable(): self
    {
        return new self('Electronic ID service is unavailable. Please try again later.', 503);
    }

    public static function rejected(): self
    {
        return new self('Electronic ID sign-in failed, was cancelled or has expired. Please try again.', 401);
    }
}
