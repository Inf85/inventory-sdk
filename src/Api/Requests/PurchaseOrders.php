<?php

namespace InventorySDK\Api\Requests;

use GuzzleHttp\Client;
use InventorySDK\Api\Request;

class PurchaseOrders extends Request
{

    /**
     * @var Client
     */
    private $http;

    /**
     * @var string
     */
    private $module = 'purchaseorders';

    /**
     * @var array
     */
    private $storage = [];

    /**
     * PurchaseOrders constructor.
     * @param Client $clientHttp
     */
    public function __construct($clientHttp)
    {
        $this->http = $clientHttp;
    }

    /**
     * Retrieves the details for an existing Purchase Order
     * https://www.zoho.com/inventory/api/v1/#Purchase_Orders_Retrieve_a_Purchase_Order
     *
     * @param int $purchaseOrderId
     * @return mixed
     * @throws \Exception
     */
    public function get($purchaseOrderId)
    {
        if ($purchaseOrderId) {

            $response = $this->request('get', $this->http, $this->module . '/' . $purchaseOrderId, null, null);
            $data = json_decode($response, true);
            $data['purchaseorder']['response_code'] = $data['code'];

            return $data['purchaseorder'];
        }

        throw new \Exception('Укажите корректный идентификатор [purchaseOrder id]');

    }

    /**
     * Creates a new Sales Order in Zoho Inventory.
     * Description about extra parameter ignore_auto_number_generation :
     * Ignore auto sales order number generation for this Purchase order.
     * This mandates the Purchase Order number to be entered. Allowed Values: true and false.
     * https://www.zoho.eu/inventory/api/v1/#Items_Create_an_item
     *
     * @param array $options
     *
     * @return mixed
     * @throws \Exception
     */
    public function create($options)
    {
        if (!empty($options)) {

            $config = $this->http->getConfig('query');

            // Ignore auto sales order number generation for this Purchase order [bool]
            $query = $config + ['ignore_auto_number_generation' => 'false'];

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];
            $response = $this->request('post', $this->http, $this->module, $formParams, $query);
            $data = json_decode($response, true);
            $data['purchaseorder']['response_code'] = $data['code'];

            return $data['purchaseorder'];
        }

        throw new \Exception('Массив не может быть пустым, пожалуйста заполните его');

    }


    /**
     * Updates a new Sales Order in Zoho Inventory.
     * Description about extra parameter ignore_auto_number_generation :
     * Ignore auto Purchase order number generation for this Purchase Order.
     * This mandates the Purchase Order number to be entered. Allowed Values: true and false.
     * https://www.zoho.com/inventory/api/v1/#Purchase_Orders_Update_a_Purchase_Order
     *
     * @param int $purchaseOrderId
     * @param array $options
     *
     * @return mixed
     * @throws \Exception
     */
    public function update($purchaseOrderId, array $options)
    {
        if (!empty($options) && $purchaseOrderId) {

            $config = $this->http->getConfig('query');

            // Ignore auto sales order number generation for this Purchase order [bool]
            $query = $config + ['ignore_auto_number_generation' => 'false'];

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];

            $response = $this->request('put', $this->http, $this->module . '/' . $purchaseOrderId, $formParams, $query);
            $data = json_decode($response, true);
            $data['purchaseorder']['response_code'] = $data['code'];

            return $data['purchaseorder'];
        }

        throw new \Exception('Массив или идентификатор не может быть пустым, пожалуйста заполните его');

    }

    /**
     * Deletes a Purchase Order from Zoho Inventory.
     * https://www.zoho.com/inventory/api/v1/#Purchase_Orders_Delete_a_Purchase_Order
     *
     * @param int $purchaseOrderId
     *
     * @return mixed
     * @throws \Exception
     */
    public function delete(int $purchaseOrderId)
    {
        if ($purchaseOrderId) {

            $response = $this->request('delete', $this->http, $this->module . '/' . $purchaseOrderId, null, null);
            $data = json_decode($response, true);

            return $data['code'] === 0;
        }

        throw new \Exception('Укажите корректный идентификатор [purchase order id]');
    }


    /**
     * Lists all the Purchase Orders present in Zoho Inventory.
     * https://www.zoho.com/inventory/api/v1/#Purchase_Orders_List_all_Purchase_Orders
     *
     * @param int $page
     *
     * @return array|mixed
     */
    /**
     * [Each page has 200 items]
     * contacts()->listAll() - get all invoices
     */
    public function listAll($page = 1)
    {
        $query = $this->http->getConfig('query') ?? [];
        $query = $query + ['page' => $page];

        $response = $this->request('get', $this->http, $this->module, null, $query);
        $data = json_decode($response, true);

        $this->storage = array_merge($this->storage, $data[$this->module]);

        $nextPage = array_key_exists('page_context', $data) && $data['page_context']['has_more_page'];

        return $nextPage ? $this->listAll($page + 1) : $this->storage;
    }


    /**
     * Upload Attchment to Purchase Order
     * @param $filename
     * @param $id
     * @return mixed
     */
    public function uploadAttachment($filename, $id)
    {
        $query = $this->http->getConfig('query') ?? [];
        $query = $query + [
                'attachment' => $filename
            ];

        $response = $this->request('post', $this->http, $this->module . '/' . $id . '/attachment', null, $query);
        return $response['message'];

    }

    public function changeToCanceled($purchaseOrderId)
    {
        if ($purchaseOrderId) {

            $response = $this->request('post', $this->http, $this->module . '/' . $purchaseOrderId . '/status/cancelled', null, null);
            $data = json_decode($response, true);

            return $data['code'] === 0;
        }

        throw new \Exception('Укажите корректный идентификатор [sales order id]');
    }


}
