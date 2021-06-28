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

namespace App\Services\Invoice;

use App\Events\Invoice\InvoiceWasPaid;
use App\Events\Payment\PaymentWasCreated;
use App\Factory\PaymentFactory;
use App\Jobs\Invoice\InvoiceWorkflowSettings;
use App\Libraries\Currency\Conversion\CurrencyApi;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\AbstractService;
use App\Services\Client\ClientService;
use App\Utils\Ninja;
use App\Utils\Traits\GeneratesCounter;
use Illuminate\Support\Carbon;

class MarkPaid extends AbstractService
{
    use GeneratesCounter;

    private $client_service;

    private $invoice;

    public function __construct(ClientService $client_service, Invoice $invoice)
    {
        $this->client_service = $client_service;

        $this->invoice = $invoice;
    }

    public function run()
    {
        if ($this->invoice->status_id == Invoice::STATUS_DRAFT) {
            $this->invoice->service()->markSent();
        }

        /*Don't double pay*/
        if ($this->invoice->statud_id == Invoice::STATUS_PAID) {
            return $this->invoice;
        }

        /* Create Payment */
        $payment = PaymentFactory::create($this->invoice->company_id, $this->invoice->user_id);

        $payment->amount = $this->invoice->balance;
        $payment->applied = $this->invoice->balance;
        $payment->number = $this->getNextPaymentNumber($this->invoice->client);
        $payment->status_id = Payment::STATUS_COMPLETED;
        $payment->client_id = $this->invoice->client_id;
        $payment->transaction_reference = ctrans('texts.manual_entry');
        $payment->currency_id = $this->invoice->client->getSetting('currency_id');
        $payment->is_manual = true;
        /* Create a payment relationship to the invoice entity */
        $payment->save();

        $this->setExchangeRate($payment);

        $payment->invoices()->attach($this->invoice->id, [
            'amount' => $payment->amount,
        ]);

        $this->invoice->next_send_date = null;
        
        $this->invoice->service()
                ->setExchangeRate()
                ->updateBalance($payment->amount * -1)
                ->updatePaidToDate($payment->amount)
                ->setStatus(Invoice::STATUS_PAID)
                ->applyNumber()
                ->deletePdf()
                ->save();

        if ($this->invoice->client->getSetting('client_manual_payment_notification')) {
            $payment->service()->sendEmail();
        }
        
        /* Update Invoice balance */
        event(new PaymentWasCreated($payment, $payment->company, Ninja::eventVars(auth()->user() ? auth()->user()->id : null)));
        event(new InvoiceWasPaid($this->invoice, $payment, $payment->company, Ninja::eventVars(auth()->user() ? auth()->user()->id : null)));

        $payment->ledger()
                ->updatePaymentBalance($payment->amount * -1);

        $this->client_service
            ->updateBalance($payment->amount * -1)
            ->updatePaidToDate($payment->amount)
            ->save();

        InvoiceWorkflowSettings::dispatchNow($this->invoice);

        return $this->invoice;
    }

    private function setExchangeRate(Payment $payment)
    {
        $client_currency = $payment->client->getSetting('currency_id');
        $company_currency = $payment->client->company->settings->currency_id;

        if ($company_currency != $client_currency) {
            $exchange_rate = new CurrencyApi();

            $payment->exchange_rate = $exchange_rate->exchangeRate($client_currency, $company_currency, Carbon::parse($payment->date));
            //$payment->exchange_currency_id = $client_currency; // 23/06/2021
            $payment->exchange_currency_id = $company_currency;
        
            $payment->save();
        }
    }
}
