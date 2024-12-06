<?php

namespace App\Handler;

use Illuminate\Http\Request;
use Spatie\WebhookClient\Exceptions\InvalidConfig;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;

class ShopifySignature implements SignatureValidator
{
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $hmac_header = $request->header($config->signatureHeaderName);
        if (!$hmac_header) {
            return false;
        }
        $signingSecret = $config->signingSecret;
        if (empty($signingSecret)) {
            throw InvalidConfig::signingSecretNotSet();
        }
        $calculated_hmac =  base64_encode(hash_hmac('sha256', $request->getContent(), $signingSecret, true));
        return hash_equals($hmac_header, $calculated_hmac);
    }
}
