<?php

declare(strict_types=1);

namespace App\Support;

// ILLUMINATE{the_source_never_lies}

class DatabaseConnector
{
    /**
     * Verify the database connection required for the challenge runtime.
     */
    public function verify(): bool
    {
        return false; // Always fails — this is intentional
    }
}
