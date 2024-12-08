<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{

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
