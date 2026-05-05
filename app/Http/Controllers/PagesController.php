<?php

namespace App\Http\Controllers;

use App\Jobs\UploadAttatchmentToS3;
use App\Mail\NewBusinessContact;
use App\Models\Pages;
use App\Models\Cities;
use App\Models\Skills;
use App\Models\Ticket;
use App\Models\Contact;
use App\Models\Countries;
use App\Models\Evaluation;
use App\Models\Attatchments;
use Illuminate\Http\Request;
use App\Models\SkillsContact;
use App\Mail\NewMessageContact;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Input as Input;
use Illuminate\Support\Facades\Validator;
use App\Rules\Mobile;

class PagesController extends Controller
{

    public function image_extensions()
    {
        return array('jpg', 'png', 'jpeg', 'gif', 'bmp');
    }


    public function show($slug)
    {

        $page = Pages::where('slug', $slug)
            ->firstOrFail();

        return view('pages.show', [
            'page' => $page,
        ]);

    }// End Method


    public function support()
    {
        $auth_user = auth()->user() ?? NULL;

        return view('pages.support', ['auth_user' => $auth_user]);

    }// End Method


    public function contact()
    {
        $ar_list = ["EG", "DZ", "SS", "SD", "IQ", "MA", "SA", "YE", "SY", "TN", "JO", "AE", "LB", "LY", "PS", "QA", "OM", "BH", "MR", "KW", "DJ", "KM",
        ];
        $countries = Countries::select('name_ar', 'name_en', 'iso_code_2', 'id')->get();
        $countries_ar = $countries->whereIn('iso_code_2', $ar_list)->all();

        //   return $countries;
        return view('pages.contact', ['countries' => $countries, 'countries_ar' => $countries_ar]);

    }// End Method


    public function contact_post(Request $request)
    {


        $roles = [

            'email' => 'required|email',
            'name' => 'required|string',
            'mobile' => ['required', new Mobile()],
            'country_id' => 'required',
            'city_id' => 'required',
            'sex' => 'required',
            // 'social' => 'required|url',
            'length' => 'required|regex:/^[0-9]{2,3}$/',
            'weight' => 'required|regex:/^[0-9]{2,3}$/',
            'work_field' => 'string',
            'birth_date' => 'required',
            // 'skills' => 'required|array',
            'image.*' => ['required', 'file', 'mimes:jpg,png,jpeg'],
            '_recaptcha' => ['required', function ($attribute, $value, $fail) {
                $response = (new \ReCaptcha\ReCaptcha(config('app.recaptcha.secret_key'), new \ReCaptcha\RequestMethod\CurlPost()))
                    ->setExpectedAction('contact')
                    ->verify($value);
                if (!$response->isSuccess()) {
                    $fail(__('validation.recaptcha_msg'));
                }
            },],
        ];
        $validator = Validator::make($request->all(), $roles, ['_recaptcha.required' => __('validation.recaptcha_msg')]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $contact = new Contact();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->mobile = $request->mobile;
        $contact->nationality_one = $request->nationality_id;
        $contact->nationality = $request->country_id;
        $contact->city = $request->city_id;
        $contact->sex = $request->sex;
        $contact->length = $request->length;
        $contact->weight = $request->weight;
        $contact->work_field = $request->work_field;
        $contact->birth_date = $request->birth_date;
        $contact->skill = $request->skill;
        $contact->save();
        if ($request->hasFile('images')) {
            $destinationPath = public_path('uploads2/contact');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $xy = $request->images;
            foreach ($xy as $i => $value) {
                $extension = strtolower($request->images[$i]->getClientOriginalExtension());
                if (in_array($extension, $this->image_extensions())) {
                    $fileName = uniqid() . '.' . $extension;
                    $request->images[$i]->move($destinationPath, $fileName);
                    $atta = new Attatchments;
                    $atta->attatchmentable_id = $contact->id;
                    $atta->attatchmentable_type = 'App\Models\Contact';
                    $atta->type = 1;
                    $atta->image = 'uploads2/contact/' . $fileName;
                    $atta->save();
                    dispatch(new UploadAttatchmentToS3('uploads2/contact/' . $fileName));
                }
            }
        }
        return back()->with('success', __('global.create'));

    }// End Method


    public function storeTicket(Request $request)
    {
        if (!env('SUPPORT_ENABLED', false))
            return response(['message' => __('Sorry not available now')]);
        $data = request()->validate([
            'name' => 'required|max:80',
            'email' => 'required|email|max:50',
            'mobile' => 'required|max:15',
            'message' => 'required|max:500',
            '_recaptcha' => ['required', function ($attribute, $value, $fail) {
                $response = (new \ReCaptcha\ReCaptcha(config('app.recaptcha.secret_key'), new \ReCaptcha\RequestMethod\CurlPost()))
                    ->setExpectedAction('support')
                    ->verify($value);
                if (!$response->isSuccess()) {
                    $fail(__('validation.recaptcha_msg'));
                }
            },],
        ], ['_recaptcha.required' => __('validation.recaptcha_msg')]);
        $data['ip'] = $request->ip();
        if (Ticket::where('ip', $request->ip())->where('created_at', '>', now()->subMinutes(20))->count() >= 2)
            return response(['message' => trans("global.You can't send message right now")]);
        $ticket = Ticket::create($data);
        $message = (new NewMessageContact($ticket));
        Mail::queue($message);
        return response(['message' => trans('global.The message was sent successfully')]);

    }// End Method


    public function getCity(Request $request)
    {
        // dd($request->country_id);
        $cities = Cities::where('country_id', $request->country_id)->get();
        return $cities;

    }


    public function evaluation()
    {

        return view('pages.evaluation', []);

    }// End Method

    public function storeEvaluation()
    {
        $data = request()->validate([
            'message' => 'required|max:500',
            '_recaptcha' => ['required', function ($attribute, $value, $fail) {
                $response = (new \ReCaptcha\ReCaptcha(config('app.recaptcha.secret_key'), new \ReCaptcha\RequestMethod\CurlPost()))
                    ->setExpectedAction('evaluation')
                    ->verify($value);
                if (!$response->isSuccess()) {
                    $fail(__('validation.recaptcha_msg'));
                }
            },]
        ], ['_recaptcha.required' => __('validation.recaptcha_msg')]);
        $evaluation = Evaluation::create($data);
        return redirect()->back()->with('message', trans('global.The message was sent successfully'));
    }// End Method

    public function business(Request $request)
    {
        if ($request->method() == 'GET')
            return view('pages.business');
        $data = $request->validate([
            '_recaptcha' => ['required', function ($attribute, $value, $fail) {
                $response = (new \ReCaptcha\ReCaptcha(config('app.recaptcha.secret_key'), new \ReCaptcha\RequestMethod\CurlPost()))
                    ->setExpectedAction('business')
                    ->verify($value);
                if (!$response->isSuccess()) {
                    $fail(__('validation.recaptcha_msg'));
                }
            },],
            'name' => 'required|max:80',
            'entity' => 'required|max:500',
            'email' => 'required|email|max:50',
            'mobile' => 'required|max:15',
        ], ['_recaptcha.required' => __('validation.recaptcha_msg')]);
        $data['type'] = 'business';
        $ticket = Ticket::create($data);
        $message = (new NewBusinessContact($ticket));
        Mail::queue($message);
        return response(['message' => __('The request has been sent successfully .. You will be answered with details immediately through the email.')]);
    }
}
