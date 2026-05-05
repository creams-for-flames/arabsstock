<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Image,Video,Vector};

class SimilarFilesController extends Controller
{
    public function SimilarFiles($type,$section,$id)
    {
        $data = NULL;
        $sections = ['image','clip','illustration'];
        $types = ['photos','videos','vectors'];
        if (!(isset($id) && is_numeric($id))  || !in_array($type , $types) || !in_array($section , $sections)) {
            abort(404);
        }
        $lang = app()->getLocale();
        $file = $this->GetDataFromSection($section,$id);
        $stock_type = $this->GetStockFromSection($section,$file->id)??'';

        switch ($type) {
            case 'photos':
                $file->tags_implode = $file->tags()->where('local', $lang)->pluck('title')->toArray();
                $results = $this->GetDataFromElasticSearch('images',$file->title. ' ' . implode(' ', $file->tags_implode));
                return view("search.similar.images", compact('file', 'results','section','stock_type'));

                break;
            case 'videos':
                $file->tags_implode = $file->tags()->where('local', $lang)->pluck('title')->toArray();
                $results = $this->GetDataFromElasticSearch('videos',$file->title. ' ' . implode(' ', $file->tags_implode));
                return view("search.similar.videos", compact('file', 'results','section','stock_type'));

                break;
            case 'vectors':
                $file->tags_implode = $file->tags()->where('local', $lang)->pluck('title')->toArray();
                $results = $this->GetDataFromElasticSearch('vectors',$file->title. ' ' . implode(' ', $file->tags_implode));
                return view('search.similar.vectors', compact('file', 'results','section','stock_type'));
               
                break;
            
        }
        
    }

    public function GetDataFromElasticSearch($type , $title)
    {
        $results = \App\Helper::similar_search_in_elasticsearch($type, $title, [], 106);
        return $results;
    }

    public function GetDataFromSection($section , $id)
    {
        $file = NULL;
        switch ($section) {
            case 'image':
                $file = Image::with('tags')->findOrFail($id);
                break;
            case 'clip':
                $file = Video::with('tags')->findOrFail($id);
                break;
            case 'illustration':
                $file = Vector::with('tags')->findOrFail($id);
                break;

        }

        return $file;
    }

    public function GetStockFromSection($section,$id)
    {
        $data = NULL;
        switch ($section) {
            case 'image':
                $data = trans('global.Stock_Id_Image',['id' => $id]);
                break;
            case 'clip':
                $data = trans('global.Stock_Id_Video',['id' => $id]);
                break;
            case 'illustration':
                $data = trans('global.Stock_Id_Vector',['id' => $id]);
                break;

        }

        return $data;
    }
}
