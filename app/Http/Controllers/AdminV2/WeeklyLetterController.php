<?php

namespace App\Http\Controllers\AdminV2;

use App\Jobs\SendWeeklyLetter;
use App\Models\ImageCategory;
use App\Models\WeeklyLetter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Vector;
use App\Models\Video;
use App\Models\WeeklyLetterClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WeeklyLetterController extends Controller
{
    public function __construct()
    {
        $this->middleware('WeeklyLetter');
    }

    public function index(Request $request)
    {
        $html_new_path = route('admin.weekly_letters.create');
        $show_link = route('admin.weekly_letters.show',0);
        $html_breadcrumbs = [
            'title' => __('views.WeeklyLetter'),
            'subtitle' => __('views.WeeklyLetter'),
            'datatable' => true,
        ];
        if (!$request->datatable)
            return view('admin_v2.weekly_letters.index', compact('html_breadcrumbs', 'html_new_path','show_link'));
        $data = process_datatable_query(WeeklyLetterClient::query());

        return $data;

    }

    public function create()
    {
        $html_breadcrumbs = [
            'title' => __('views.WeeklyLetter'),
            'subtitle' => __('views.WeeklyLetter'),
        ];
        $file_type = ["file","category"];
        $fileable_type = ["image","vector","video"];
        return view('admin_v2.weekly_letters.create', compact('html_breadcrumbs','file_type','fileable_type'));
    }

    public function store(Request $request)
    {
       $validator =  Validator::make($request->all(), [
            'title' => ['required'],
            'target' => ['required', Rule::in(['custom', 'all'])],
            'custom_target' => [Rule::requiredIf(function () {
              return   \request()->target === 'custom';
            }), function ($attribute, $value, $fail) {
                foreach ($value as $email) {
                    $data = [
                        'email' => $email
                    ];
                    $validator = Validator::make($data, ['email' => 'email']);
                    if ($validator->fails()) {
                        $fail($email . ' is invalid.');
                    }
                }
            },],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ],422);
        }
        $keys = ["file_type","fileable_type","file_title","file_url","fileable_id"];
        $data = $request->only($keys);
        
        $request->merge(['custom_target' => ($request->custom_target ? implode(',', $request->custom_target) : '')]);
        for ($i = 0; $i < count($data['file_type']); $i++) {

            $output[] = [
                "file_type" => $data['file_type'][$i],
                "fileable_type" => $data['fileable_type'][$i],
                "file_title" =>$data['file_title'][$i],
                "file_url" => $data['file_url'][$i],
                "fileable_id" => $data['fileable_id'][$i]
            ];
        }
        $validator = Validator::make([
            'data' => $output
        ], [
            'data'=> ["required",   function ($attribute, $value, $fail) use ($output) {
               $files_count = collect($value)->where('file_type', 'file')->count();
               $categories_count = collect($value)->where('file_type', 'category')->count();
                if ($files_count !==  6) {
                    $fail(__("validation.files_equal_6"));
                }elseif ($categories_count !== 6) {
                    $fail(__("validation.categories_equal_6"));

                }
            }],
            'data.*.file_type' => 'required',
            'data.*.fileable_type' => 'required',
            'data.*.file_title' => ['string', 'required_if:data.*.file_type,===,category'],
            'data.*.file_url' => ['url', 'required_if:data.*.file_type,===,category'],
            'data.*.fileable_id' => [
            'required',
            function ($attribute, $value, $fail) use ($output) {
                $index = explode('.', $attribute)[1];
                if (! DB::table(strtolower($output[$index]['fileable_type'].'s'))->where('id', $value)->exists()) {
                    $fail($attribute . ' does not exist in the table ' . strtolower($output[$index]['fileable_type'])."s value: {$value}");
                }
            }
            ],

        ]);
  
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ],422);
        }
        $output = collect($output)->map(function($q){
            $fileable_type = $q['fileable_type'];
            switch ($fileable_type) {
                case 'image':
                    $className = Image::class;
                    break;
                case 'video':
                    $className = Video::class;
                    break; 
                case 'vector':
                    $className = Vector::class;
                    break;                

            }
            $q['fileable_type'] = $className;
            return $q;
        });
        $WeeklyLetter = WeeklyLetterClient::create($request->only('sent','title','target','custom_target'));

        $WeeklyLetter->files()->createMany($output->toArray());
        
        return response()->json([
            'success' => true,
            'message'=>trans('admin.success_add'),
            "redirect"=>route('admin.weekly_letters.index')
        ]);  

  
  

    }
    public function show(Request $request, $id){
        $html_breadcrumbs = [
            'title' => __('views.WeeklyLetter'),
            'subtitle' => __('views.WeeklyLetter'),
        ];
        $show_link = route('admin.weekly_letters.show',$id);

            $WeeklyLetter = WeeklyLetterClient::with(['files'=>function($q){
            $q->select("id",
            "weekly_letter_client_id",
            "fileable_id",
            "fileable_type",
            "file_type",
            "image_generated",
            "file_title",
            "file_url");
          },'files.fileable'])
          ->withCount([
            'files as files_generated'=> function ($query)  {
                $query->where('image_generated' , true);
            },
            'files as files_pending'=> function ($query)  {
                $query->where('image_generated' , false);
            },
            'files as files_count'=> function ($query)  {
                $query->where('file_type' , 'file');
            },
            'files as categories_count'=> function ($query)  {
                $query->where('file_type' , 'category');
            },
          ])
          ->findOrFail($id);



         $files = $WeeklyLetter->files()->orderBy('file_type','desc')->get();
        $progress = 0;
        if ($WeeklyLetter->target_count){
            $progress = round(($WeeklyLetter->users()->count()/$WeeklyLetter->target_count)*100);
        }
        if (($WeeklyLetter->files_generated) && ($WeeklyLetter->files_pending === 0))
        $mail = (new \App\Mail\WeeklyLetterEmail($WeeklyLetter,false))->render();
        else
        $mail = false;

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message'=>__("admin.mail_in_processing",['files_generated'=>$WeeklyLetter->files_generated,'files_pending'=>$WeeklyLetter->files_pending]),
                'mail'=>$mail,
            ]); 
        }
        return view('admin_v2.weekly_letters.show', compact('html_breadcrumbs','WeeklyLetter','progress','files','mail','id','show_link'));
    }

    public function submit($id)
    {
        $WeeklyLetter = WeeklyLetterClient::with('files.fileable')->where('status','pending')->whereHas('files',function($q){
            $q->where('image_generated',true);
        })->find($id);
        if ($WeeklyLetter) {
            $allChildrenActive = $WeeklyLetter->files->every(function ($child) {
                return $child->image_generated;
            });
            if ($allChildrenActive) {
                $WeeklyLetter->status = 'active';
                $WeeklyLetter->save();
                SendWeeklyLetter::dispatch($WeeklyLetter->id);
                Session::flash('success', trans('admin.weekly_letter_sent'));
                return redirect()->route('admin.weekly_letters.index');
            }else{
               return redirect()->back()->with('error', __('admin.weekly_letter_sent'));

            }
        }
        return redirect()->back()->with('error', __('admin.weekly_letter_sent'));

    }
}
