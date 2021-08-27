<?php

namespace InventorySDK\Api\Requests;

use GuzzleHttp\Client;
use InventorySDK\Api\Request;

class Items extends Request
{

    /**
     * @var Client
     */
    private $http;

    /**
     * @var string
     */
    private $module = 'items';

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

    /**
     * Fetches the details for an existing item.
     * https://www.zoho.com/inventory/api/v1/#Items_Retrieve_an_item
     *
     * @param int $itemId
     *
     * @return mixed
     * @throws \Exception
     */
    public function get($itemId)
    {
        if ($itemId) {
            $query = $this->http->getConfig('query') ?? [];
            $response = $this->request('get', $this->http, $this->module . '/' . $itemId, null, $query);

            $data = json_decode($response, true);
            $data['item']['response_code'] = $data['code'];

            return $data['item'];
        }

        throw new \Exception('Укажите корректный идентификатор [item id]');

    }

    /**
     * Creates a new item in Zoho Inventory.
     * https://www.zoho.eu/inventory/api/v1/#Items_Create_an_item
     *
     * @param array $options
     *
     * @return mixed
     * @throws \Exception
     */
    public function create(array $options)
    {
        if (!empty($options)) {

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];

            $response = $this->request('post', $this->http, $this->module, $formParams, null);

            $data = json_decode($response, true);
            $data['item']['response_code'] = $data['code'];

            return $data['item'];
        }

        throw new \Exception('Массив не может быть пустым, пожалуйста заполните его');

    }


    /**
     * Update the details of an item.
     * https://www.zoho.eu/inventory/api/v1/#Items_Update_an_item
     *
     * @param int $itemId
     * @param array $options
     *
     * @return mixed
     * @throws \Exception
     */
    public function update(int $itemId, array $options)
    {
        if (!empty($options) && $itemId) {
            $query = $this->http->getConfig('query') ?? [];
            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];

            $response = $this->request('put', $this->http, $this->module . '/' . $itemId, $formParams, $query);

            $data = json_decode($response, true);
            $data['item']['response_code'] = $data['code'];

            return $data['item'];
        }

        throw new \Exception('Массив или идентификатор не может быть пустым, пожалуйста заполните его');

    }

    /**
     * Deletes an existing item from Zoho Inventory.
     * https://www.zoho.eu/inventory/api/v1/#Items_Delete_an_item
     *
     * @param int $itemId
     *
     * @return mixed
     * @throws \Exception
     */
    public function delete(int $itemId)
    {
        if ($itemId) {
            $query = $this->http->getConfig('query') ?? [];
            $response = $this->request('delete', $this->http, $this->module . '/' . $itemId, null, $query);

            $data = json_decode($response, true);

            return $data['code'] === 0;
        }

        throw new \Exception('Укажите корректный идентификатор [item id]');
    }


    /**
     * Lists all the items present in Zoho Inventory
     * https://www.zoho.com/inventory/api/v1/#Items_List_all_the_items
     *
     * @param int $page
     *
     * @return array|mixed
     */
    public function listAll($page = 1)
    {
        $query = $this->http->getConfig('query') ?? [];
        $query = $query + ['page' => $page];

        $fullResponse = $this->request('get', $this->http, $this->module, null, $query);

        $data = json_decode($fullResponse, true);

        $this->storage = array_merge($this->storage, $data[$this->module]);

        $nextPage = array_key_exists('page_context', $data) && $data['page_context']['has_more_page'];

        return $nextPage ? $this->listAll($page + 1) : $this->storage;

    }

    /**
     * Mark Item As Active
     * @param int $itemId
     * @return mixed
     */
    public function markActive($itemId, $action = 'confirm')
    {
        $response = $this->request('post', $this->http, $this->module . '/' . $itemId . '/' . $action, null, null);
        $data = json_decode($response, true);

        return $data;

    }

    /**
     * Search contact by SKU in Zoho Inventory
     * @param $contactName
     * @param string $options
     * @return mixed
     */
    public function searchBySku($sku,$options=''){
        $config = $this->http->getConfig('query') ?? [];
        $query = $config + [
                'sku_contains' => $sku
            ];
        $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

        $formParams = ['JSONString' => $jsonData];
        $response = $this->request('get', $this->http, $this->module, $formParams, $query);

        $data = json_decode($response, true);

        return $data['items'];
    }
}
