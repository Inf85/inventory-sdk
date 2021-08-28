<?php
return [
    'client_id' => env('ZOHO_INVENTORY_CLIENT_ID', ''),
    'client_secret' => env('ZOHO_INVENTORY_CLIENT_SECRET', ''),
    'refresh_token' => env('ZOHO_INVENTORY_REFRESH_TOKEN', ''),
    'redirect_uri' => '',
    'base_uri' => 'https://inventory.zoho.com/api/v1/'
];
