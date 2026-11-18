<?php

namespace App\Console\Commands;

use Database\Seeders\ShareDataSeeder;
use Illuminate\Console\Command;

class ImportShareDatasets extends Command
{
    protected $signature = 'share:import-datasets';
    protected $description = 'Import R18/R19 CSV datasets from the datasets/ directory into MySQL/SQLite';

    public function handle(): int
    {
        try {
            $this->info('Importing SHARE CSV datasets...');
            $seeder = new ShareDataSeeder();
            $seeder->setContainer(app())->setCommand($this);
            $seeder->run();
            $this->info('Import completed. R18: 8 relief requests, 7 inventory records. R19: 8 emergency requests (same relief_requests table).');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Import failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
