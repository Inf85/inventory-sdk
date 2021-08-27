<?php


namespace InventorySDK\Api\Requests;

use GuzzleHttp\Client;
use InventorySDK\Api\Request;

class ShipmentOrders extends Request
{

    /**
     * @var Client
     */
    private $http;

    /**
     * @var string
     */
    private $module = 'shipmentorders';

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
     * Create Shipment Order in Zoho Inventory
     * @param $options
     * @param $salesOrderId
     * @param $packagesIds
     * @param string $tracking
     * @return mixed
     * @throws \Exception
     */
    public function create($options, $salesOrderId, $packagesIds, $tracking = 'false')
    {
        if (!empty($options)) {

            $config = $this->http->getConfig('query');

            $query = $config + [
                    'salesorder_id' => $salesOrderId,
                    'package_ids' => $packagesIds,
                    'is_tracking_required' => $tracking
                ];

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];

            $response = $this->request('post', $this->http, $this->module, $formParams, $query);
            $data = json_decode($response, true);

            return $data;
        }

        throw new \Exception('Массив не может быть пустым, пожалуйста заполните его');

    }

    /**
     * Delete Shipment Order in Zoho Inventory
     * @param $shipmentOrderId
     * @return mixed
     * @throws \Exception
     */
    public function delete($shipmentOrderId)
    {
        if ($shipmentOrderId) {

            $response = $this->request('delete', $this->http, $this->module . '/' . $shipmentOrderId, null, null);
            $data = json_decode($response, true);

            return $data;
        }

        throw new \Exception('Укажите корректный идентификатор [shipmentOrder id ]');
    }


    /**
     * Enable Tracking for Package
     * @param $options
     * @param $shipmentOrderId
     * @return mixed
     * @throws \Exception
     */
    public function enableTracking($options, $shipmentOrderId)
    {
        if (!empty($options) && $shipmentOrderId) {

            $config = $this->http->getConfig('query');
            $query = $config;

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];

            $response = $this->request('post', $this->http, $this->module . '/' . $shipmentOrderId . '/enable/tracking', $formParams, $query);
            $data = json_decode($response, true);

            return $data;
        }
        throw new \Exception('Укажите корректный идентификатор [shipmentOrder id ]');
    }

}
