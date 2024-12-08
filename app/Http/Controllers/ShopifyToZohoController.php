<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShopifyToZohoController extends Controller
{
    public function mapToZohoOrder(array $order) {}

    public function getAccessToken()
    {
        $response =  Http::zoho()->get('/contacts');
        return $response->json();
    }

    public static function mapShopItems(array $lineItems = [])
    {
        $mappedItems = [];
        foreach ($lineItems as $lineItem) {
            $taxline = $lineItem['tax_lines'][0];
            $item = [];
            $item['quantity'] = $lineItem['quantity'];
            $item['sku'] = $lineItem['sku'];
            $item['rate'] = $lineItem['price_set']['presentment_money']['amount'];
            $item['tax'] = [
                'rate' => number_format($taxline['rate'] * 100, 2),
                'title' => $taxline['title'],
            ];
            $item['discount_amount'] = $lineItem['total_discount_set']['presentment_money']['amount'];
            $item['is_taxable'] = $lineItem['taxable'];
            $mappedItems = array_merge($mappedItems, $item);
        }
        return $mappedItems;
    }
}
