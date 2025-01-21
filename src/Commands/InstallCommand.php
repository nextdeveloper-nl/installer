<?php

namespace NextDeveloper\Installer\Commands;

use Exception;
use Illuminate\Console\Command;
use NextDeveloper\Installer\Contracts\InstallerInterface;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;
use function Laravel\Prompts\progress;

class InstallCommand extends Command
{
    protected $signature = 'nextdeveloper:install {--force : Force the installation without confirmation}';
    protected $description = 'Install NextDeveloper packages from the official repository';
    protected $startTime;

    public function __construct(
        protected InstallerInterface $installer
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $this->startTime = microtime(true);

        try {
            $this->showWelcomeMessage();
            
            // Check requirements with spinner
            spin(
                fn () => $this->installer->checkRequirements(),
                'Checking system requirements...'
            );
            
            $packages = $this->installer->getAvailablePackages();
            $this->displayAvailablePackages($packages);

            $selectedPackages = $this->selectPackages($packages);
            if (empty($selectedPackages)) {
                error('❌ You must select at least one package');
                return 1;
            }

            $packagesToInstall = $this->installer->resolveDependencies($selectedPackages);
            $this->showInstallationSummary($packagesToInstall);

            if ($this->shouldProceed()) {
                $this->installPackagesWithProgress($packagesToInstall);
                $this->showCompletionMessage();
                return 0;
            }

            info('💡 Installation cancelled by user');
            return 0;

        } catch (Exception $e) {
            $this->handleError($e);
            return 1;
        }
    }

    protected function showWelcomeMessage(): void
    {
        $this->line('');
        $this->info('🚀 Welcome to NextDeveloper Installer');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('');
    }

    protected function displayAvailablePackages(array $packages): void
    {
        $tableData = [];
        foreach ($packages as $key => $package) {
            $tableData[] = [
                'name' => $package['name'],
                'description' => $package['description'],
                'dependencies' => empty($package['dependencies']) ?
                    'None' : implode(', ', $package['dependencies'])
            ];
        }

        $this->line('📦 Available Packages:');
        $this->line('');
        table(
            ['Package', 'Description', 'Dependencies'],
            $tableData
        );
        $this->line('');
    }

    protected function selectPackages(array $packages): array
    {
        $choices = array_map(function ($package) {
            return "{$package['name']} - {$package['description']}";
        }, $packages);

        return multiselect(
            "📋 Select packages to install:",
            $choices,
            default: [array_key_first($choices)],
            required: true,
        );
    }

    protected function showInstallationSummary(array $packages): void
    {
        $this->line('');
        $this->info('📋 Installation Summary:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━');
        foreach ($packages as $index => $package) {
            $this->line(sprintf(" %d. %s", $index + 1, $package));
        }
        $this->line('');
    }

    protected function installPackagesWithProgress(array $packages): void
    {
        $totalPackages = count($packages);
        
        foreach ($packages as $index => $package) {
            $currentStep = $index + 1;
            $this->line(sprintf(
                "📦 Installing package (%d/%d): %s",
                $currentStep,
                $totalPackages,
                $package
            ));

            // Show spinner while installing each package
            spin(
                fn () => $this->installer->installPackages([$package]),
                'Installing dependencies...'
            );

            $this->line('✅ Installation completed');
            $this->line('');
        }
    }

    protected function showCompletionMessage(): void
    {
        $duration = round(microtime(true) - $this->startTime, 2);

        $this->line('');
        $this->info('✨ Installation completed successfully! ✨');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line("⏱️  Total time: {$duration} seconds");
        $this->line('');
        $this->info('📘 Next steps:');
        $this->line('  1. Review the configuration files in config/');
        $this->line('  2. Run migrations if needed: php artisan migrate');
        $this->line('  3. Check the documentation at: https://github.com/nextdeveloper-nl/');
        $this->line('');
    }

    protected function handleError(Exception $e): void
    {
        $this->line('');
        error('❌ Installation failed!');
        error('━━━━━━━━━━━━━━━━━━');
        error($e->getMessage());

        if (file_exists(base_path('composer.json.backup'))) {
            if (confirm('Would you like to restore the composer.json backup?')) {
                copy(base_path('composer.json.backup'), base_path('composer.json'));
                info('✅ Backup restored successfully');
            }
        }
    }

    protected function shouldProceed(): bool
    {
        if (!$this->option('force') && !$this->option('no-interaction')) {
            return confirm('Would you like to proceed with the installation?');
        }
        return true;
    }
}