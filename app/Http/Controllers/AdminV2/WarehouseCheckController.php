<?php

namespace App\Http\Controllers\AdminV2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\Image\RestoreNonExistentImages;
use App\Jobs\Image\ImagesWarehouseCheckRequests;
use App\Jobs\Vector\RestoreNonExistentVectors;
use App\Jobs\Vector\VectorsWarehouseCheckRequests;
use App\Jobs\Video\RestoreNonExistentVideos;
use App\Jobs\Video\VideosWarehouseCheckRequests;
use App\Models\Image;
use App\Models\ImageFolder;
use App\Models\User;
use App\Models\Vector;
use App\Models\VectorFolder;
use App\Models\Video;
use App\Models\VideoFolder;
use App\Models\WarehouseCheckRequest;
use App\WarehouseCheck;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;

class WarehouseCheckController extends Controller
{
    public function index(Request $request,$type)
    {
        $html_new_path = route('admin.warehouse_check_requests.create',['type'=>$type]);
        $show_link = route("admin.warehouse_check_requests.warehouse_check.index",['type'=>$type,'id'=>0]);
        $index_url = route("admin.warehouse_check_requests.index",['type'=>$type]);
        $edit_url = route("admin.warehouse_check.insert_queue",['type'=>$type,'id'=>0]);
        $admin_file_reupload_not_found = route('admin.warehouse_check.admin_file_reupload_not_found',['type'=>$type,'id'=>0]);
        $large_file = NULL;
        $html_breadcrumbs = [
            'title' => __('admin.warehouse_check'),
            'subtitle' => __('admin.warehouse_check'),
            'datatable' => true,
        ];

        if (!$request->datatable)
            return view('admin_v2.warehouse_check_requests.index', compact('html_breadcrumbs', 'html_new_path','edit_url','index_url','show_link','admin_file_reupload_not_found','type','large_file'));



         $query = WarehouseCheckRequest::where('type',$type)->orderBy('id','desc');
        $data = process_datatable_query($query);

        return $data;

    }

    public function create_warehouse_check_request(Request $request,$type)
    {
        switch ($type) {
            case 'images':
                $files_count =Image::count();
                break;
            case 'videos':
                $files_count =Video::whereNull('parent_id')->count();
                break;
            case 'vectors':
                $files_count =Vector::count();
                break;
        }
        $data =new WarehouseCheckRequest;
        $data =  $data->create([
            'type' =>$type,
            'target'=>$request->get('target','all'),
            'target_count'=> $files_count

        ]);
        switch ($type) {
            case 'images':
                dispatch(new ImagesWarehouseCheckRequests($data->id))->onQueue('insert_queue');
                break;
            case 'videos':
                dispatch(new VideosWarehouseCheckRequests($data->id))->onQueue('insert_queue');
                break;
            case 'vectors':
                dispatch(new VectorsWarehouseCheckRequests($data->id))->onQueue('insert_queue');
                break;
        }

        Session::flash('success', trans('misc.successfully_added'));
        return redirect()->back();
    }
    public function warehouse_check(Request $request,$type,$id)
    {
        $data = WarehouseCheckRequest::findOrFail($id);


        if (!$request->datatable){
            $html_new_path = '';
            $show_link = '';
            $index_url = route("admin.warehouse_check_requests.warehouse_check.index",['type'=>$type,'id'=>$id]);
            $datatable_url = route("admin.warehouse_check_requests.warehouse_check.datatable",['type'=>$type,'id'=>$id]);


            $edit_url = route("admin.warehouse_check.insert_queue",['type'=>$type,'id'=>0]);
            $admin_file_reupload_not_found = route('admin.warehouse_check.admin_file_reupload_not_found',['type'=>$type,'id'=>0]);
            $large_file = NULL;

            $html_breadcrumbs = [
                'title' => __('admin.warehouse_check'),
                'subtitle' => __('admin.warehouse_check'),
                'datatable' => true,
            ];
            switch ($type) {
                case 'images':
                $folders = ImageFolder::select('folder', 'id')->get();
                $admins =User::where('role', 'admin')->get();
                $types =['large', 'small', 'medium', 'thumbnail','preview','search','search_large','og_image'];
                $large_file = 'large';
                    break;
                case 'videos':
                $folders = VideoFolder::select('folder', 'id')->get();
                $admins =User::where('role', 'admin_video')->get();
                $types =['preview','4K','FHD','HD','SD', 'thumbnail','thumbnail_sm','cut_video','gif_video','size_240p','search','og_image'];
                $large_file = 'preview';
                    break;
                case 'vectors':
                $folders = VectorFolder::select('folder', 'id')->get();
                $admins =User::where('role', 'admin_vector')->get();
                $types = ['vector','large', 'thumbnail','preview','search_large','og_image'];
                $large_file = 'vector';
                    break;
            }
            $progress = 0;
            if ($data->target_count){
                $progress = round(($data->check_count/$data->target_count)*100);
            }
                return view('admin_v2.warehouse_check_requests.warehouse_check.index', compact('html_breadcrumbs', 'html_new_path','edit_url','index_url','show_link','admin_file_reupload_not_found','type','large_file','data','progress','folders','admins','datatable_url','types'));


        }


    }
    public function warehouse_check_datatable(Request $request,$type,$id)
    {
        $folder_id = $request->input('query.folder_id');
        $user_id = $request->input('query.user_id');
        $contributor_id = $request->input('query.contributor_id');
        $file_status_warehouse_check = $request->input('query.file_status','notfound');
        $file_status_warehouse_check = $file_status_warehouse_check === 'all'?null:$file_status_warehouse_check;
        $status = $request->input('query.status');
        $type_file = $request->input('query.type');
        $status = $status === 'all'?null:$status;
        $type_file = $type_file === 'all'?null:$type_file;
        $data = WarehouseCheckRequest::findOrFail($id);


        switch ($type) {
            case 'images':
                $type = Image::class;
                break;
            case 'videos':
                $type = Video::class;
                break;
            case 'vectors':
                $type = Vector::class;
                break;
        }
         $query = WarehouseCheck::withoutGlobalScopes()
                ->with(['warehouseable.user','warehouseable.folder',
                'warehouseable' => function ($query) {
                $query->withoutGlobalScopes();
                },
                ])
                ->whereHasMorph('warehouseable', $type)
                ->when($folder_id, function ($query) use ($folder_id , $type) {
                    $query->
                    whereHasMorph(
                        'warehouseable',
                        [$type],
                        function (Builder $query) use($folder_id){
                            $query->where('folder_id', $folder_id);
                        }
                    );
                })
                ->when($user_id, function ($query) use ($user_id , $type) {
                    $query->
                    whereHasMorph(
                        'warehouseable',
                        [$type],
                        function (Builder $query) use($user_id){
                            $query->where('user_id', $user_id);
                        }
                    );
                })
                ->when($contributor_id, function ($query) use ($contributor_id , $type) {
                    $query->
                    whereHasMorph(
                        'warehouseable',
                        [$type],
                        function (Builder $query) use($contributor_id){
                            $query->where('user_id', $contributor_id);
                        }
                    );
                })
                ->when($status, function ($query) use ($status , $type) {
                    $query->
                    whereHasMorph(
                        'warehouseable',
                        [$type],
                        function (Builder $query) use($status){
                            $query->where('status', $status);
                        }
                    );
                })
                ->where('warehouse_check_request_id',$id)
                ->when($file_status_warehouse_check , function ($query) use ($file_status_warehouse_check){
                    $query->where('status',$file_status_warehouse_check);
                })
                ->when($type_file , function ($query) use ($type_file){
                    $query->where('type',$type_file);
                })

                ->orderBy('id','desc');
        $data = process_datatable_query($query);

        return $data;

    }
    public function insert_queue($type,$id)
    {



        $file = WarehouseCheck::where('id',$id)->where('status',"notfound")->firstOrFail();
        $file->status = 'pending';
        $file->save();
        $type = $file->warehouseable_type;
        switch ($type) {
            case Image::class:
                dispatch(new RestoreNonExistentImages($file->id,$file->type))->onQueue('insert_queue');
             break;
            case Video::class:
                dispatch(new RestoreNonExistentVideos($file->id,$file->type))->onQueue('insert_queue');
             break;
            case Vector::class:
                dispatch(new RestoreNonExistentVectors($file->id,$file->type))->onQueue('insert_queue');
             break;
      }

        Session::flash('success', trans('misc.success_update'));
        return redirect()->back();

    }


    public function admin_file_reupload_not_found($type ,$id)
    {
        switch ($type) {
            case 'images':
                $type = Image::class;
                $view = 'admin_v2.warehouse_check_requests.warehouse_check.admin_file_reupload_not_found.images';
                $date_name = 'date';
                break;
            case 'videos':
                $type = Video::class;
                $view = 'admin_v2.warehouse_check_requests.warehouse_check.admin_file_reupload_not_found.videos';
                $date_name = 'date';
                break;
            case 'vectors':
                $type = Vector::class;
                $view = 'admin_v2.warehouse_check_requests.warehouse_check.admin_file_reupload_not_found.vectors';
                $date_name = 'created_at';
                break;
        }
        $file = WarehouseCheck::
        with(['warehouseable.user','warehouseable.folder','warehouseable' => function ($query) {
            $query->withoutGlobalScopes();
            }])
        ->whereHasMorph('warehouseable', $type)
        ->findOrFail($id);
        $accessToken = \auth()->user()->createToken('userToken')->accessToken;

     return view($view,compact('accessToken','file','date_name'));
    }
}
