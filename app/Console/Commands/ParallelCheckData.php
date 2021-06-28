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

namespace App\Console\Commands;

use App\Jobs\Ninja\CheckCompanyData;
use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Class CheckData.
 */
class ParallelCheckData extends Command
{
    /**
     * @var string
     */
    protected $name = 'ninja:pcheck-data';

    /**
     * @var string
     */
    protected $description = 'Check company data in parallel';

    protected $log = '';

    protected $isValid = true;

    public function handle()
    {
        $hash = Str::random(32);

        Company::cursor()->each(function ($company) use ($hash) {
            CheckCompanyData::dispatch($company, $hash)->onQueue('checkdata');
        });
    }
}
