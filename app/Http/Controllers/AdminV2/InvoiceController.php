<?php

namespace App\Http\Controllers\AdminV2;

use App\Models\Invoice;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of Invoices.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $index_url = route('admin.invoices.datatable');

        $html_breadcrumbs = [
            'title' => __('views.Invoices'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        return view(
            'admin_v2.invoice.index',
            compact(
                'html_breadcrumbs',
                'index_url'
            )
        );
    }


    public function datatable(Request $request)
    {
        $data = process_datatable_query(Invoice::with(['plan', 'user']), function (
            $query,
            $search
        ) {
            return $query
                ->join('arabsstock_users.users as users', 'users.id', '=', 'invoices.user_id')
                ->join('image_plans', 'image_plans.id', '=', 'invoices.plan_id')
                ->where(function($query) use ($search) {
                    $query->where('username', 'like', '%' . $search . '%')
                          ->orWhere('name', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }
}
