<?php

namespace Applab\LaravelAzureApim\Commands;

use Illuminate\Console\Command;

class LaravelAzureApimCommand extends Command
{
    public $signature = 'apim:info';

    public $description = 'Display information about the Laravel Azure APIM package';

    public function handle(): int
    {
        $this->info('Laravel Azure APIM Package');
        $this->line('');
        $this->line('  <fg=yellow>Facade usage:</>');
        $this->line('    Apim::api()->create(...)');
        $this->line('    Apim::policy()->applyThrottle(...)');
        $this->line('    Apim::product()->create(...)');
        $this->line('    Apim::subscription()->subscribe(...)');
        $this->line('    Apim::monitor()->apiUsage(...)');
        $this->line('');
        $this->line('  <fg=yellow>Publish config:</>');
        $this->line('    php artisan vendor:publish --tag=apim-config');
        $this->line('');

        return self::SUCCESS;
    }
}
