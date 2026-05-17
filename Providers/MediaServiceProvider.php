<?php

namespace Modules\Media\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Intervention\Image\Image;
use DataSDK\Categories\Models\Categories;
use Modules\Media\Models\Categories as MemberCategories;
use Modules\Media\Observers\MediaObserver;
use Modules\Media\Models\Media;
use Intervention\Image\ImageServiceProvider;
use Modules\Media\Services\MediaLibraryService;
use Modules\Media\Services\ProfileImageService;


class MediaServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Media';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'media';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        $this->registerVendorProviders();
        // Sæt ekstra diske via config

        $this->observers();
        

    }

    public function registerVendorProviders(){

       // $this->app->register(ImageServiceProvider::class);

    }
  
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        
        $this->app->register(RouteServiceProvider::class);
        
    

        $this->app->register(\Modules\Media\Providers\DiskServiceProvider::class);

    }


    public function observers(){

        Media::observe(MediaObserver::class);

    }
    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');

        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );

        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/media-library.php'), 'media-library'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);
        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }

    private function setImageManipulator()
    {
        // Define a thumbnail variant
        ImageManipulator::defineVariant(
            'thumb',
            ImageManipulation::make(function (Image $image, $originalMedia) {
                $image->fit(100, 100); // Resize og crop til 100x100
            })->outputJpegFormat()
        );
    }

 
}
