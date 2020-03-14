<?php

namespace Korotkiewicz\PayU;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use \Illuminate\Contracts\Foundation\Application;


class PayUServiceProvider extends ServiceProvider {

    /**
     * Create a new service provider instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

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
            $continueUrl = config('payu.continue_url'); 
            $notifyUrl = config('payu.notify_url'); 
            $shopName = config('payu.shop_name'); 
           
            return new PayU($productionMode, $merchantId, $signatureKey, $clientId, $clientSecret, $continueUrl, $notifyUrl, $shopName);
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
            'Korotkiewicz\PayU\PayUServiceProvider',
        ];
    }

}