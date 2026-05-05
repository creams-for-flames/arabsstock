<?php

namespace App\Http\Controllers\AdminV2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FailedJob;
use App\Jobs\InsertFailedJob;
use Illuminate\Support\Facades\{Session,Log,Artisan};

class FailedJobController extends Controller
{
    public function index()
    {
        $index_url = route('admin.failed_jobs.datatable');
        $edit_url = route('admin.failed_jobs.insert_queue', 0);
        $destroy_url = route('admin.failed_jobs.destroy', 0);

        // $object = new Withdraw();

        $html_breadcrumbs = [
            'title' => __('views.failed_jobs'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $is_request = 1;
        $html_new_path = '';
        $subheader_actions = [
            'delete' => [
                'type' => 'button',
                'text' => __('views.Cancel_all'),
                'url' => route('admin.failed_jobs.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];
        return view(
            'admin_v2.failed_jobs.index',
            compact('html_breadcrumbs', 'subheader_actions', 'index_url', 'edit_url', 'destroy_url','is_request')
        );
    }

    public function datatable(Request $request)
    {
        $search_callback = function ($query, $search) {
            return $query;
        };

        $data = process_datatable_query(FailedJob::query()->orderBy('id','desc'), $search_callback);

        return $data;
    }

    public function destroy($id)
    {
        // TODO allow multiple delete
        $id = explode(',', $id)[0];
        $failed_job = FailedJob::where('id',$id)->forceDelete();
        Session::flash('success', trans('misc.success_delete'));
        return redirect()->back();
    }

    public function insert_queue($id)
    {
      $job =   FailedJob::where('id',$id)->first();
      if ($job->connection === "media") {
            $job->delete();
           dispatch(new InsertFailedJob($id))->onQueue('insert_queue');
      }else{
        Artisan::call("queue:retry", ['id' => $id]);
        Log::channel('info')->info("InsertFailedJob Arabsstock back id:  {$id}");
      }
        Session::flash('success', trans('misc.success_update'));
        return redirect()->back();

    }

    public function view_exception($id)
    {
      $job =   FailedJob::where('id',$id)->first();
      Session::flash('success', $job->exception);
      return redirect()->back();
    }
}
