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
namespace App\Helpers\Mail;

use Dacastro4\LaravelGmail\Services\Message\Mail;
use Illuminate\Mail\MailManager;

class GmailTransportManager extends MailManager
{
    protected function createGmailTransport()
    {
        return new GmailTransport(new Mail);
    }
}
