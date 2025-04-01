# Creating FreeScout Modules with Settings Menu Items

This guide explains how to create FreeScout modules that include settings pages accessible from the FreeScout admin dashboard.

## Module Structure

A typical FreeScout module follows this structure:

```
ModuleName/
├── Config/
├── Http/
│   ├── Controllers/
│   │   └── SettingsController.php
│   ├── Middleware/
│   └── routes.php
├── Providers/
│   └── ModuleNameServiceProvider.php
├── Resources/
│   ├── views/
│   │   └── settings.blade.php
│   └── lang/
├── Public/
│   ├── css/
│   └── js/
├── module.json
├── composer.json
└── start.php
```

## Step 1: Create module.json

Define your module metadata:

```json
{
  "name": "Your Module Name",
  "alias": "your-module-name",
  "description": "Description of your module's functionality",
  "version": "1.0.0",
  "detailsUrl": "https://github.com/username/your-module",
  "author": "Your Name",
  "authorUrl": "https://github.com/username/",
  "requiredAppVersion": "1.8.7",
  "license": "MIT",
  "keywords": [
    "keyword1",
    "keyword2"
  ],
  "img": "/modules/your-module-name/images/module.svg",
  "active": 0,
  "order": 0,
  "providers": [
    "Modules\\YourModuleName\\Providers\\YourModuleNameServiceProvider"
  ],
  "aliases": {},
  "files": [
    "start.php"
  ],
  "requires": []
}
```

## Step 2: Create the Service Provider

The service provider is crucial for registering your module's functionality:

```php
<?php

namespace Modules\YourModuleName\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class YourModuleNameServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     */
    public function boot(Router $router)
    {
        $this->registerViews();
        $this->registerMiddleware($router);
        $this->hooks();
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        // Additional registration logic if needed
    }

    /**
     * Register views.
     */
    protected function registerViews()
    {
        $viewPath = resource_path('views/modules/yourmodulename');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/yourmodulename';
        }, \Config::get('view.paths')), [$sourcePath]), 'your-module-name');
    }

    /**
     * Register middleware if needed.
     */
    protected function registerMiddleware(Router $router)
    {
        // $router->aliasMiddleware('your.middleware', \Modules\YourModuleName\Http\Middleware\YourMiddleware::class);
    }

    /**
     * Module hooks.
     */
    public function hooks()
    {
        // IMPORTANT: This is how you add your module to the settings menu
        \Eventy::addFilter('settings.sections', function ($sections) {
            $sections['your-module-section'] = [
                'title' => __('Your Module Name'),
                'icon' => 'icon-name', // Use an appropriate icon name
                'order' => 150
            ];
            return $sections;
        }, 15);

        // IMPORTANT: This tells FreeScout which view to load when the settings menu item is clicked
        \Eventy::addFilter('settings.view', function ($view, $section) {
            if ($section !== 'your-module-section') {
                return $view;
            }
            return 'your-module-name::settings';
        }, 20, 2);
        
        // Optional: Add additional CSS/JS for your settings page
        \Eventy::addFilter('settings.section_settings', function ($settings, $section) {
            if ($section !== 'your-module-section') {
                return $settings;
            }
            
            // Include any js/css files if needed
            $settings['js'] = asset('modules/your-module-name/js/settings.js');
            $settings['css'] = asset('modules/your-module-name/css/settings.css');
            
            return $settings;
        }, 20, 2);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides()
    {
        return [];
    }
}
```

## Step 3: Create start.php

This file ensures your routes are loaded:

```php
<?php

if (!app()->routesAreCached()) {
    require __DIR__ . '/Http/routes.php';
}
```

## Step 4: Create Routes

Define your module's routes, especially for the settings page:

```php
<?php

// Settings routes (admin only)
Route::group(['middleware' => ['web', 'auth', 'roles'], 'roles' => ['admin'], 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\YourModuleName\Http\Controllers'], function () {
    // IMPORTANT: The URL must use app-settings prefix for settings pages
    Route::get('/app-settings/your-module-section', ['uses' => 'SettingsController@index'])->name('your-module-name.settings');
    Route::post('/app-settings/your-module-section', ['uses' => 'SettingsController@save'])->name('your-module-name.settings.save');
});

// Other module routes...
```

## Step 5: Create the Settings Controller

Create a controller to handle displaying and saving settings:

```php
<?php

namespace Modules\YourModuleName\Http\Controllers;

use App\Option;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles:admin');
    }
    
    /**
     * Show the settings page.
     */
    public function index()
    {
        // Retrieve any existing settings from the database
        $setting_value = Option::get('your_module_setting_name');
        
        return view('your-module-name::settings', [
            'setting_value' => $setting_value,
        ]);
    }
    
    /**
     * Save settings.
     */
    public function save(Request $request)
    {
        // IMPORTANT: Use $request->validate instead of $this->validate
        $validated = $request->validate([
            'setting_field' => 'required|string|max:255',
        ]);
        
        // Save settings to the database
        Option::set('your_module_setting_name', $request->setting_field);
        
        \Session::flash('flash_success_floating', __('Settings saved'));
        
        return redirect()->route('your-module-name.settings');
    }
}
```

## Step 6: Create the Settings View

Create a Blade template for your settings page:

```blade
@extends('layouts.app')

@section('title', __('Your Module Name'))

@section('content')
<div class="section-heading">
    {{ __('Your Module Settings') }}
</div>

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <form class="form-horizontal" method="POST" action="{{ route('your-module-name.settings.save') }}">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('setting_field') ? ' has-error' : '' }}">
                    <label for="setting_field" class="col-sm-2 control-label">{{ __('Setting Name') }}</label>

                    <div class="col-sm-6">
                        <input id="setting_field" type="text" class="form-control" name="setting_field" value="{{ old('setting_field', $setting_value) }}" required autofocus>

                        @if ($errors->has('setting_field'))
                            <span class="help-block">
                                <strong>{{ $errors->first('setting_field') }}</strong>
                            </span>
                        @endif
                        <p class="help-block">
                            {{ __('Description of this setting') }}
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6 col-sm-offset-2">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Save') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
```

## Common Pitfalls and Troubleshooting

1. **Middleware Issues**: 
   - Use `'middleware' => ['web', 'auth', 'roles'], 'roles' => ['admin']` pattern instead of directly including 'admin' in the middleware array
   - In controllers, use `$this->middleware('roles:admin')` not `$this->middleware('admin')`

2. **URL Path Issues**:
   - Use `/app-settings/your-module-section` format for settings pages
   - Make sure the section name in routes.php matches the section name in the service provider

3. **Controller Validation**:
   - Use `$request->validate([...])` instead of `$this->validate($request, [...])`
   - The base Controller class doesn't include ValidationRequests trait

4. **View Registration**:
   - Ensure your view namespace in loadViewsFrom matches what you use in the settings.view filter
   - Example: `'your-module-name::settings'` should match what you registered

5. **Permission Issues**:
   - If you encounter file permission issues on your server, ensure proper permissions:
   - `chmod -R 775 /path/to/storage`
   - Set ownership to the web server user

## Deployment

1. Zip your module directory
2. Upload to FreeScout via the Modules section in admin
3. Enable the module
4. Your settings menu item should appear in the FreeScout settings

## Testing Module Changes During Development

After making changes to your module:

1. Clear Laravel caches:
   ```
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. Reload FreeScout in your browser

This ensures your changes are picked up by the application.

## Working with JavaScript in FreeScout

FreeScout uses Content Security Policy (CSP) to protect against Cross-Site Scripting (XSS) attacks. This means any inline JavaScript in your Blade templates must include a proper nonce attribute.

### Adding CSP Nonce to Inline Scripts

Always include the FreeScout CSP nonce helper when adding inline JavaScript:

```blade
<script type="text/javascript" {!! \Helper::cspNonceAttr() !!}>
    document.addEventListener('DOMContentLoaded', function() {
        // Your JavaScript code here
        const button = document.querySelector('.your-button');
        button.addEventListener('click', function() {
            // Handle click event
        });
    });
</script>
```

Without this nonce attribute, browsers will block your inline scripts with a CSP error like:

```
Refused to execute inline script because it violates the following Content Security Policy directive: "script-src 'self' 'nonce-xxx'".
```

### Best Practices for Module JavaScript

1. **Use Vanilla JavaScript** instead of jQuery when possible for better performance
2. **Always include the CSP nonce** with `{!! \Helper::cspNonceAttr() !!}`
3. **Consider external JS files** for complex functionality, which you can register through the service provider
4. **Use `document.addEventListener('DOMContentLoaded', function() {...})` pattern** for initialization
