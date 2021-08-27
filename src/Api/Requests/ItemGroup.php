<?php


namespace InventorySDK\Api\Requests;

use GuzzleHttp\Client;
use InventorySDK\Api\Request;

class ItemGroup extends Request
{
    /**
     * @var Client
     */
    private $http;

    /**
     * @var string
     */
    private $module = 'itemgroups';

    /**
     * @var array
     */
    private $storage = [];

    /**
     * Item constructor.
     *
     * @param Client $clientHttp
     */
    public function __construct($clientHttp)
    {
        $this->http = $clientHttp;
    }

    public function create(array $options)
    {
        if (!empty($options)) {
            try {
                $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

                $formParams = ['JSONString' => $jsonData];

                $response = $this->request('post', $this->http, $this->module, $formParams);

                $data = json_decode($response, true);
                $data['response_code'] = $data['code'];

                return $data['itemgroups'];
            }catch (\Exception $exception){

                return null;
            }
        }

        throw new \Exception('Массив не может быть пустым, пожалуйста заполните его');

    }

    public function searchByName($name,$options=''){
        $config = $this->http->getConfig('query') ?? [];
        $query = $config + [
                'group_name_contains' => $name
            ];
        $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

        $formParams = ['JSONString' => $jsonData];
        $response = $this->request('get', $this->http, $this->module, $formParams, $query);

        $data = json_decode($response, true);

        return $data['itemgroups'] ;
    }
}
