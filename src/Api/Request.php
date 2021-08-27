<?php

namespace InventorySDK\Api;

use GuzzleHttp\Client;
use InventorySDK\Api\System\Auth;

class Request
{

    public $httpClient = null;

    private static $instance = null;


    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __clone()
    {
    }

    private function __construct()
    {
    }

    public function getHttpClient($options = [])
    {
        if ($this->httpClient === null) {
            $this->httpClient = new Client($options);
        }
        return $this->httpClient;
    }

    public function request($method, $http, $url, $formParams = null, $query = null)
    {
        $params = [];
        $headsers = [
            'Authorization' => 'Zoho-oauthtoken '. Auth::getAccessToken(),
            'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
        ];
        if (!is_null($query)) {
            $params['query'] = $query;
        }
        if (!is_null($formParams)) {
            $params['form_params'] = $formParams;
        }
        return $http->{$method}($url, $params)
            ->getBody()
            ->getContents();
    }

}
