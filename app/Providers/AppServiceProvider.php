<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /** @var \Illuminate\Foundation\Exceptions\Handler $handler */
        $handler = $this->app->make(ExceptionHandler::class);

        $handler->renderable(function (RuntimeException $e, OutputInterface $output): void {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void {}
}
