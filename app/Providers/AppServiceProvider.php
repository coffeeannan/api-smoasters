<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    private  function getAccessToken()
    {
        $response =  Http::withQueryParameters([
            'refresh_token' => env('ZOHO_REFRESH_TOKEN'),
            'client_id' => env('ZOHO_CLIENT_ID'),
            'client_secret' => env('ZOHO_CLIENT_SECRET'),
            'redirect_uri' => 'https://google.com',
            'grant_type' => 'refresh_token'

        ])->post('https://accounts.zoho.eu/oauth/v2/token');
        $accessToken = $response->json('access_token');
        return  $accessToken;
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Http::macro('zoho', function () {
            $response =  Http::withQueryParameters([
                'refresh_token' => env('ZOHO_REFRESH_TOKEN'),
                'client_id' => env('ZOHO_CLIENT_ID'),
                'client_secret' => env('ZOHO_CLIENT_SECRET'),
                'redirect_uri' => 'https://google.com',
                'grant_type' => 'refresh_token'

            ])->post('https://accounts.zoho.eu/oauth/v2/token');
            return Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Zoho-oauthtoken ' . $response->json('access_token'),
                'Content-Type' => 'application/json',
            ])->baseUrl('https://www.zohoapis.eu/inventory/v1')->withQueryParameters([
                'organization_id' => env('ZOHO_ORGANISATION_ID'),
            ]);
        });
    }
}
