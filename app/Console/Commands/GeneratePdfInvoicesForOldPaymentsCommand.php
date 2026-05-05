<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GeneratePdfInvoicesForOldPaymentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate_pdf_invoices_for_old_payments:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GeneratePdfInvoicesForOldPaymentsCommand';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch(
        new \App\Jobs\GeneratePdfInvoicesForOldPayments()
        )->onConnection('sync');
    }
}
