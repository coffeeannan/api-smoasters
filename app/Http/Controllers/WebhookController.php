<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ZohoController;
use Illuminate\Http\Request;

class WebhookController extends Controller
{

    public function orderCreated(Request $request)
    {
        $order = $request->json('body');
        $products = $request->json('body.line_items');
        $customer = $request->json('body.customer');
        $order_number = $request->json('body.order_number');
        $orderEmail = $request->json('body.email');
        $orderStatusUrl = $request->json('body.order_status_url');
        // var_dump($products);
        $mappedItems = ShopifyToZohoController::mapShopItems($request->json('body.line_items'));
        $zohoController = new ZohoController();
        $zohoItems = $zohoController->matchItems([$mappedItems]);
        return response()->json($zohoItems);
    }

    public function returnCustomerData(Request $req)
    {
        return response()->json(['message' => 'Customer data was sent.'], 200);
    }


    public function deleteCustomerData(Request $req)
    {
        return response()->json(['message' => 'Customer data was removed.'], 200);
    }

    public function deleteShop(Request $req)
    {
        return response()->json(['message' => 'Shop data was removed.'], 200);
    }
}
