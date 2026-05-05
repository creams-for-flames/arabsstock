<?php

namespace App\Http\Controllers\AdminV2;

use App\Models\Evaluation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EvaluationController extends Controller
{
    public function index()
    {
        $index_url = route('admin.evaluations.datatable');
        $show_url = route('admin.evaluations.show', 0);
        // $destroy_url = route('admin.evaluations.destroy', 0);

        $object = new Evaluation();

        $html_breadcrumbs = [
            'title'     => __('views.evaluations'),
            'subtitle'  => __('views.Index'),
            'datatable' => true,
        ];
        $subheader_actions =NULL;

        return view(
            'admin_v2.evaluations.index',
            compact(
                'html_breadcrumbs',
                'subheader_actions',
                'index_url',
                'show_url',
                'object'
            )
        );
    }

    public function datatable(Request $request)
    {
        $search_callback = function ($query, $search) {
            return $query
                ->where(function ($query) use ($search) {
                    $query->where('message', 'like', '%' . $search . '%');
                });
        };

        $data = process_datatable_query(Evaluation::query()->orderBy('id','desc'), $search_callback);
        $data['data'] = collect($data['data'])->map(function($item){
            $item['message'] = Str::substr($item['message'], 0, 30);
            return $item;
        });
        return $data;
    }





    public function show($id)
    {
        $evaluation = Evaluation::find($id);

        if($evaluation && empty($evaluation->seen_at)){
            $evaluation->seen_at = now();
            $evaluation->save();
        }
        
        $index_url = route('admin.evaluations.index');

        $html_breadcrumbs = [
            'title'    => __('views.evaluations'),
            'subtitle' => __('views.Edit'),
        ];
        return view(
            'admin_v2.evaluations.show',
            compact('html_breadcrumbs', 'evaluation','index_url')
        );
    }

  

}
