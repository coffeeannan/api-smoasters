<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ZohoController extends Controller
{

    public function createOrder(Request $request): bool
    {
        $response = Http::zoho()->post('/salesorders', $request->all());
        $createdOrder = $response->json('salesorder');
        $salesOrderId = $createdOrder['salesorder_number'];
        if ($salesOrderId) {
            Log::info('Generated salesorder: ' . $salesOrderId);
            return true;
        }
        return false;
    }

    public function matchTax(array $taxLine)
    {

        $response = Http::zoho()->get('/settings/taxes');
        $taxes = $response->json('taxes');
        foreach ($taxes as $tax) {
            if (number_format($tax['tax_percentage'], 2) === $taxLine['rate']) {
                return $tax;
            }
        }
        return  [
            'tax_id' => '',
            'tax_name' => $taxLine['title'],
            'tax_percentage' => $taxLine['rate']
        ];
    }

    public static function findCurrency(string $currencyCode)
    {

        $response = Http::zoho()->get('/settings/currencies');
        $currencies = $response->json('currencies');
        foreach ($currencies as $currency) {
            if ($currency['currency_code'] === $currencyCode) {
                return $currency;
            }
        }
        return  [];
    }



    public function matchItems(array $lineItems = [])
    {
        $positions = [];
        $zohoItems = $this->fetchAllData('/items', 'items');
        $item = [];

        foreach ($lineItems as $lineItem) {

            foreach ($zohoItems as $zohoItem) {

                if ($zohoItem['sku'] === $lineItem['sku']) {
                    $tax = $this->matchTax($lineItem['tax']);
                    $item['tax_id'] = $tax['tax_id'];
                    $item['tax_name'] = $tax['tax_name'];
                    $item['tax_percentage'] = $tax['tax_percentage'];

                    $item['sku'] = $lineItem['sku'];
                    $item['quantity'] = $lineItem['quantity'];
                    $item['price'] = $lineItem['rate'];
                    $item['discount'] = $lineItem['discount_amount'];
                    $item['is_taxable'] = $lineItem['is_taxable'];
                    $positions = array_merge($positions, $item);
                }
            }
        }
        return $positions;
    }

    public static function findCustomer(array $customer)
    {

        $matchedCustomer =  Http::zoho()->get('/contacts', [
            'contact_name' => $customer['first_name'] . ' ' . $customer['last_name'],
            'email' => $customer['email'],
        ]);
        return $matchedCustomer;
    }

    public static function createCustomer($payload) {}


    private function fetchAllData(string $endpoint, string $property): array
    {
        $allData = [];
        $currentPage = 1;

        while (true) {
            $response = Http::zoho()->get($endpoint, [
                'page' => $currentPage,
            ]);

            if (!$response->successful()) {
                throw new \Exception("Failed to fetch data from endpoint: {$endpoint}");
            }


            $data = $response->json($property);
            if (!is_array($data)) {
                throw new \Exception("Invalid data format for property: {$property}");
            }

            $allData = array_merge($allData, $data);

            $pageContext = $response->json('page_context');
            if (empty($pageContext['has_more_page'])) {
                break;
            }

            $currentPage++;
        }

        return $allData;
    }
}
