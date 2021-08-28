<?php
/**
 * Created by PhpStorm.
 * User: Михаил
 * Date: 22.04.2019
 * Time: 12:19
 */

namespace InventorySDK\Api\Requests;

use GuzzleHttp\Client;
use InventorySDK\Api\Request;

class Contacts extends Request
{
    /**
     * @var Client
     */
    private $http;

    /**
     * @var string
     */
    private $module = 'contacts';

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
     * Search contact by Name in Zoho Inventory
     * @param $contactName
     * @param string $options
     * @return mixed
     */
    public function searchByName($contactName,$options=''){
        $config = $this->http->getConfig('query') ?? [];

        $query = $config + [
              'contact_name_startswith' => $contactName
            ];

        $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

        $formParams = ['JSONString' => $jsonData];
        $response = $this->request('get', $this->http, $this->module, $formParams, $query);

        $data = json_decode($response, true);
        $data['contacts']['response_code'] = $data['code'];

        return $data['contacts'];
    }


    /**
     * List of All Contacts
     * @param int $page
     * @return array
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
     * Create Contact
     * @param array $options
     * @param false $autoIncrement
     * @return mixed
     * @throws \Exception
     */
    public function create(array $options, $autoIncrement = false)
    {
        if (!empty($options)) {

            $config = $this->http->getConfig('query') ?? [];
            // Ignore auto sales order number generation for this sales order [bool]
            $query = $config ;

            $jsonData = json_encode($options, JSON_PRESERVE_ZERO_FRACTION);

            $formParams = ['JSONString' => $jsonData];

            $response = $this->request('post', $this->http, $this->module, $formParams, $query);
            $data = json_decode($response, true);
            $data['contact']['response_code'] = $data['code'];

            return $data['contact'];
        }

        throw new \Exception('Массив не может быть пустым, пожалуйста заполните его');

    }

}
