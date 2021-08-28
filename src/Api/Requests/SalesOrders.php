<?php


namespace InventorySDK\Api\Requests;

use GuzzleHttp\Client;
use InventorySDK\Api\Request;

class SalesOrders extends Request
{

    /**
     * @var Client
     */
    private $http;

    /**
     * @var string
     */
    private $module = 'salesorders';

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
     * Retrieves the details for an existing Sales Order
     * https://www.zoho.com/inventory/api/v1/#Sales_Orders_Retrieve_a_Sales_Order
     *
     * @param int $salesOrderId
     * @return mixed
     * @throws \Exception
     */
    public function get($salesOrderId)
    {
        if ($salesOrderId) {

            $response = $this->request('get', $this->http, $this->module . '/' . $salesOrderId, null, null);
            $data = json_decode($response, true);
            $data['salesorder']['response_code'] = $data['code'];

            return $data['salesorder'];
        }

        throw new \Exception('Укажите корректный идентификатор [sales order id]');

    }

    /**
     * Creates a new Sales Order in Zoho Inventory.
     * https://www.zoho.com/inventory/api/v1/#Sales_Orders_Create_a_Sales_Order
     *
     * @param array $options
     * @param bool $autoIncrement
     * @return mixed
     * @throws \Exception
     */
    public function create(array $options, $autoIncrement = true)
    {
        if (!empty($options)) {

            $config = $this->http->getConfig('query') ?? [];
            // Ignore auto sales order number generation for this sales order [bool]
            $query = $config + ['ignore_auto_number_generation' => $autoIncrement ? 'false' : 'true'];

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];
            $response = $this->request('post', $this->http, $this->module, $formParams, $query);
            $data = json_decode($response, true);
            $data['salesorder']['response_code'] = $data['code'];

            return $data['salesorder'];
        }

        throw new \Exception('Массив не может быть пустым, пожалуйста заполните его');

    }

    /**
     * Updates a new Sales Order in Zoho Inventory.
     * Important: Sales orders that have been shipped or on hold cannot be updated.
     * https://www.zoho.com/inventory/api/v1/#Sales_Orders_Update_a_Sales_Order
     *
     * @param int $salesOrderId
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function update(int $salesOrderId, array $options)
    {
        if (!empty($options) && $salesOrderId) {

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];

            $response = $this->request('put',$this->http, $this->module . '/' . $salesOrderId, $formParams, null);
            $data = json_decode($response, true);
            $data['salesorder']['response_code'] = $data['code'];

            return $data['salesorder'];
        }

        throw new \Exception('Массив или идентификатор не может быть пустым, пожалуйста заполните его');

    }

    /**
     * Deletes an existing Sales Order from Zoho Inventory.
     * Important: Sales orders marked for drop shipment or backorder cannot be deleted.
     * https://www.zoho.com/inventory/api/v1/#Sales_Orders_Delete_a_Sales_Order
     *
     * @param int $contactId
     * @return mixed
     * @throws \Exception
     */
    public function delete(int $salesOrderId)
    {
        if ($salesOrderId) {

            $response = $this->request('delete', $this->http, $this->module . '/' . $salesOrderId, null, null);
            $data = json_decode($response, true);

            return $data['code'] === 0;
        }

        throw new \Exception('Укажите корректный идентификатор [sales order id]');
    }

    /**
     * Lists all the available Sales Orders in Zoho Inventory.
     * https://www.zoho.com/inventory/api/v1/#Sales_Orders_List_all_Sales_Orders
     *
     * @param bool $all
     * @param int $page
     * @return array|mixed
     */
    /**
     * [Each page has 200 items]
     * contacts()->listAll() - get first page of contacts
     * contacts()->listAll(false, 2) - get second page of contacts and etc.
     * contacts()->listAll(true) - get all contacts
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

    public function changeToVoid($salesOrderId)
    {
        if ($salesOrderId) {

            $response = $this->request('post', $this->http, $this->module . '/' . $salesOrderId . '/status/void', null, null);
            $data = json_decode($response, true);

            return $data['code'] === 0;
        }

        throw new \Exception('Укажите корректный идентификатор [sales order id]');
    }
}
