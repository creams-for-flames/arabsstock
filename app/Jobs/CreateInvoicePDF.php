<?php

namespace App\Jobs;

use App\Models\AdminImageSettings;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ImageSubscription;
use App\Models\VideoSubscription;
use App\Models\VectorSubscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use \Mpdf\Mpdf as PDF;


class CreateInvoicePDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $payment;

    public function __construct($payment)
    {
        $this->payment = $payment;
    }

    public function handle()
    {
        $payment = $this->payment;
        $client_name = $payment->user->name;
        if ($payment->user->is_business) {
            $client_name = $client_name . ' (' . $payment->user->company_name . ')';
        }
        $is_paid = in_array(get_class($payment), [ImageSubscription::class, VideoSubscription::class, VectorSubscription::class]) ? 1 : $payment->paid;
        $invoice = [
            'id' => $payment->id,
            'invoice_id' => $payment->payment_id ?: $payment->subscription_id,
            'client_name' => $client_name,
            'client_email' => $payment->user->email,
            'client_is_business' => $payment->user->is_business ? '1' : '0',
            'client_company_name' => $payment->user->company_name,
            'client_company_contact' => $payment->user->company_email . ' , ' . $payment->user->company_phone,
            'client_company_address' => $payment->user->company_address,
            'client_company_tax_id' => $payment->user->company_tax_id,
            'date' => explode(' ', $payment->created_at)[0],
            'amount' => "{$payment->amount} USD",
            'payment_method' => $payment->payment_method->title_en,
            'payment_status' => $is_paid ? 'paid' : 'Unpaid',
            'paid' => "{$payment->amount} USD",
            'items' => [['title' => $payment->plan->title_en, 'amount' => "{$payment->amount} USD"]],
            'terms' => __(($payment->plan_type == 'package' ? 'views.Immediate payment' : 'views.Recurring Invoice'), [], 'en'),
        ];
        Log::channel('info')->info("CreateInvoicePDF: subscription-{$invoice['id']}");
        app()->setLocale('en');
        $setting = image_settings();
        $items = $invoice['items'];
        if (!isset($invoice['type']))
            $invoice['type'] = '';

        if (!isset($invoice['client_company_tax_id']))
            $invoice['client_company_tax_id'] = '';

        $key = md5("{$invoice['type']}_{$invoice['id']}") . '_' . time();
        $filename = "invoice_{$invoice['type']}_{$invoice['id']}_{$key}.pdf";
        $output_folder = DS . 'uploads' . DS . 'invoices';


        $data = [
            'output' => $output_folder . DS . $filename,
            'logo' => public_path('img/logowe.png'),
            'logo_text' => $setting->title_en,
            'from_label' => __('views.invoice_from'),
            'for_label' => __('views.invoice_for'),
            'details_label' => __('views.invoice_details'),
            'company' => 'DR SOLUTIONS FZCO (ArabsStock)',
            'company_address_line1' => 'Offices 3, One Central, Dubai World Trade Centre',
            'company_address_line2' => '',
            'client' => preg_replace('/[\@\.\;\'"]+/', '', $invoice['client_name']),
            'client_contact_name' => $invoice['client_email'],
            'client_is_business' => @$invoice['client_is_business'],
            'client_company_name' => @$invoice['client_company_name'],
            'client_company_contact' => @$invoice['client_company_contact'],
            'client_company_address' => @$invoice['client_company_address'],
            'client_company_tax_id' => @$invoice['client_company_tax_id'],
            'invoice_label' => __('views.invoice_invoice'),
            'invoice_id' => $invoice['invoice_id'],
            'invoice_date_label' => __('views.invoice_invoice_date'),
            'invoice_date' => $invoice['date'],
            'amount_due_label' => __('views.invoice_amount_due'),
            'amount_due' => $invoice['amount'],
            'payment_method_label' => __('views.invoice_payment_method'),
            'payment_method' => $invoice['payment_method'],
            'payment_status_label' => __('views.invoice_payment_status'),
            'payment_status' => __('views.invoice_' . $invoice['payment_status']),
            'item_label' => __('views.invoice_item'),
            'price_label' => __('views.invoice_price'),
            'items' => $items,
            'total_label' => __('views.invoice_total'),
            'amount_paid_label' => $invoice['payment_status'] === 'refund' ? __('views.invoice_amount_refund') : __('views.invoice_amount_paid'),
            'total' => $invoice['amount'],
            'amount_paid' => $is_paid ? $invoice['paid'] : 0,
            'terms_label' => __('views.invoice_terms'),
            'terms_value' => $invoice['terms'],
            'notes_label' => __('views.invoice_notes'),
            'notes_value' => __('views.invoice_notes_value'),
            'tax_number_label' => __('views.tax_number'),
        ];

        $document = new PDF([
            'mode' => 'utf-8',
            'format' => 'A4',
            'CSSselectMedia' => 'all'
        ]);
        $document->WriteHTML(view('invoice.pdf', compact('data', 'payment')));
        $file = $output_folder . DS . $filename;
        $status_file = Storage::disk('s3')->put($file, $document->Output($filename, "S"));
        if ($status_file) {
            $path = DS . 'uploads' . DS . 'invoices' . DS . $filename;
            $payment->invoice_file = $path;
            $payment->save();
            Log::channel('info')->info("invoice {$invoice['id']} with pdf file  {$file}");

        } else {
            Log::channel('info')->info("invoice {$invoice['id']} without pdf file  {$file}");
        }
    }
}
