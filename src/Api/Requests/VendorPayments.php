<?php


namespace InventorySDK\Api\Requests;

use GuzzleHttp\Client;
use InventorySDK\Api\Request;

class VendorPayments extends Request
{
    /**
     * @var Client
     */
    private $http;

    /**
     * @var string
     */
    private $module = 'vendorpayments';

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


    public function create($options)
    {
        if (!empty($options)) {

            $config = $this->http->getConfig('query') ?? [];

            $query = $config + [
                    // Ignore auto sales order number generation for this sales order [bool]
                    'ignore_auto_number_generation' => 'false',

                    //Send the invoice to the contact person(s) associated with the invoice.[bool]

                ];

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];

            $response = $this->request('post', $this->http, $this->module, $formParams, $query);
            $data = json_decode($response, true);
            $data['vendorpayment']['response_code'] = $data['code'];
            $data['vendorpayment']['response_message'] = $data['message'];
            return $data['vendorpayment'];
        }

        throw new \Exception('Массив не может быть пустым, пожалуйста заполните его');

    }

    public function deletePayment($billPaymentId)
    {
        if ($billPaymentId) {

            $response = $this->request('delete', $this->http, $this->module . '/' . $billPaymentId, null, null);
            $data = json_decode($response, true);

            return $data;
        }

        throw new \Exception('Укажите корректный идентификатор [ payment id]');
    }

}
