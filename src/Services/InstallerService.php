<?php

namespace NextDeveloper\Installer\Services;

use Exception;
use Illuminate\Support\Facades\Process;
use NextDeveloper\Installer\Contracts\InstallerInterface;
use NextDeveloper\Installer\Support\PackageManager;

class InstallerService implements InstallerInterface
{
    public function __construct(
        protected PackageManager $packageManager
    ) {}

    public function checkRequirements(): bool
    {
        $requirements = config('installer.requirements');
        
        if (!version_compare(PHP_VERSION, $requirements['php'], '>=')) {
            throw new Exception("PHP version {$requirements['php']} or higher is required");
        }

        if ($requirements['composer'] && empty(exec('composer --version'))) {
            throw new Exception('Composer is not installed');
        }

        return true;
    }

    public function getAvailablePackages(): array
    {
        return config('installer.packages', []);
    }

    public function resolveDependencies(array $selectedPackages): array
    {
        return $this->packageManager->resolveDependencies($selectedPackages);
    }

    public function installPackages(array $packageNames): void
    {
        $packages = config('installer.packages', []);
        
        foreach ($packageNames as $packageName) {
            // Find the package configuration by name
            $package = null;
            foreach ($packages as $key => $info) {
                if ($info['name'] === $packageName) {
                    $package = $info;
                    break;
                }
            }

            if (!$package) {
                throw new Exception("Package configuration not found for: {$packageName}");
            }

            $result = Process::run("composer require {$package['package']}");
            
            if (!$result->successful()) {
                throw new Exception("Failed to install {$package['name']}: " . $result->errorOutput());
            }
            
            $this->publishPackageAssets($package);
        }
    }

    public function publishPackageAssets(array $package): void
    {
        foreach ($package['publishable'] as $type => $shouldPublish) {
            if (!$shouldPublish) continue;

            $result = Process::run(
                "php artisan vendor:publish --provider={$package['provider']} --tag={$type}"
            );

            if (!$result->successful()) {
                throw new Exception("Failed to publish {$type} for {$package['name']}");
            }
        }
    }
} 