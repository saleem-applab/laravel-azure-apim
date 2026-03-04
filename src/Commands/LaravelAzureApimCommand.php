<?php

namespace Applab\LaravelAzureApim\Commands;

use Illuminate\Console\Command;

class LaravelAzureApimCommand extends Command
{
    public $signature = 'laravel-azure-apim';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
