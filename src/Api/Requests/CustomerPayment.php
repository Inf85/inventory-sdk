<?php
/**
 * Created by PhpStorm.
 * User: Михаил
 * Date: 22.04.2019
 * Time: 15:15
 */

namespace InventorySDK\Api\Requests;

use GuzzleHttp\Client;
use InventorySDK\Api\Request;

class CustomerPayment extends Request
{
    /**
     * @var Client
     */
    private $http;

    /**
     * @var string
     */
    private $module = 'customerpayments';

    /**
     * @var array
     */
    private $storage = [];

    /**
     * Invoices constructor.
     *
     * @param Client $clientHttp
     */
    public function __construct($clientHttp)
    {
        $this->http = $clientHttp;
    }

    /**
     * Retrieves the details for an existing Customer Payment
     *
     * @param $customerPaymentId
     * @return mixed
     * @throws \Exception
     */
    public function get($customerPaymentId)
    {
        if ($customerPaymentId) {

            $response = $this->request('get', $this->http, $this->module . '/' . $customerPaymentId, null, null);
            $data = json_decode($response, true);
            $data['payment']['response_code'] = $data['code'];

            return $data['payment'];
        }

        throw new \Exception('Укажите корректный идентификатор [purchaseOrder id]');

    }

    /**Lists all the available Customer Payments in Zoho Inventory.
     * @param int $page
     * @return array|mixed
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
     * Create new Customer Payment in Zoho Inventory
     * @param $options
     * @return mixed
     * @throws \Exception
     */
    public function create($options)
    {
        if (!empty($options)) {

            $config = $this->http->getConfig('query');

            $query = $config + [
                    // Ignore auto sales order number generation for this sales order [bool]
                    'ignore_auto_number_generation' => 'false',

                    //Send the invoice to the contact person(s) associated with the invoice.[bool]

                ];

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];

            $response = $this->request('post', $this->http, $this->module, $formParams, $query);
            $data = json_decode($response, true);
            $data['payment']['response_code'] = $data['code'];

            return $data['payment'];
        }

        throw new \Exception('Массив не может быть пустым, пожалуйста заполните его');

    }


    /**
     * Delete Customer Payment From Zoho Inventory
     * @param $invoicePaymentId
     * @return mixed
     * @throws \Exception
     */
    public function deletePayment($invoicePaymentId)
    {
        if ($invoicePaymentId) {


            $response = $this->request('delete', $this->http, $this->module. '/' . $invoicePaymentId, null, null);
            $data = json_decode($response, true);

            return $data['code'];
        }

        throw new \Exception('Укажите корректный идентификатор [invoice id или invoice payment id]');
    }
}
