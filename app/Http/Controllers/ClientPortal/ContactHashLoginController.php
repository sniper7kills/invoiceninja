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

namespace App\Http\Controllers\ClientPortal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactHashLoginController extends Controller
{

    /**
     * Logs a user into the client portal using their contact_key
     * @param  string $contact_key  The contact key
     * @return Auth|Redirect
     */
    public function login(string $contact_key)
    {
        return redirect()->to('/client/invoices');
    }

    public function magicLink(string $magic_link)
    {
        return redirect()->to('/client/invoices');
    }

    public function errorPage(Request $request)
    {
        return render('generic.error', ['title' => $request->session()->get('title'), 'notification' => $request->session()->get('notification')]);
    }
}
