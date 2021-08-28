<?php


namespace InventorySDK\Api\Requests;

use GuzzleHttp\Client;
use InventorySDK\Api\Request;

class Invoices extends Request
{

    /**
     * @var Client
     */
    private $http;

    /**
     * @var string
     */
    private $module = 'invoices';

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
     * Get the details of an invoice.
     * https://www.zoho.com/inventory/api/v1/#Invoices_Get_an_invoice
     *
     * @param int $invoiceId
     *
     * @return mixed
     * @throws \Exception
     */
    public function get($invoiceId)
    {
        if ($invoiceId) {

            $response = $this->request('get', $this->http, $this->module . '/' . $invoiceId, null, null);
            $data = json_decode($response, true);
            $data['invoice']['response_code'] = $data['code'];

            return $data['invoice'];
        }

        throw new \Exception('Укажите корректный идентификатор [invoice id]');

    }


    /**
     * Create an invoice for your customer.
     * https://www.zoho.com/inventory/api/v1/#Invoices_Create_an_invoice
     *
     * @param array $options
     *
     * @return mixed
     * @throws \Exception
     */
    /**
     *
     * Example of options:
     * $options = [
     *      "customer_id"     => 1130564000000086001,
     *      "invoice_number"  => 777777770,
     *      "line_items"      => [
     *                 [
     *                  "item_id"     => 1130564000000074865,
     *                  "name"        => "Real Seven",
     *                  "description" => "Real Ninjas in pyjamas",
     *
     *                  ],
     *                  [
     *                  "item_id"     => 1130564000000074857,
     *                  "name"        => "Real Eight",
     *                  "description" => "Turtles",
     *                  ]
     *                ]
     *          ];
     */
    public function create($options)
    {
        if (!empty($options)) {

            $config = $this->http->getConfig('query') ?? [];

            $query = $config + [
                    // Ignore auto sales order number generation for this sales order [bool]
                    'ignore_auto_number_generation' => 'false',

                    //Send the invoice to the contact person(s) associated with the invoice.[bool]
                    'send' => 'false'
                ];

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];

            $response = $this->request('post', $this->http, $this->module, $formParams, $query);
            $data = json_decode($response, true);
            $data['invoice']['response_code'] = $data['code'];

            return $data['invoice'];
        }

        throw new \Exception('Массив не может быть пустым, пожалуйста заполните его');

    }

    /**
     * Update an existing invoice. To delete a line item just remove it from the line_items list.
     * https://www.zoho.com/inventory/api/v1/#Invoices_Update_an_invoice
     *
     * @param int $invoiceId
     * @param array $options
     *
     * @return mixed
     * @throws \Exception
     */
    /**
     *
     * Example of options:
     * $options = [
     *              "customer_id"     => 1130564000000086001,
     *              "invoice_number"  => 777777770,
     *              "reason"          => "New important changes",
     *              "line_items"      => [
     *                                      [
     *                                          "item_id"     => 1130564000000074865,
     *                                          "name"        => "Real Seven",
     *                                          "description" => "Real Ninjas in pyjamas",
     *
     *                                      ],
     *                                      [
     *                                          "item_id"     => 1130564000000074857,
     *                                          "name"        => "Real Eight",
     *                                          "description" => "Turtles",
     *
     *                                      ]
     *                                     ]
     *          ];
     */
    public function update($invoiceId, $options)
    {
        if (!empty($options) && $invoiceId) {

            $config = $this->http->getConfig('query') ?? [];

            // Ignore auto sales order number generation for this sales order [bool]
            $query = $config + ['ignore_auto_number_generation' => 'true'];

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];

            $response = $this->request('put', $this->http, $this->module . '/' . $invoiceId, $formParams, $query);
            $data = json_decode($response, true);
            $data['invoice']['response_code'] = $data['code'];

            return $data['invoice'];
        }

        throw new \Exception('Массив или идентификатор не может быть пустым, пожалуйста заполните его');

    }

    /**
     * Delete an existing invoice. Invoices which have payment or credits note applied cannot be deleted.
     * https://www.zoho.com/inventory/api/v1/#Invoices_Delete_an_invoice
     *
     * @param int $invoiceId
     *
     * @return mixed
     * @throws \Exception
     */
    public function delete($invoiceId)
    {
        if ($invoiceId) {

            $response = $this->request('delete', $this->http, $this->module . '/' . $invoiceId, null, null);
            $data = json_decode($response, true);

            return $data['code'];
        }

        throw new \Exception('Укажите корректный идентификатор [invoice id]');
    }

    /**
     * List all invoices with pagination.
     * https://www.zoho.com/inventory/api/v1/#Invoices_List_invoices
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
     * Change status of Invoice to Void
     * @param $invoiceId
     * @return bool
     * @throws \Exception
     */
    public function changeStatusToVoid($invoiceId)
    {
        if ($invoiceId) {

            $response = $this->request('post', $this->http, $this->module . '/' . $invoiceId . '/status/void', null, null);
            $data = json_decode($response, true);

            return $data['code'] === 0;
        }

        throw new \Exception('Укажите корректный идентификатор [invoice id]');
    }

    /**
     * Change status of Invoice to writeOff
     * @param $invoiceId
     * @return bool
     * @throws \Exception
     */
    public function writeOff($invoiceId)
    {
        if ($invoiceId) {

            $response = $this->request('post', $this->http, $this->module . '/' . $invoiceId . '/writeoff', null, null);
            $data = json_decode($response, true);

            return $data['code'] === 0;
        }

        throw new \Exception('Укажите корректный идентификатор [invoice id]');
    }


}
