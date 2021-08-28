<?php

namespace InventorySDK\Api\Requests;

use GuzzleHttp\Client;
use InventorySDK\Api\Request;

class PurchaseReceives extends Request
{

    /**
     * @var Client
     */
    private $http;

    /**
     * @var string
     */
    private $module = 'purchasereceives';

    /**
     * @var array
     */
    private $storage = [];

    /**
     * PurchaseReceives constructor.
     *
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
    public function get($purchaseReceiveId)
    {
        if ($purchaseReceiveId) {

            $response = $this->request('get', $this->http, $this->module . '/' . $purchaseReceiveId, null, null);
            $data = json_decode($response, true);
            $data['purchasereceive']['response_code'] = $data['code'];

            return $data['purchasereceive'];
        }

        throw new \Exception('Укажите корректный идентификатор [purchase receive id]');

    }

    /**
     * A new purchase receive can a be created.
     * To create Purchase receive, URL parameter purchaseorder_id is needed.
     * https://www.zoho.com/inventory/api/v1/#Purchase_Receives_Create_a_purchase_receive
     *
     * @param int $purchaseReceiveId
     * @param array $options
     *
     * @return mixed
     * @throws \Exception
     */
    public function create($purchaseReceiveId, $options)
    {
        if (!empty($options) && $purchaseReceiveId) {

            $config = $this->http->getConfig('query') ?? [];

            // Ignore auto sales order number generation for this Purchase order [bool]
            $query = $config + [
                    'ignore_auto_number_generation' => 'false',
                    'purchaseorder_id' => $purchaseReceiveId
                ];

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];

            $response = $this->request('post', $this->http, $this->module, $formParams, $query);
            $data = json_decode($response, true);
            $data['purchasereceive']['response_code'] = $data['code'];

            return $data;
        }

        throw new \Exception('Массив не может быть пустым, пожалуйста заполните его');

    }

    /**
     * Deletes a Purchase Receive from Zoho Inventory
     * https://www.zoho.com/inventory/api/v1/#Purchase_Receives_Delete_a_Purchase_Receive
     *
     * @param int $purchaseReciveId
     *
     * @return mixed
     * @throws \Exception
     */
    public function delete(int $purchaseReciveId)
    {
        if ($purchaseReciveId) {

            $response = $this->request('delete', $this->http, $this->module . '/' . $purchaseReciveId, null, null);
            $data = json_decode($response, true);

            return $data['code'] === 0;
        }

        throw new \Exception('Укажите корректный идентификатор [purchase receive id]');
    }

}
