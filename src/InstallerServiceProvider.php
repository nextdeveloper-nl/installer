<?php

namespace NextDeveloper\Installer;

use Illuminate\Support\ServiceProvider;
use NextDeveloper\Installer\Contracts\InstallerInterface;
use NextDeveloper\Installer\Services\InstallerService;

class InstallerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/Config/installer.php', 'installer'
        );

        $this->app->bind(InstallerInterface::class, InstallerService::class);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/Config/installer.php' => config_path('installer.php'),
        ], 'config');

        $this->commands([
            Commands\InstallCommand::class,
        ]);
    }
}
