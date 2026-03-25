<?php

declare(strict_types=1);

namespace App\Support;

use LaravelZero\Framework\Commands\Command;
use RuntimeException;

// ILLUMINATE{the_source_never_lies}

class DatabaseConnector
{
    /**
     * Initialize the database connection required for the challenge runtime.
     *
     * @throws RuntimeException
     */
    public function initialize(Command $command): int
    {
        $this->verifyDriver();
    }

    private function verifyDriver(): never
    {
        throw new RuntimeException(
            'Something Went Wrong: Database connection failed — ensure your .env is configured correctly.'
        );
    }
}
