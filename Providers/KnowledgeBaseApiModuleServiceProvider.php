<?php

namespace Modules\KnowledgeBaseApiModule\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class KnowledgeBaseApiModuleServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->registerViews();
        $this->registerMiddleware($router);
        $this->hooks();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register views.
     *
     * @return void
     */
    protected function registerViews()
    {
        $viewPath = resource_path('views/modules/knowledgebaseapimodule');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/knowledgebaseapimodule';
        }, \Config::get('view.paths')), [$sourcePath]), 'knowledgebaseapimodule');
    }

    /**
     * Register the middleware.
     *
     * @param \Illuminate\Routing\Router $router
     */
    protected function registerMiddleware(Router $router)
    {
        $router->aliasMiddleware('knowledgebase.api.token', \Modules\KnowledgeBaseApiModule\Http\Middleware\ApiTokenMiddleware::class);
    }

    /**
     * Module hooks.
     */
    public function hooks()
    {
        // Replace the existing menu action with correct filters
        
        // Add item to settings sections
        \Eventy::addFilter('settings.sections', function ($sections) {
            $sections['knowledge-base-api'] = [
                'title' => __('Knowledge Base API'),
                'icon' => 'book', // You can change the icon as needed
                'order' => 150,
                'active' => \Request::is('*app-settings/knowledge-base-api*')
            ];
            
            // Add KB Analytics section
            $sections['kb-analytics'] = [
                'title' => __('KB Analytics'),
                'icon' => 'signal',
                'order' => 151,
                'active' => \Request::is('*app-settings/kb-analytics*')
            ];
            
            return $sections;
        }, 15);

        // Settings view name
        \Eventy::addFilter('settings.view', function ($view, $section) {
            if ($section === 'knowledge-base-api') {
                return 'knowledgebaseapimodule::settings';
            }
            
            if ($section === 'kb-analytics') {
                return 'knowledgebaseapimodule::analytics';
            }
            
            return $view;
        }, 20, 2);
        
        // Ensure settings javascript/css are loaded
        \Eventy::addFilter('settings.section_settings', function ($settings, $section) {
            if ($section !== 'knowledge-base-api' && $section !== 'kb-analytics') {
                return $settings;
            }
            
            // $settings['js'] = asset('modules/knowledgebaseapimodule/js/settings.js');
            // $settings['css'] = asset('modules/knowledgebaseapimodule/css/settings.css');
            
            return $settings;
        }, 20, 2);
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
}
