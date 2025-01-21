<?php

namespace NextDeveloper\Installer\Support;

use Exception;

class PackageManager
{
    public function resolveDependencies(array $selectedPackages): array
    {
        $resolved = [];
        $packages = config('installer.packages');

        foreach ($selectedPackages as $package) {
            $packageName = $this->extractPackageName($package);
            $packageInfo = $packages[$packageName] ?? null;

            if (!$packageInfo) {
                throw new Exception("Package {$packageName} not found");
            }

            // Resolve dependencies first
            foreach ($packageInfo['dependencies'] ?? [] as $dep) {
                if (!in_array($packages[$dep]['name'], $resolved)) {
                    $resolved[] = $packages[$dep]['name'];
                }
            }

            // Add the package itself
            if (!in_array($packageInfo['name'], $resolved)) {
                $resolved[] = $packageInfo['name'];
            }
        }

        return $resolved;
    }

    protected function extractPackageName(string $package): string
    {
        return explode(' - ', $package)[0];
    }
} 