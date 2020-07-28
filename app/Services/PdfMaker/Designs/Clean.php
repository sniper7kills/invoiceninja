<?php

/**
 * Invoice Ninja (https://invoiceninja.com)
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2020. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://opensource.org/licenses/AAL
 */

namespace App\Services\PdfMaker\Designs;

class Clean
{
    public function html()
    {
        return file_get_contents(
            base_path('resources/views/pdf-designs//clean.html')
        );
    }
}