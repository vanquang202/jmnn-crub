<?php
namespace Jmnn\Crub;
use Illuminate\Support\ServiceProvider;

class CrubServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerCommands();
    }

    private function registerCommands()
    {
        $this->commands(
            [
                \Jmnn\Crub\Console\Commands\CrubCommand::class
            ]
        );
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'crub');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }
}
