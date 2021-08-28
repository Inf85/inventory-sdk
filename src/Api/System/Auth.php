<?php


namespace InventorySDK\Api\System;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class Auth
{
    public static function getAccessToken()
    {
        $token = Redis::get('inventory-token');

        return $token ? $token : self::refreshAccessToken();
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private static function refreshAccessToken()
    {

        $query = [
            'grant_type' => 'refresh_token',
            'client_id' => config('zoho-inventory.app.client_id'),
            'client_secret' => config('zoho-inventory.app.client_secret'),
            'refresh_token' => config('zoho-inventory.app.refresh_token'),
            'redirect_uri' => config('zoho-inventory.app.redirect_uri'),
        ];

        $client = new Client();
        $response = $client->request('POST', 'https://accounts.zoho.'.config('zoho-inventory.app.zoho_domain').'/oauth/v2/token', ['query' => $query]);
        $parsed = json_decode($response->getBody()->getContents(), JSON_OBJECT_AS_ARRAY);
        if ($response->getStatusCode() !== 200 || !isset($parsed['access_token'])) {
           Log::info('Invalid response on token refresh request. Response: ' . print_r($parsed, true));
            throw new \Exception('Invalid response on token refresh request. Response: ' . print_r($parsed, true));
        } else {
            Redis::set('inventory-token', $parsed['access_token']);
            Redis::expire('inventory-token', 3500);
            return $parsed['access_token'];

        }

    }
}
