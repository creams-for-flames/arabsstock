<?php

namespace App\Cashier;

use App\Cashier\Concerns\HandlesTaxes;
use App\Cashier\Concerns\ManagesCustomer;
use App\Cashier\Concerns\ManagesInvoices;
use App\Cashier\Concerns\ManagesPaymentMethods;
use App\Cashier\Concerns\ManagesSubscriptions;
use App\Cashier\Concerns\PerformsCharges;

trait Billable
{
    use HandlesTaxes;
    use ManagesCustomer;
    use ManagesInvoices;
    use ManagesPaymentMethods;
    use ManagesSubscriptions;
    use PerformsCharges;
}
