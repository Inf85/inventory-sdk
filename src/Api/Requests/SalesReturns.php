<?php


namespace InventorySDK\Api\Requests;

use GuzzleHttp\Client;
use InventorySDK\Api\Request;

class SalesReturns extends Request
{
    /**
     * @var Client
     */
    private $http;

    /**
     * @var string
     */
    private $module = 'salesreturns';

    /**
     * @var array
     */

    private $storage = [];

    public function __construct($clientHttp)
    {
        $this->http = $clientHttp;
    }


    /**
     * Получаем запись из модуля SalesReturn
     */
    public function get($salesReturnId)
    {
        if ($salesReturnId) {

            $response = $this->request('get', $this->http, $this->module . '/' . $salesReturnId, null, null);
            $data = json_decode($response, true);
            $data['salesreturn']['response_code'] = $data['code'];

            return $data['salesreturn'];
        }

        throw new \Exception('Укажите корректный идентификатор [sales order id]');

    }

    /**
     * Get List of all available SalesReturns From Zoho Inventory
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
     * Create Sales Return record in Zoho Inventory
     * @param $options
     * @param $salesOrderId
     * @param bool $autoIncrement
     * @return mixed
     * @throws \Exception
     */
    public function create($options, $salesOrderId, $autoIncrement = false)
    {
        if (!empty($options)) {

            $config = $this->http->getConfig('query') ?? [];
            // Ignore auto sales order number generation for this sales order [bool]
            $query = $config + [
                    'ignore_auto_number_generation' => $autoIncrement ? 'false' : 'true',
                    'salesorder_id' => $salesOrderId,
                ];

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];

            $response = $this->request('post', $this->http, $this->module, $formParams, $query);
            $data = json_decode($response, true);
            $data['salesreturn']['response_code'] = $data['code'];

            return $data['salesreturn'];
        }

        throw new \Exception('Массив не может быть пустым, пожалуйста заполните его');


    }

    /**
     * Create Sales Return Recieve
     * @param $options
     * @param $salesReturnId
     * @param bool $autoIncrement
     * @return mixed
     * @throws \Exception
     */
    public function createRecieves($options, $salesReturnId, $autoIncrement = false)
    {
        if (!empty($options)) {

            $config = $this->http->getConfig('query') ?? [];
            // Ignore auto sales order number generation for this sales order [bool]
            $query = $config + [
                    'ignore_auto_number_generation' => $autoIncrement ? 'false' : 'true',
                    'salesreturn_id' => $salesReturnId,
                ];

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];

            $response = $this->request('post', $this->http, 'salesreturnreceives', $formParams, $query);
            $data = json_decode($response, true);

            return $data;
        }

        throw new \Exception('Массив не может быть пустым, пожалуйста заполните его');

    }

    /**
     * Create Return Note
     * @param $options
     * @param $salesReturnId
     * @param bool $autoIncrement
     * @return mixed
     * @throws \Exception
     */
    public function createReturnNotes($options, $salesReturnId, $autoIncrement = false)
    {
        if (!empty($options)) {

            $config = $this->http->getConfig('query') ?? [];
            // Ignore auto sales order number generation for this sales order [bool]
            $query = $config + [
                    'ignore_auto_number_generation' => $autoIncrement ? 'false' : 'true',
                    'salesreturn_id' => $salesReturnId,
                ];

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];

            $response = $this->request('post', $this->http, 'creditnotes', $formParams, $query);
            $data = json_decode($response, true);

            return $data;
        }

        throw new \Exception('Массив не может быть пустым, пожалуйста заполните его');
    }
}
