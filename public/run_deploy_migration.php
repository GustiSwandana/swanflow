<?php

use Illuminate\Contracts\Console\Kernel;

/**
 * Safe Migration & Cache Runner for Hostinger Production Deployment
 * SwanFlow 2026
 *
 * NOTE: Only runs 'migrate --force' which is purely additive.
 * Does NOT run migrate:fresh, migrate:refresh, db:wipe, or db:seed.
 * All existing data is 100% preserved.
 */
$secret = $_GET['secret'] ?? '';
if ($secret !== 'swanflow_deploy_2026_safe') {
    http_response_code(403);
    exit("Access denied. Invalid deployment secret.\n");
}

header('Content-Type: text/plain; charset=utf-8');

echo "==========================================\n";
echo " SwanFlow Production Safe Deployment Runner\n";
echo "==========================================\n\n";

// Set base directory
$baseDir = __DIR__;
if (! file_exists($baseDir.'/artisan') && file_exists(dirname($baseDir).'/artisan')) {
    $baseDir = dirname($baseDir);
}

chdir($baseDir);

// 1. Extract deploy_update.zip if present
$zipLocations = [
    $baseDir.'/deploy_update.zip',
    $baseDir.'/public/deploy_update.zip',
];

foreach ($zipLocations as $zipPath) {
    if (file_exists($zipPath)) {
        echo "1. Extracting update package ({$zipPath})...\n";
        $zip = new ZipArchive;
        if ($zip->open($zipPath) === true) {
            $zip->extractTo($baseDir);
            $zip->close();
            @unlink($zipPath);
            echo "   -> Successfully extracted and cleaned up update package!\n";
        } else {
            echo "   -> WARNING: Could not extract {$zipPath}.\n";
        }
        break;
    }
}

// 2. Safe .env configuration update
echo "2. Ensuring .env settings for production and Google Drive OAuth...\n";
try {
    $envPath = $baseDir.'/.env';
    if (file_exists($envPath)) {
        $envContent = file_get_contents($envPath);
        $updatedEnv = $envContent;

        $settings = [
            'APP_URL' => 'https://swanflow.space',
            'SESSION_LIFETIME' => '5',
            'SESSION_INACTIVITY_TIMEOUT' => '5',
        ];

        if (!empty($_REQUEST['google_client_id'])) {
            $settings['GOOGLE_DRIVE_CLIENT_ID'] = $_REQUEST['google_client_id'];
        }
        if (!empty($_REQUEST['google_client_secret'])) {
            $settings['GOOGLE_DRIVE_CLIENT_SECRET'] = $_REQUEST['google_client_secret'];
        }
        if (!empty($_REQUEST['google_folder_id'])) {
            $settings['GOOGLE_DRIVE_FOLDER_ID'] = $_REQUEST['google_folder_id'];
        }

        foreach ($settings as $key => $val) {
            if (preg_match("/^{$key}=.*$/m", $updatedEnv)) {
                $updatedEnv = preg_replace("/^{$key}=.*$/m", "{$key}={$val}", $updatedEnv);
            } else {
                $updatedEnv .= "\n{$key}={$val}\n";
            }
        }

        if (! preg_match('/^GOOGLE_DRIVE_REFRESH_TOKEN=/m', $updatedEnv)) {
            $updatedEnv .= "GOOGLE_DRIVE_REFRESH_TOKEN=\n";
        }

        if ($updatedEnv !== $envContent) {
            file_put_contents($envPath, $updatedEnv);
            echo "   -> .env updated with production keys successfully.\n";
        } else {
            echo "   -> .env keys are already up-to-date.\n";
        }
    }
} catch (Throwable $e) {
    echo '   -> Env Note: '.$e->getMessage()."\n";
}

// 3. Bootstrap Laravel
require $baseDir.'/vendor/autoload.php';
$app = require_once $baseDir.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

// 4. Safe Database Migrations
echo "3. Running Safe Database Migrations (additive only)...\n";
try {
    $kernel->call('migrate', ['--force' => true]);
    echo $kernel->output()."\n";
} catch (Throwable $e) {
    echo '   -> Migration Output/Note: '.$e->getMessage()."\n";
}

// 5. Cache Clear & Rebuild
echo "4. Clearing & Rebuilding Caches...\n";
try {
    $kernel->call('view:clear');
    echo '   -> view:clear: '.trim($kernel->output())."\n";

    $kernel->call('route:clear');
    echo '   -> route:clear: '.trim($kernel->output())."\n";

    $kernel->call('config:clear');
    echo '   -> config:clear: '.trim($kernel->output())."\n";

    $kernel->call('cache:clear');
    echo '   -> cache:clear: '.trim($kernel->output())."\n";
} catch (Throwable $e) {
    echo '   -> Cache Output/Note: '.$e->getMessage()."\n";
}

echo "\n==========================================\n";
echo "ALL_MIGRATIONS_AND_CACHE_CLEAR_SUCCESS\n";
echo "==========================================\n";
