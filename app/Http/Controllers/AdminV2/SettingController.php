<?php

namespace App\Http\Controllers\AdminV2;


use App\Models\AdminImageSettings;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{


    public function edit()
    {
        $image_tags = image_settings();
        $admin_video_setting = new \App\Models\AdminVideoSettings();
        $video_tags = $admin_video_setting->first();

        $admin_vector_setting = new \App\Models\AdminVectorSettings();
        $vector_tags = $admin_vector_setting->first();

        $image_profit_ratio = $image_tags->profit_ratio;
        $image_en_tags = [];
        $image_ar_tags = [];
        $video_ar_tags = [];
        $video_en_tags = [];

        $vector_ar_tags = [];
        $vector_en_tags = [];


        if ($image_tags->tags_ar_in_home) {
            $image_ar_tags = explode(',', $image_tags->tags_ar_in_home);

        }
        if ($image_tags->tags_en_in_home) {
            $image_en_tags = explode(',', $image_tags->tags_en_in_home);


        }


        if ($video_tags->tags_ar_in_home) {
            $video_ar_tags = explode(',', $video_tags->tags_ar_in_home);

        }
        if ($video_tags->tags_en_in_home) {
            $video_en_tags = explode(',', $video_tags->tags_en_in_home);


        }


        if ($vector_tags->tags_ar_in_home) {
            $vector_ar_tags = explode(',', $vector_tags->tags_ar_in_home);

        }
        if ($vector_tags->tags_en_in_home) {
            $vector_en_tags = explode(',', $vector_tags->tags_en_in_home);


        }


        $update_url = route('admin.settings.update');
        $index_url = route('admin.dashboard.index');
        $select2_tags_images_ar_url = route('images.tags.ar.select2');
        $select2_tags_images_en_url = route('images.tags.en.select2');

        $select2_tags_videos_ar_url = route('videos.tags.ar.select2');
        $select2_tags_videos_en_url = route('videos.tags.en.select2');

        $select2_tags_vectors_ar_url = route('vectors.tags.ar.select2');
        $select2_tags_vectors_en_url = route('vectors.tags.en.select2');


        return view('admin_v2.setting.edit',
            compact(
                'video_tags',
                'video_ar_tags',
                'video_en_tags',

                'vector_tags',
                'vector_ar_tags',
                'vector_en_tags',

                'image_en_tags',
                'image_ar_tags',
                'update_url',

                'select2_tags_images_ar_url',
                'select2_tags_images_en_url',
                'select2_tags_videos_ar_url',
                'select2_tags_videos_en_url',
                'select2_tags_vectors_ar_url',
                'select2_tags_vectors_en_url',
                'index_url',
                'image_profit_ratio',
                'image_tags'
            ));
    }

    public function update(Request $request)
    {

        $rules = [
            'four_k_price' => 'required|numeric|min:0|not_in:0',
            'fhd_price' => 'required|numeric|min:0|not_in:0',
            'hd_price' => 'required|numeric|min:0|not_in:0',
            'sd_price' => 'required|numeric|min:0|not_in:0',
            'profit_ratio' => 'required|numeric|min:0|not_in:0',
            'minimum_payout' => 'required|numeric|min:0|not_in:0',

        ];

        $this->validate($request, $rules);

        $image_tags = image_settings();
        $admin_video_setting = new \App\Models\AdminVideoSettings();
        $admin_vector_setting = new \App\Models\AdminVectorSettings();
        $video_tags = $admin_video_setting->first();
        $vector_tags = $admin_vector_setting->first();


        if ($request->get('tags_images_ar')) {
            $image_tags->tags_ar_in_home = implode(',', $request->get('tags_images_ar'));

        } else {
            $image_tags->tags_ar_in_home = null;
        }
        if ($request->get('tags_images_en')) {


            $image_tags->tags_en_in_home = implode(',', $request->get('tags_images_en'));
        } else {
            $image_tags->tags_en_in_home = null;
        }
        $image_tags->profit_ratio = $request->get('profit_ratio');
        $image_tags->minimum_payout = $request->get('minimum_payout');
        if ($request->file('model_releas')) {
            $file = $request->file('model_releas');
            $extension = $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $file->move($destinationPath, "releas_model_form.pdf");
            $image_tags->releas_model = "releas_model_form.pdf";
        }
        $image_tags->save();


        if ($request->get('tags_videos_ar')) {


            $video_tags->tags_ar_in_home = implode(',', $request->get('tags_videos_ar'));
        } else {
            $video_tags->tags_ar_in_home = null;
        }
        if ($request->get('tags_videos_en')) {

            $video_tags->tags_en_in_home = implode(',', $request->get('tags_videos_en'));
        } else {
            $video_tags->tags_en_in_home = null;
        }


        $video_tags->four_k_price = $request->get('four_k_price');
        $video_tags->fhd_price = $request->get('fhd_price');
        $video_tags->hd_price = $request->get('hd_price');
        $video_tags->sd_price = $request->get('sd_price');


        $video_tags->save();


        if ($request->get('tags_vectors_ar')) {


            $vector_tags->tags_ar_in_home = implode(',', $request->get('tags_vectors_ar'));
        } else {
            $vector_tags->tags_ar_in_home = null;
        }
        if ($request->get('tags_vectors_en')) {

            $vector_tags->tags_en_in_home = implode(',', $request->get('tags_vectors_en'));
        } else {
            $vector_tags->tags_en_in_home = null;
        }


        $vector_tags->save();


        \Session::flash('success', trans('admin.success_update'));

        return redirect()->route('admin.settings.edit');
    } //<--- End Method


    public function tags_ar_image_select2(Request $request)
    {
        $search = $request->get('q', '');
        $data = Tag::has('images')->select('title')->distinct('title')
            ->where('local', 'ar')
            ->where('title', 'like', '%' . $search . '%')
            ->paginate(10)->toArray();
        return $data;
    }

    public function tags_en_image_select2(Request $request)
    {
        $search = $request->get('q', '');
        $data = Tag::has('images')->select('title')->distinct('title')
            ->where('local', 'en')
            ->where('title', 'like', '%' . $search . '%')
            ->paginate(10)->toArray();

        return $data;
    }

    public function tags_ar_video_select2(Request $request)
    {
        $search = $request->get('q', '');
        $data = Tag::has('videos')->select('title')->distinct('title')->whereHas('videos', function ($q) {
            $q->where('is_uploaded', '1');
            $q->where('status', 'active');
        })->where('local', 'ar')->where('title', 'like', '%' . $search . '%')->paginate(10)->toArray();

        return $data;
    }

    public function tags_en_video_select2(Request $request)
    {
        $search = $request->get('q', '');
        $data = Tag::has('videos')->select('title')->distinct('title')->whereHas('videos', function ($q) {
            $q->where('is_uploaded', '1');
            $q->where('status', 'active');
        })->where('local', 'en')->where('title', 'like', '%' . $search . '%')->paginate(10)->toArray();

        return $data;
    }
}
