<?php

namespace Jmnn\Crub\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CrubCommand extends Command
{
    protected $signature = 'crub:build {model}';

    protected $description = 'Command build crud';

    public function handle()
    {
//        Artisan::call('make:controller',[]);
//        Artisan::call('make:model',[]);
        return Command::SUCCESS;
    }
}
