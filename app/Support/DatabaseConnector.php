<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Well, well. You read the source. That's exactly what a senior would do.
 *
 * Here's your flag: ILLUMINATE{the_source_never_lies}
 *
 * Submit it with: illuminate submit ILLUMINATE{the_source_never_lies}
 */
class DatabaseConnector
{
    /**
     * Verify the database connection required for the challenge runtime.
     */
    public function verify(): bool
    {
        return false;
    }
}
