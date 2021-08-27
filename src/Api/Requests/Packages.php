<?php


namespace InventorySDK\Api\Requests;

use GuzzleHttp\Client;
use InventorySDK\Api\Request;

class Packages extends Request
{

    /**
     * @var Client
     */
    private $http;

    /**
     * @var string
     */
    private $module = 'packages';

    /**
     * @var array
     */
    private $storage = [];

    /**
     * SalesOrders constructor.
     *
     * @param Client $clientHttp
     */
    public function __construct($clientHttp)
    {
        $this->http = $clientHttp;
    }

    /**
     * Retrieves the details for an existing Package
     *
     * @param int $packageId
     * @return mixed
     * @throws \Exception
     */
    public function get($packageId)
    {
        if ($packageId) {

            $response = $this->request('get', $this->http, $this->module . '/' . $packageId, null, null);
            $data = json_decode($response, true);
            $data['package']['response_code'] = $data['code'];

            return $data['package'];
        }

        throw new \Exception('Укажите корректный идентификатор [package id]');

    }


    /**
     * Lists all the available Sales Orders in Zoho Inventory.
     * https://www.zoho.com/inventory/api/v1/#Sales_Orders_List_all_Sales_Orders
     *
     * @param bool $all
     * @param int $page
     * @return array|mixed
     */

    public function listAll($all = false, $page = 1)
    {

        if ($all) {

            $query = $this->http->getConfig('query') ?? [];
            $query = $query + ['page' => $page];

            $fullResponse = $this->request('get', $this->http, $this->module, null, $query);
            $data = json_decode($fullResponse, true);

            $this->storage = array_merge($this->storage, $data[$this->module]);

            $nextPage = array_key_exists('page_context', $data) && $data['page_context']['has_more_page'];

            return $nextPage ? $this->listAll(true, $page + 1) : $this->storage;

        }

        $response = $this->request('get', $this->http, $this->module, null, null);

        $data = json_decode($response, true);

        $success = $data['message'] == 'success';

        return $success ? $data[$this->module] : $data;

    }

    /**
     * Create Package in Zoho Inventory
     * @param $options
     * @param $salesOrderId
     * @return mixed
     * @throws \Exception
     */
    public function create($options, $salesOrderId)
    {
        if (!empty($options)) {

            $config = $this->http->getConfig('query');
            $query = $config + [
                    'ignore_auto_number_generation' => 'false',
                    'salesorder_id' => $salesOrderId
                ];

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];

            $response = $this->request('post', $this->http, $this->module, $formParams, $query);
            $data = json_decode($response, true);
            $data['package']['response_code'] = $data['code'];

            return $data['package'];
        }

        throw new \Exception('Массив не может быть пустым, пожалуйста заполните его');

    }

}
