<?php

namespace App\Http\Controllers\AdminV2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RejectionReason;
use Illuminate\Support\Facades\Session;

class RejectionReasonsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$category)
    {
        abort_if(!in_array($category,['images','videos','vectors']), 404);
        $index_route = route('admin.rejection_reasons.index',['category'=>$category]);
        $html_new_path = route('admin.rejection_reasons.create',['category'=>$category]);
        $show_link = route('admin.rejection_reasons.edit',['category'=>$category,'id'=> 0]);
        $html_breadcrumbs = [
            'title' => __('admin.RejectionReason'),
            'subtitle' => __('admin.RejectionReason'),
            'datatable' => true,
        ];
        
        if (!$request->datatable)
            return view('admin_v2.rejection_reasons.index', compact('index_route','html_breadcrumbs', 'html_new_path','show_link'));
        $data = process_datatable_query(RejectionReason::query()->where('category',$category));

        return $data;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($category)
    {
        abort_if(!in_array($category,['images','videos','vectors']), 404);

        $html_breadcrumbs = [
            'title' => __('admin.RejectionReason'),
            'subtitle' => __('admin.RejectionReason'),
        ];
        $store_url = route('admin.rejection_reasons.store',$category);
        $index_url = route('admin.rejection_reasons.index',$category);

        return view('admin_v2.rejection_reasons.create', compact('html_breadcrumbs','index_url','store_url'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request , $category)
    {
        abort_if(!in_array($category,['images','videos','vectors']), 404);

        $data = $request->validate([
            'title' => 'required|max:80',
            'description_ar' => 'required|max:500',
            'description_en' => 'required|max:500',
            'type' => 'required',
            'status' => 'sometimes',
        ]);
        $data['category'] = $category;
        RejectionReason::create($data);
        Session::flash('success', trans('admin.success_add'));
        return redirect()->route('admin.rejection_reasons.index',$category);
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($category,$id)
    {
        abort_if(!in_array($category,['images','videos','vectors']), 404);

        $html_breadcrumbs = [
            'title' => __('admin.RejectionReason'),
            'subtitle' => __('admin.RejectionReason'),
        ];

        $RejectionReason = RejectionReason::findOrFail($id);
        $store_url = route('admin.rejection_reasons.update',['category'=>$category,'id'=>$id]);
        $index_url = route('admin.rejection_reasons.index',$category);
        return view('admin_v2.rejection_reasons.edit', compact('html_breadcrumbs','RejectionReason','index_url','store_url'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $category, $id)
    {
        abort_if(!in_array($category,['images','videos','vectors']), 404);

        $rules = [
            'title' => 'required|max:80',
            'description_ar' => 'required|max:500',
            'description_en' => 'required|max:500',
            'type' => 'required',
            'status' => 'sometimes',
        ];

        $data = $request->validate($rules);
        RejectionReason::where('id',$id)->update($data);       
         Session::flash('success', trans('admin.success_update'));
        return redirect()->route('admin.rejection_reasons.index',$category);



    }

 
}
