<?php

namespace App\Http\Controllers\AdminV2;

use App\Models\CategoryAdmin;
use App\Models\CategoryVideoAdmin;
use App\Models\EmailSubscribe;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EmailSubscriptionController extends Controller
{
    public function index()
    {
        $index_url = route('admin.email_subscribe.datatable');


        $object = new EmailSubscribe();

        $html_breadcrumbs = [
            'title'     => __('views.Email_subscribe'),
            'subtitle'  => __('views.Index'),
            'datatable' => true,
        ];


        return view(
            'admin_v2.email_subscribe.index',
            compact(
                'html_breadcrumbs',
                'index_url',
                'object'
            )
        );
    }

    public function datatable(Request $request)
    {
        $data = process_datatable_query(EmailSubscribe::query(), function (
            $query,
            $search
        ) {
            return $query
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }





}
