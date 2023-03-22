<?php

namespace Repository;

use Illuminate\Support\ServiceProvider;
use Repository\Console\RepositoryMakeCommand;
use Repository\Console\RepositoryOnlyMakeCommand;
use Repository\Console\ServiceMakeCommand;
use Repository\Console\ServiceOnlyMakeCommand;
use Repository\Console\FullControllMakeCommand;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
    protected $commands = [
        RepositoryMakeCommand::class,
        ServiceMakeCommand::class,
        RepositoryOnlyMakeCommand::class,
        ServiceOnlyMakeCommand::class,
        FullControllMakeCommand::class
    ];
}