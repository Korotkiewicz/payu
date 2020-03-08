<?php

namespace Korotkiewicz\PayU;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;


class PayUServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(){
        $this->app->singleton('Korotkiewicz\PayU\PayUServiceProvider', function($app) {
            $productionMode = config('payu.production_mode') ? 'secure' : 'sandbox';
            $merchantId = config('payu.merchant_id');
            $signatureKey = config('payu.signature_key');
            $clientId = config('payu.client_id');
            $clientSecret = config('payu.client_secret'); 
           
            return new PayU($productionMode, $merchantId, $signatureKey, $clientId, $clientSecret);
        });

        $this->mergeConfigFrom(
            __DIR__ . '/config/payu.php', 'payu'
        );
    }

    /**
     * Publish the plugin configuration.
     */
    public function boot(){
        $this->publishes([
            __DIR__ . '/config/payu.php' => config_path('payu.php')
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(){
        return [
            'Korotkiewicz\PayU',
        ];
    }

}