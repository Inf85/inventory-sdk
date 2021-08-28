<?php

namespace InventorySDK\Api;

use App\Libs\ZohoInventory\Request\ItemsGroup;
use InventorySDK\Api\Requests\Bills;
use InventorySDK\Api\Requests\Contacts;
use InventorySDK\Api\Requests\CustomerPayment;
use InventorySDK\Api\Requests\Invoices;
use InventorySDK\Api\Requests\Items;
use InventorySDK\Api\Requests\Packages;
use InventorySDK\Api\Requests\PurchaseOrders;
use InventorySDK\Api\Requests\PurchaseReceives;
use InventorySDK\Api\Requests\SalesOrders;
use InventorySDK\Api\Requests\SalesReturns;
use InventorySDK\Api\Requests\ShipmentOrders;
use InventorySDK\Api\Requests\VendorPayments;
use InventorySDK\Api\Request as HttpRequest;
use GuzzleHttp\Client as HttpClient;

class ZohoInventoryApi
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    private static $instance = null;

    protected $shopName;


    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __clone()
    {
    }

    /**
     * ZohoInventoryApi constructor.
     * @param string $apiToken
     * @param int $organizationID
     */
    public function __construct()
    {
        $this->httpClient = HttpRequest::getInstance()->getHttpClient([
            'base_uri' => config('zoho-inventory.app.base_uri'),
        ]);

    }

    /**
     * @return Items
     */
    public function items()
    {
        return new Items($this->httpClient);
    }

    /**
     * @return ItemsGroup
     */
    public function itemGroup(){
        return new ItemsGroup($this->httpClient);
    }

    /**
     * @return SalesOrders
     */
    public function salesOrders()
    {
        return new SalesOrders($this->httpClient);
    }

    /**
     * @return Packages
     */
    public function packages()
    {
        return new Packages($this->httpClient);
    }

    /**
     * @return Invoices
     */
    public function invoices()
    {
        return new Invoices($this->httpClient);
    }

    /**
     * @return PurchaseOrders
     */
    public function purchaseOrders()
    {
        return new PurchaseOrders($this->httpClient);
    }

    /**
     * @return PurchaseReceives
     */
    public function purchaseReceives()
    {
        return new PurchaseReceives($this->httpClient);
    }


    /**
     * @return Bills
     */
    public function bills()
    {
        return new Bills($this->httpClient);
    }

    /**
     * @return ShipmentOrders
     */
    public function shipmentOrders()
    {
        return new ShipmentOrders($this->httpClient);
    }

    /**
     * @return Contacts
     */
    public function contacts()
    {
        return new Contacts($this->httpClient);
    }

    /**
     * @return CustomerPayment
     */
    public function customerPayment()
    {
        return new CustomerPayment($this->httpClient);
    }

    /**
     * @return VendorPayments
     */
    public function vendorPayment(){
        return new VendorPayments($this->httpClient);
    }

    /**
     * @return SalesReturns
     */
    public function salesReturns(){
        return new SalesReturns($this->httpClient);
    }
}
