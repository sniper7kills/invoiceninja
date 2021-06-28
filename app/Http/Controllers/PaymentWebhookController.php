<?php

/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2021. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Http\Controllers;

use App\Http\Requests\Payments\PaymentWebhookRequest;

class PaymentWebhookController extends Controller
{
    public function __invoke(PaymentWebhookRequest $request, string $company_key, string $company_gateway_id)
    {
        $payment = $request->getPayment();
        
        if (!$payment) {
            return response()->json(['message' => 'Payment record not found.'], 400);
        }

        $client = is_null($payment) ? $request->getClient() : $payment->client;

        if (!$client) {
            return response()->json(['message' => 'Client record not found.'], 400);
        }

        return $request->getCompanyGateway()
            ->driver($client)
            ->processWebhookRequest($request, $payment);
    }
}
