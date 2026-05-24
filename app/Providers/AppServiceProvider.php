<?php

namespace App\Providers;

use App\File;
use App\Observers\FileObserver;
use App\Setting;
use Form;
use Illuminate\Support\ServiceProvider;

// Tambahkan "use" ini untuk Google Drive
use Illuminate\Support\Facades\Storage;
use Google\Client as GoogleClient;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use Google\Service\Drive;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 1. Kodingan Lama Komandan (Dynamic Constants)
        try {
            $settings = Setting::all();
            foreach ($settings as $setting) {
                config(['settings.' . $setting->name => $setting->value]);
            }
            config(['settings_array.model_types_plural' => ['tags' => ucfirst(config('settings.tags_label_plural')), 'documents' => ucfirst(config('settings.document_label_plural')), 'files' => ucfirst(config('settings.file_label_plural'))]]);
        } catch (\Exception $e) {}

        // 2. Kodingan Lama Komandan (Laravel Collective)
        Form::component('bsText', 'components.input', ['name', 'value' => null, 'attributes' => [], 'label' => null]);
        Form::component('bsTextarea', 'components.textarea', ['name', 'value' => null, 'attributes' => [], 'label' => null]);
        Form::component('bsSelect', 'components.select', ['name', 'list' => null, 'value'=>null, 'attributes' => [], 'label' => null]);

        // 3. Kodingan Lama Komandan (Observer)
        File::observe(FileObserver::class);

        Storage::extend('google', function($app, $config) {
            $client = new GoogleClient();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);
            $service = new Drive($client);
            $adapter = new GoogleDriveAdapter($service, $config['folderId']);
            $driver = new Filesystem($adapter);

            return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter, $config);
        });
    }
}