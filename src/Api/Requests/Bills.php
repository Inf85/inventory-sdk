<?php

namespace InventorySDK\Api\Requests;

use GuzzleHttp\Client;
use InventorySDK\Api\Request;

class Bills extends Request
{

    /**
     * @var Client
     */
    private $http;

    /**
     * @var string
     */
    private $module = 'bills';

    /**
     * @var array
     */
    private $storage = [];

    /**
     * Bills constructor.
     *
     * @param Client $clientHttp
     */
    public function __construct($clientHttp)
    {
        $this->http = $clientHttp;
    }

    /**
     * Fetches the details of a Bill.
     * https://www.zoho.com/inventory/api/v1/#Bills_Retrieve_a_Bill
     *
     * @param int $billId
     *
     * @return mixed
     * @throws \Exception
     */
    public function get($billId)
    {
        if ($billId) {

            $response = $this->request('get', $this->http, $this->module . '/' . $billId, null, null);

            $data = json_decode($response, true);
            $data['bill']['response_code'] = $data['code'];

            return $data['bill'];
        }

        throw new \Exception('Укажите корректный идентификатор [bill id]');

    }

    /**
     * Creates a Bill in Zoho Inventory.
     * Description about the extra parameter attachment : Allowed extensions are gif, png, jpeg, jpg, bmp and pdf.
     * https://www.zoho.com/inventory/api/v1/#Bills_Create_a_Bill
     *
     * @param array $options
     *
     * @return mixed
     * @throws \Exception
     */
    /*
     * Example:
      $options = [
            'vendor_id' => '1072331000000861022',
            'bill_number' => 'BL-00002',
            'date' => '2018-03-31',
            'due_date' => '2018-03-31',
            'line_items'     => [
                [
                    'purchaseorder_item_id'=> '',
                    'receive_item_id' => '',
                    'item_id' => '1072331000000766643',
                    'name' => 'test-test',
                    'description' => '',
                    'item_order' => 0,
                    'quantity' => 1.0,
                    'unit' => 'Sqm'
                ]
            ],

        ];
     */
    public function create($options)
    {
        if (!empty($options)) {
            $config = $this->http->getConfig('query');

            $query = $config + [

                ];

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];
            $response = $this->request('post', $this->http, $this->module, $formParams, $query);

            $data = json_decode($response, true);
            $data['bill']['response_code'] = $data['code'];

            return $data['bill'];
        }

        throw new \Exception('Массив не может быть пустым, пожалуйста заполните его');

    }

    /**
     * Updates the details of an existing Bill.
     * https://www.zoho.com/inventory/api/v1/#Bills_Update_a_Bill
     *
     * @param int $billId
     * @param array $options
     *
     * @return mixed
     * @throws \Exception
     */
    public function update($billId, $options)
    {
        if (!empty($options) && $billId) {
            $config = $this->http->getConfig('query');

            $query = $config;

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];

            $response = $this->request('put', $this->http, $this->module . '/' . $billId, $formParams, $query);


            $data = json_decode($response, true);
            $data['bill']['response_code'] = $data['code'];

            return $data['bill'];
        }

        throw new \Exception('Массив или идентификатор не может быть пустым, пожалуйста заполните его');

    }

    /**
     * Deletes a Bill from Zoho Inventory.
     * https://www.zoho.com/inventory/api/v1/#Bills_Delete_a_Bill.
     *
     * @param int $billId
     *
     * @return mixed
     * @throws \Exception
     */
    public function delete($billId)
    {
        if ($billId) {

            $response = $this->request('delete', $this->http, $this->module . '/' . $billId, null, null);
            $data = json_decode($response, true);

            return $data['code'] === 0;
        }

        throw new \Exception('Укажите корректный идентификатор [bill id]');
    }

    /**
     * Lists all the Bills in Zoho Inventory.
     * https://www.zoho.com/inventory/api/v1/#Bills_List_all_Bills.
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


}
