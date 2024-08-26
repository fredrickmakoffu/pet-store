<?php

namespace App\Providers;

use App\Services\ManageJwtTokens;
use App\Contracts\Auth\AuthTokenInterface;
use App\Guards\JwtGuard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
      $this->registerPolicies();

      // bind the interface to the implementation
			$this->app->bind(AuthTokenInterface::class, function (Application $app) {
				return new ManageJwtTokens($app->make('auth')->createUserProvider('users'));
			});

			// extend the auth guard
			Auth::extend('jwt', function (Application $app, string $name, array $config) {
				return new JwtGuard(
					Auth::createUserProvider($config['provider']),
					$app->make(AuthTokenInterface::class)
				);
			});
    }
}
