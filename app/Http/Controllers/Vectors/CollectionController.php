<?php

namespace App\Http\Controllers\Vectors;

use App\Http\Controllers\Controller;
use App\Models\CollectionVideo;
use App\Models\VectorCollection;
use Illuminate\Support\Facades\Auth;
use App\Models\CollectionVector;
use App\Models\CollectionImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CollectionController extends Controller
{


    protected function validator(array $data)
    {

        Validator::extend('ascii_only', function ($attribute, $value, $parameters) {
            return !preg_match('/[^x00-x7F\-]/i', $value);
        });


        return Validator::make($data, [
            'title' => 'required|max:25|min:2',
        ]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
    
            $validator = $this->validator($input);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray(),
                ]);
            }
    
            $sql = new VectorCollection();
            $sql->title = trim($request->title);
            $sql->type = 'public';
            $sql->user_id = Auth::user()->id;
            $sql->save();
    
            $idCollection = $sql->id;
    
            return response()->json([
                'success' => true,
    
                'id' => $idCollection,
                'title' => $sql->title,
                'data' => '<div class="radio margin-bottom-15">
                                <label class="checkbox-inline addImageCollection text-overflow padding-zero" data-image-id="' . $request->vector_id . '" data-collection-id="' . $idCollection . '">
                                <input class="no-show addListUser" name="checked" type="checkbox" value="true">
                                <span class="input-sm">' . trim(e($request->title)) . '</span>
                                </label>
                            </div>',
            ]);
        }
        $exception = [
            'success' => false,
            'message' => 'Validation Errors',
            'code' => 401,
            'msg' => __('auth.login_required'),
        ];
        throw new HttpResponseException(response()->json($exception, 422));
        

    }//<--- End Method

    // Add Remove Image to Collection
    public function addVectorCollection(Request $request, $id, $id2)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                $collectionsImage = new CollectionVector;
                $collectionsImage = $collectionsImage->where([
                    'collection_id' => $id,
                    'vector_id' => $id2
                ])->first();
    
                // Verify user
                if (isset($collectionsImage) && $collectionsImage->belongsCollection->user_id != Auth::user()->id) {
                    return response()->json([
                        'status' => false,
                        'error' => trans('misc.error'),
                    ]);
                    exit;
                }// <--- Verify user
    
    
                if (isset($collectionsImage)) {
                    $collectionsImage->delete();
    
                    return response()->json([
                        'status' => true,
                        'data' => trans('misc.successfully_removed'),
                    ]);
    
                } else {
                    $saveCollectionsImage = new CollectionVector;
                    $saveCollectionsImage->collection_id = $id;
                    $saveCollectionsImage->vector_id = $id2;
                    $saveCollectionsImage->save();
                    return response()->json([
                        'status' => true,
                        'data' => trans('misc.successfully_added'),
                    ]);
                }
            }
        }
        $exception = [
            'success' => false,
            'message' => 'Validation Errors',
            'code' => 2,
            'msg' => __('auth.login_required'),
        ];
        throw new HttpResponseException(response()->json($exception, 422));
    }

    // Edit Collection
    public function edit(Request $request)
    {

        $input = $request->all();

        $sql = VectorCollection::find($request->id);

        if (!isset($sql) || $sql->user_id != Auth::user()->id) {
            return response()->json([
                'not_authorized' => true,
            ]);
        }

        $validator = $this->validator($input);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ]);
        }

        $sql->title = trim($request->title);
        $sql->type = $request->type;
        $sql->save();

        $idCollection = $sql->id;

        return response()->json([
            'success' => true,
        ]);


    }//<--- End Method

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {

        $collection = VectorCollection::findOrFail($id);
        $collectionsImages = CollectionImage::find($collection->collection_id);

        if (!isset($collection) || $collection->user_id != Auth::user()->id) {
            return redirect()->back();
        }

        if (isset($collectionsImages)) {
            foreach ($collectionsImages as $collectionsImage) {
                $collectionsImage->delete();
            }
        }

        $collection->delete();

        return redirect(Auth::user()->username . '/collections');

    }//<--- End Method

    public function deleteVideoCollection($collectionID, $videoID)
    {

        $collection = VectorCollection::findOrFail($collectionID);
        $collectionsVideo = CollectionVideo::where('collection_id', $collection->id)
            ->where('video_id', $videoID)->first();

        $collectionsVideo->delete();
        return redirect()->back();
    }
}
