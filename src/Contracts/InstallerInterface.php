<?php

namespace NextDeveloper\Installer\Contracts;

interface InstallerInterface
{
    public function checkRequirements(): bool;
    public function getAvailablePackages(): array;
    public function resolveDependencies(array $selectedPackages): array;
    public function installPackages(array $packages): void;
    public function publishPackageAssets(array $package): void;
} 