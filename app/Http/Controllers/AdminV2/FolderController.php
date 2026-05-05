<?php

namespace App\Http\Controllers\AdminV2;

use App\Models\ImageFolder;
use App\Models\VectorFolder;
use App\Models\VideoFolder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FolderController extends Controller
{
    public function index()
    {
        $index_url = route('admin.folders.datatable');
        $edit_url = route('admin.sessions.edit', ['id'=>0 , 'type'=> "images"]);
        $destroy_url = route('admin.folders.destroy', 0);

        $object = new ImageFolder();

        $html_breadcrumbs = [
            'title' => __('views.Folders'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = auth()->user()->role == 'admin' ? route('admin.folders.create') : false;

        $subheader_actions = [
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.folders.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];
        $count_key = 'images_count';
        return view(
            'admin_v2.folder.index',
            compact(
                'html_breadcrumbs',
                'html_new_path',
                'subheader_actions',
                'index_url',
                'edit_url',
                'destroy_url',
                'object',
                'count_key'
            )
        );
    }

    public function datatable(Request $request)
    {
        $query = ImageFolder::withCount('images')->withCount(['images as published' => function ($q) {
            $q->where('images.status', 'active');
        }])->withCount(['images as pending' => function ($q) {
            $q->where('images.status', 'pending');
        }]);
        if ($request->input('query.generalSearch'))
            $query->where('folder', 'like', "%" . \request('query.generalSearch') . "%");
        if ($request->input('query.content_status') == 'pending')
            $query->whereHas('images', function ($q) {
                $q->where('images.status', 'pending');
            });
        if ($request->input('query.content_status') == 'published')
            $query->whereDoesntHave('images', function ($q) {
                $q->where('images.status', 'pending');
            });
        $data = process_datatable_query($query);
        return $data;
    }

    public function create()
    {
        $index_url = route('admin.folders.index');
        $store_url = route('admin.folders.store');

        $html_breadcrumbs = [
            'title' => __('views.Folders'),
            'subtitle' => __('views.New'),
        ];

        return view('admin_v2.folder.create', compact('html_breadcrumbs', 'index_url', 'store_url'));
    }

    public function store(Request $request)
    {
        $rules = [
            'folder' => 'required|unique:image_folders',
        ];

        $this->validate($request, $rules);

        $sql = new ImageFolder();
        $sql->folder = trim($request->folder);
        $sql->save();
        \Session::flash('success', trans('admin.success_added'));
        return redirect()->route('admin.folders.index');
    }

    public function edit($id)
    {
        $folder = ImageFolder::find($id);
        $index_url = route('admin.folders.index');
        $update_url = route('admin.folders.update', $id);

        $html_breadcrumbs = [
            'title' => __('views.Folders'),
            'subtitle' => __('views.Edit'),
        ];

        return view(
            'admin_v2.folder.edit',
            compact('html_breadcrumbs', 'index_url', 'update_url', 'folder')
        );
    }

    public function update(Request $request, $id)
    {
        $folder = ImageFolder::find($id);

        if (!isset($folder)) {
            return redirect()->route('admin.folders.index');
        }
        $rules = [
            'folder' => 'required|unique:image_folders,folder,' . $id,
        ];

        $this->validate($request, $rules);

        $folder->folder = trim($request->folder);
        $folder->save();

        \Session::flash('success', trans('misc.success_update'));

        return redirect()->route('admin.folders.index');
    }

    public function destroy($id)
    {
        // TODO allow multiple delete
        $id = explode(',', $id)[0];
        $folder = ImageFolder::doesnthave('images')->find($id);

        if (!$folder || $folder->id == 1) {
            $error_message = trans('admin.access_denied_you_have_content');
            \Session::flash('error', $error_message);
            return redirect()->route('admin.folders.index');
        } else {
            $folder->delete();
            \Session::flash('success', trans('misc.success_delete'));
            return redirect()->route('admin.folders.index');
        }
    }

    public function index_video()
    {


        $index_url = route('admin.videos.folders.datatable');
        $edit_url = route('admin.sessions.edit', ['id'=>0 , 'type'=> "videos"]);
        $destroy_url = route('admin.videos.folders.destroy', 0);

        $object = new VideoFolder();

        $html_breadcrumbs = [
            'title' => __('views.Folders'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = auth()->user()->role == 'admin_video' ? route('admin.videos.folders.create') : false;
        $subheader_actions = [
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.videos.folders.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        $is_videos_site = true;
        $count_key = 'videos_count';
        return view(
            'admin_v2.folder.index',
            compact(
                'is_videos_site',
                'html_breadcrumbs',
                'html_new_path',
                'subheader_actions',
                'index_url',
                'edit_url',
                'destroy_url',
                'object',
                'count_key'
            )
        );
    }

    public function datatable_video(Request $request)
    {
        $query = VideoFolder::withCount('videos')->withCount(['videos as published' => function ($q) {
            $q->where('videos.status', 'active');
        }])->withCount(['videos as pending' => function ($q) {
            $q->where('videos.status', 'pending');
        }]);
        if ($request->input('query.generalSearch'))
            $query->where('folder', 'like', "%" . \request('query.generalSearch') . "%");
        if ($request->input('query.content_status') == 'pending')
            $query->whereHas('videos', function ($q) {
                $q->where('videos.status', 'pending');
            });
        if ($request->input('query.content_status') == 'published')
            $query->whereDoesntHave('videos', function ($q) {
                $q->where('videos.status', 'pending');
            });
        $data = process_datatable_query($query);
        return $data;
    }

    public function create_video()
    {


        $index_url = route('admin.videos.folders.index');
        $store_url = route('admin.videos.folders.store');
        $is_videos_site = true;
        $html_breadcrumbs = [
            'title' => __('views.Folders'),
            'subtitle' => __('views.New'),
        ];

        $is_videos_site = true;

        return view('admin_v2.folder.create', compact('html_breadcrumbs', 'index_url', 'store_url', 'is_videos_site'));
    }

    public function store_video(Request $request)
    {


        $rules = [
            'folder' => 'required|unique:video_folders',
        ];

        $this->validate($request, $rules);

        $sql = new VideoFolder();
        $sql->folder = trim($request->folder);
        $sql->save();

        \Session::flash('success', trans('admin.success_added'));

        return redirect()->route('admin.videos.folders.index');
    }

    public function edit_video($id)
    {


        $folder = VideoFolder::find($id);
        $is_videos_site = true;
        $index_url = route('admin.videos.folders.index');
        $update_url = route('admin.videos.folders.update', $id);

        $html_breadcrumbs = [
            'title' => __('views.Folders'),
            'subtitle' => __('views.Edit'),
        ];

        $is_videos_site = true;

        return view(
            'admin_v2.folder.edit',
            compact('is_videos_site', 'html_breadcrumbs', 'index_url', 'update_url', 'folder', 'is_videos_site')
        );
    }

    public function update_video(Request $request, $id)
    {


        $folder = VideoFolder::find($id);

        if (!isset($folder)) {
            return redirect()->route('admin.videos.folders.index');
        }

        $rules = [
            'folder' => 'required|unique:video_folders,folder,' . $id,
        ];

        $this->validate($request, $rules);

        $folder->folder = trim($request->folder);
        $folder->save();

        \Session::flash('success', trans('misc.success_update'));

        return redirect()->route('admin.videos.folders.index');
    }

    public function destroy_video($id)
    {


        // TODO allow multiple delete
        $id = explode(',', $id)[0];
        $folder = VideoFolder::doesnthave('videos')->find($id);

        if (!$folder || $folder->id == 1) {
            $error_message = trans('admin.access_denied_you_have_content');
            \Session::flash('error', $error_message);
            return redirect()->route('admin.videos.folders.index');
        } else {
            $folder->delete();
            \Session::flash('success', trans('misc.success_delete'));
            return redirect()->route('admin.videos.folders.index');
        }
    }


    //vectors functions

    public function index_vector(Request $request)
    {


        $index_url = route('admin.vectors.folders.datatable');
        $edit_url = route('admin.vectors.folders.edit', 0);
        $destroy_url = route('admin.vectors.folders.destroy', 0);


        $object = new VectorFolder();

        $html_breadcrumbs = [
            'title' => __('views.Folders'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = auth()->user()->role == 'admin_vector' ? route('admin.vectors.folders.create') : false;
        $subheader_actions = [
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.vectors.folders.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];
        $is_vectors_site = true;
        $count_key = 'vectors_count';

        return view(
            'admin_v2.folder.index',
            compact(
                'is_vectors_site',
                'html_breadcrumbs',
                'html_new_path',
                'subheader_actions',
                'index_url',
                'edit_url',
                'destroy_url',
                'object',
                'count_key'
            )
        );
    }


    public function datatable_vector(Request $request)
    {
        $query = VectorFolder::withCount('vectors')->withCount(['vectors as published' => function ($q) {
            $q->where('vectors.status', 'active');
        }])->withCount(['vectors as pending' => function ($q) {
            $q->where('vectors.status', 'pending');
        }]);
        if ($request->input('query.generalSearch'))
            $query->where('folder', 'like', "%" . \request('query.generalSearch') . "%");
        if ($request->input('query.content_status') == 'pending')
            $query->whereHas('vectors', function ($q) {
                $q->where('vectors.status', 'pending');
            });
        if ($request->input('query.content_status') == 'published')
            $query->whereDoesntHave('vectors', function ($q) {
                $q->where('vectors.status', 'pending');
            });
        $data = process_datatable_query($query);
        return $data;
    }

    public function create_vector()
    {


        $index_url = route('admin.vectors.folders.index');
        $store_url = route('admin.vectors.folders.store');
        $is_videos_site = true;
        $html_breadcrumbs = [
            'title' => __('views.Folders'),
            'subtitle' => __('views.New'),
        ];

        $is_vectors_site = true;

        return view('admin_v2.folder.create', compact('html_breadcrumbs', 'index_url', 'store_url', 'is_vectors_site'));
    }

    public function store_vector(Request $request)
    {


        $rules = [
            'folder' => 'required|unique:vector_folders',
        ];

        $this->validate($request, $rules);

        $sql = new VectorFolder();
        $sql->folder = trim($request->folder);
        $sql->save();

        \Session::flash('success', trans('admin.success_added'));

        return redirect()->route('admin.vectors.folders.index');
    }

    public function edit_vector($id)
    {


        $folder = VectorFolder::find($id);
        $is_vectors_site = true;
        $index_url = route('admin.vectors.folders.index');
        $update_url = route('admin.vectors.folders.update', $id);

        $html_breadcrumbs = [
            'title' => __('views.Folders'),
            'subtitle' => __('views.Edit'),
        ];

        $is_vectors_site = true;

        return view(
            'admin_v2.folder.edit',
            compact('is_vectors_site', 'html_breadcrumbs', 'index_url', 'update_url', 'folder', 'is_vectors_site')
        );
    }

    public function update_vector(Request $request, $id)
    {


        $folder = VectorFolder::find($id);

        if (!isset($folder)) {
            return redirect()->route('admin.vectors.folders.index');
        }

        $rules = [
            'folder' => 'required|unique:vector_folders,folder,' . $id,
        ];

        $this->validate($request, $rules);

        $folder->folder = trim($request->folder);
        $folder->save();

        \Session::flash('success', trans('misc.success_update'));

        return redirect()->route('admin.vectors.folders.index');
    }

    public function destroy_vector($id)
    {


        // TODO allow multiple delete
        $id = explode(',', $id)[0];
        $folder = VectorFolder::doesnthave('vectors')->find($id);

        if (!$folder || $folder->id == 1) {
            $error_message = trans('admin.access_denied_you_have_content');
            \Session::flash('error', $error_message);
            return redirect()->route('admin.vectors.folders.index');
        } else {
            $folder->delete();
            \Session::flash('success', trans('misc.success_delete'));
            return redirect()->route('admin.vectors.folders.index');
        }
    }


}
