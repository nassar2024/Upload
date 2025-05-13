<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    public function boot()
    {
        $this->routes(function () {
            Log::info('RouteServiceProvider: Checking routes/api.php existence', [
                'exists' => file_exists(base_path('routes/api.php')),
                'path' => base_path('routes/api.php')
            ]);
            try {
                Route::prefix('api')
                     ->middleware('api')
                     ->namespace($this->namespace)
                     ->group(base_path('routes/api.php'));
                Log::info('RouteServiceProvider: Successfully loaded routes/api.php');
            } catch (\Exception $e) {
                Log::error('RouteServiceProvider: Failed to load routes/api.php', [
                    'error' => $e->getMessage()
                ]);
            }

            Log::info('RouteServiceProvider: Loading routes/web.php');
            Route::middleware('web')
                 ->namespace($this->namespace)
                 ->group(base_path('routes/web.php'));
        });
    }
}