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

use App\Jobs\Util\ImportStripeCustomers;
use App\Jobs\Util\StripeUpdatePaymentMethods;
use Illuminate\Http\Request;

class StripeController extends BaseController
{
    public function update(Request $request)
    {
        if ($request->user()->isAdmin()) {
            StripeUpdatePaymentMethods::dispatch($request->user()->company());

            return response()->json(['message' => 'Processing'], 200);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

    public function import()
    {
        if (auth()->user()->isAdmin()) {
            ImportStripeCustomers::dispatch(auth()->user()->company());

            return response()->json(['message' => 'Processing'], 200);
        }
        
        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
