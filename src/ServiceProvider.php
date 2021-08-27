<?php
/**
 * Created by PhpStorm.
 * User: Михаил
 * Date: 17.04.2020
 * Time: 13:53
 */

namespace InventorySDK;

use Illuminate\Routing\Router;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot(Router $router){
        $this->publishes([
            __DIR__.'/../config' => config_path('zoho-inventory'),
        ]);
    }
}