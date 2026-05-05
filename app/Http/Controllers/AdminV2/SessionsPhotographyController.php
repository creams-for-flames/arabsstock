<?php

namespace App\Http\Controllers\AdminV2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SessionRequest;
use App\Http\Requests\SessionsPhotographyRequest;
use App\Models\Actor;
use App\Models\Cities;
use App\Models\Contract;
use App\Models\Countries;
use App\Models\ImageFolder;
use App\Models\Location;
use App\Models\Photographer;
use App\Models\SessionInvoice;
use App\Models\SessionLocation;
use App\Traits\SessionManagementTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class SessionsPhotographyController extends Controller
{
    use SessionManagementTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type)
    {

            $index_url = route('admin.sessions.datatable',$type);


        $html_breadcrumbs = [
            'title' =>  __('views.SessionManagement'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        $subheader_actions = [];

        return view('admin_v2.sessions_photography.index',compact('html_breadcrumbs','subheader_actions','index_url'));
    }
    public function datatable(Request $request,$type)
    {
        $folder_type = $this->selectSessionType($type);
        $data = process_datatable_query($folder_type::query(), function (
            $query,
            $search
        ) {
            return $query
                ->where(function($query) use ($search) {
                    $query->where('title_ar', 'like', '%' . $search . '%')
                          ->orWhere('title_en', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type)
    {
        $contract = Contract::select('id','content_ar','title_ar')->find(2);
        $countries = $this->getCountries();
        $iso_code_2_SA = $countries->where('iso_code_2', "SA")->first();
        $cities = $this->getCities($iso_code_2_SA->id);


        return view('admin_v2.sessions_photography.create', compact('countries', 'cities','contract','type'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SessionsPhotographyRequest $request,$type)
    {
        $requestData = $request->validated();
        $folder_type = $this->selectSessionType($type);
        $folder = $this->createSession($requestData, $folder_type);
        // Your logic after creating the session...

        return response()->json(['msg' => __('misc.successfully_added')]);



    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     public function edit($type,$id)
     {
        $contract = Contract::select('id','content_ar','title_ar')->find(2);
        $update_url = route('admin.sessions.update',['type'=> $type,'id'=>$id]);
        $countries = $this->getCountries();
        $iso_code_2_SA = $countries->where('iso_code_2', "SA")->first();
        $cities = $this->getCities($iso_code_2_SA->id);

        $folder_type = $this->selectSessionType($type);

         $data = $folder_type::with(['actors','photographers','invoices','locations' => function ($query) {
            $query->orderBy('created_at', 'desc')->first();
        }])->findOrFail($id);
         $index_url = route('admin.sessions.index',['type'=>$type]);
         $update_url = route('admin.sessions.update',['type'=>$type,'id'=> $id]);
 
         $html_breadcrumbs = [
             'title' => __('views.contracts'),
             'subtitle' => __('views.Edit'),
         ];
 
         return view('admin_v2.sessions_photography.edit', compact('html_breadcrumbs','countries','cities','contract','type', 'data', 'index_url', 'update_url'));
     }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SessionsPhotographyRequest $request,$type, $id)
    {
        $requestData = $request->validated();
        $folder_type = $this->selectSessionType($type);
        $folder = $this->updateSession($requestData, $folder_type,$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getPhotographers(Request $request)
    {
        $keyword = $request->get('keyword','');
        $data = collect([]);
        if (isset($keyword) && $keyword != '') {
            $data = Photographer::query()->where(function($q) use ($keyword){
                    $q->orWhere('email','like','%'.$keyword.'%')
                    ->orWhere('id_number','like','%'.$keyword.'%');
            })->get();
        }
        return response()->json([$data],200);

    }

    public function getActors(Request $request)
    {
        $keyword = $request->get('keyword','');
        $data = collect([]);
        if (isset($keyword) && $keyword != '') {
            $data = Actor::query()->where(function($q) use ($keyword){
                $q->orWhere('email','like','%'.$keyword.'%')
                ->orWhere('id_number','like','%'.$keyword.'%');
        })->get();
        }
        return response()->json([$data],200);

    }

    public function getLocations(Request $request)
    {
        $keyword = $request->get('keyword','');
        $data = collect([]);
        if (isset($keyword) && $keyword != '') {
            $data = SessionLocation::query()->where(function($q) use ($keyword){
                $q->orWhere('email','like','%'.$keyword.'%')
                ->orWhere('name','like','%'.$keyword.'%')
                ->orWhere('license_code','like','%'.$keyword.'%');

            })->get();
        }
        return response()->json([$data],200);

    }
}
