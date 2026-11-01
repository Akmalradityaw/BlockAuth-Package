<?php

namespace BlockAuth\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blockauth:install 
                            {--force : Menimpa file yang sudah ada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Instal modul otentikasi, profil, RBAC, dan UI React BlockAuth';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai instalasi BlockAuth...');
        $filesystem = new Filesystem();
        $stubsPath = __DIR__ . '/../../stubs';

        // 1. Copy backend code (Actions, DTO, Controllers, Requests, Models, Policies, Providers)
        $this->comment('Menyalin kode backend (Actions, DTOs, Controllers, Requests, Models)...');
        $filesystem->copyDirectory($stubsPath . '/app', app_path());

        // 2. Copy database migrations & seeders
        $this->comment('Menyalin migrations dan seeders...');
        $filesystem->copyDirectory($stubsPath . '/database/migrations', database_path('migrations'));
        $filesystem->copyDirectory($stubsPath . '/database/seeders', database_path('seeders'));

        // 3. Copy frontend resources (Inertia.js + React, Views, CSS)
        $this->comment('Menyalin file frontend (Inertia.js + React, Views, CSS)...');
        $filesystem->copyDirectory($stubsPath . '/resources', resource_path());

        // 4. Copy config files
        $this->comment('Menyalin konfigurasi...');
        if (!file_exists(config_path('permission.php')) || $this->option('force')) {
            $filesystem->copy($stubsPath . '/config/permission.php', config_path('permission.php'));
        }

        // 5. Setup Routes & Bootstrap Middleware
        $this->comment('Menyiapkan rute dan middleware...');
        $filesystem->copyDirectory($stubsPath . '/routes', base_path('routes'));
        $this->updateBootstrapApp();

        // 6. Copy build configs
        $this->comment('Menyalin konfigurasi Vite, PostCSS, dan Tailwind...');
        $filesystem->copy($stubsPath . '/tailwind.config.js', base_path('tailwind.config.js'));
        $filesystem->copy($stubsPath . '/postcss.config.js', base_path('postcss.config.js'));
        $filesystem->copy($stubsPath . '/vite.config.js', base_path('vite.config.js'));

        // 7. Update package.json
        $this->comment('Memperbarui dependensi package.json...');
        $this->updateNodePackages();

        $this->newLine();
        $this->info('✅ BlockAuth berhasil diinstal!');
        $this->newLine();
        $this->comment('Langkah selanjutnya:');
        $this->line('  1. Jalankan: npm install');
        $this->line('  2. Jalankan: php artisan storage:link');
        $this->line('  3. Jalankan: php artisan migrate --seed');
        $this->line('  4. Jalankan: npm run dev');
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Daftarkan middleware HandleInertiaRequests dan CheckMaintenance di bootstrap/app.php.
     */
    protected function updateBootstrapApp(): void
    {
        $bootstrapPath = base_path('bootstrap/app.php');
        if (!file_exists($bootstrapPath)) {
            return;
        }

        $content = file_get_contents($bootstrapPath);

        if (!str_contains($content, 'HandleInertiaRequests::class')) {
            $useStatements = "use App\Http\Middleware\CheckMaintenance;\nuse App\Http\Middleware\HandleInertiaRequests;\n";
            $content = preg_replace('/(use Illuminate\\\\Foundation\\\\Application;)/', $useStatements . '$1', $content, 1);

            $middlewareSnippet = "->withMiddleware(function (Middleware \$middleware) {\n        \$middleware->web(append: [\n            HandleInertiaRequests::class,\n            CheckMaintenance::class,\n        ]);\n    })";

            $pattern = '/->withMiddleware\(function\s*\(Middleware\s*\$middleware\)\s*\{\s*(\/\/.*?)?\s*\}\)/s';
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $middlewareSnippet, $content, 1);
            }

            file_put_contents($bootstrapPath, $content);
        }
    }

    /**
     * Update dependencies di package.json.
     */
    protected function updateNodePackages(): void
    {
        $packageJsonPath = base_path('package.json');
        if (!file_exists($packageJsonPath)) {
            return;
        }

        $packages = json_decode(file_get_contents($packageJsonPath), true);
        if (empty($packages['name'])) {
            $packages['name'] = 'laravel-app';
        }

        $dependencies = [
            '@inertiajs/react' => '^2.0.0',
            'chart.js' => '^4.5.1',
            'react' => '^18.3.1',
            'react-chartjs-2' => '^5.3.1',
            'react-dom' => '^18.3.1',
        ];

        $devDependencies = [
            '@vitejs/plugin-react' => '^4.3.4',
            'autoprefixer' => '^10.4.20',
            'daisyui' => '^4.12.14',
            'postcss' => '^8.4.49',
            'tailwindcss' => '^3.4.15',
        ];

        // Hapus Tailwind v4 jika ada (karena BlockAuth menggunakan Tailwind v3 + daisyUI 4)
        unset($packages['devDependencies']['@tailwindcss/vite']);

        $packages['dependencies'] = array_merge(
            $packages['dependencies'] ?? [],
            $dependencies
        );

        $packages['devDependencies'] = array_merge(
            $packages['devDependencies'] ?? [],
            $devDependencies
        );

        ksort($packages['dependencies']);
        ksort($packages['devDependencies']);

        file_put_contents(
            $packageJsonPath,
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }
}
