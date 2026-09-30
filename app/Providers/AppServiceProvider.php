<?php

namespace App\Providers;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (
            app()->environment('production') ||
            str_contains(request()->header('host', ''), 'trycloudflare.com') ||
            str_contains(request()->header('host', ''), 'ngrok') ||
            request()->header('x-forwarded-proto') === 'https' ||
            request()->server('HTTP_X_FORWARDED_PROTO') === 'https' ||
            request()->isSecure()
        ) {
            URL::forceScheme('https');
        }

        Storage::extend('google', function ($app, $config) {
            $options = [];
            if (! empty($config['teamDriveId'])) {
                $options['teamDriveId'] = $config['teamDriveId'];
            }
            if (! empty($config['sharedFolderId'])) {
                $options['sharedFolderId'] = $config['sharedFolderId'];
            }

            $client = new Client;

            $refreshToken = $config['refreshToken'] ?? null;
            if (empty($refreshToken)) {
                $tokenFile = storage_path('app/google_drive_token.json');
                if (file_exists($tokenFile)) {
                    $tokenData = json_decode(file_get_contents($tokenFile), true);
                    $refreshToken = $tokenData['refresh_token'] ?? null;
                }
            }

            if (! empty($config['serviceAccountKey']) && file_exists($config['serviceAccountKey'])) {
                $client->setAuthConfig($config['serviceAccountKey']);
                $client->addScope(Drive::DRIVE);
            } else {
                $client->setClientId($config['clientId'] ?? '');
                $client->setClientSecret($config['clientSecret'] ?? '');
                if (! empty($refreshToken)) {
                    $client->refreshToken($refreshToken);
                }
            }

            $service = new Drive($client);
            $folder = $config['folder'] ?? $config['folderName'] ?? null;
            if (empty($folder)) {
                $folder = (! empty($config['folderId']) && ! preg_match('/^[a-zA-Z0-9_-]{25,}$/', (string) $config['folderId']))
                    ? $config['folderId']
                    : 'SwanFlow Uploads';
            }
            $adapter = new GoogleDriveAdapter($service, $folder, $options);
            $driver = new Filesystem($adapter);

            return new FilesystemAdapter($driver, $adapter, $config);
        });
    }
}
