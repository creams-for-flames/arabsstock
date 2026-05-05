<?php

namespace App\Http\Controllers\AdminV2;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CastingController extends Controller
{
    public function index()
    {
        $index_url = route('admin.models.requests.datatable');
        $show_url = route('admin.models.requests.show', 0);


        $object = new Contact();
        $is_models_site = true;


        $html_breadcrumbs = [
            'title'     => __('views.Requests'),
            'subtitle'  => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path =NULL;
        $subheader_actions = NULL;

        return view(
            'admin_v2.models.dashboard.requests',
            compact(
                'html_breadcrumbs',
                'html_new_path',
                'subheader_actions',
                'index_url',
                'show_url',
                'object',
                'is_models_site'
            )
        );
    }

    public function datatable(Request $request)
    {
        $search_callback = function ($query, $search) {
            return $query
                ->where(function ($query) use ($search) {
                    $query->where('skill', 'like', '%' . $search . '%');
                });
        };


        $data = process_datatable_query(Contact::query(), $search_callback);

        return $data;
    }

    public function show($id)
    {
         $models = Contact::with(['images','nationality_casting'])->find($id);
        $index_url = route('admin.models.dashboard.requests');

        $html_breadcrumbs = [
            'title'    => __('views.Requests'),
            'subtitle' => __('views.Edit'),
        ];
        return view(
            'admin_v2.models.dashboard.show',
            compact('html_breadcrumbs', 'models','index_url')
        );
    }


}
