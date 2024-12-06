<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Process the webhook payload asynchronously
        // Perform actions based on the webhook data
        var_dump($request->json());
        return response()->json(['success' => true]);
    }
}
