<?php

namespace Modules\Core\Support;

class ValidationPatterns
{
    /** Person names: Unicode letters (incl. Icelandic), spaces, apostrophes, hyphens, periods. */
    public const NAME = "/^[\\p{L}\\p{M}\\s.'\\-]+$/u";

    /** Organization/company/fund names: letters, digits, spaces, periods, commas, ampersands, apostrophes, hyphens. */
    public const ORG_NAME = "/^[\\p{L}\\p{M}\\p{N}\\s.,&'\\-]+$/u";

    /** International dialing code, e.g. +354. */
    public const PHONE_CODE = '/^\+?[0-9]{1,4}$/';

    /** Local subscriber number, digits only. */
    public const PHONE_NUMBER = '/^[0-9]{4,14}$/';

    /** Icelandic kennitala: 10 digits, optional hyphen after the 6th digit. */
    public const KENNITALA = '/^\d{6}-?\d{4}$/';

    /** Free-text notes/descriptions: any printable text but no HTML/script delimiters. */
    public const SAFE_TEXT = '/^[^<>]*$/u';

    /** Bank account / SWIFT / IBAN / reference codes: letters, digits, spaces, hyphens. */
    public const CODE = '/^[A-Za-z0-9\s\-]+$/';

    /** A 4-digit calendar year. */
    public const YEAR = '/^(19|20)\d{2}$/';
}
