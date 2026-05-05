<?php

namespace App\Http\Controllers\AdminV2;

use App\Jobs\SendNewsletter;
use App\Models\Newsletter;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $html_new_path = route('admin.newsletter.create');
        $html_breadcrumbs = [
            'title' => __('views.Newsletter'),
            'subtitle' => __('views.Newsletter'),
            'datatable' => true,
        ];
        if (!$request->datatable)
            return view('admin_v2.newsletter.index', compact('html_breadcrumbs', 'html_new_path'));
        $data = process_datatable_query(Newsletter::query(), function (
            $query,
            $search
        ) {
            return $query
                ->where(function ($query) use ($search) {
                    $query->where('subject', 'like', '%' . $search . '%');
                });
        });

        return $data;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $html_breadcrumbs = [
            'title' => __('views.Newsletter'),
            'subtitle' => __('views.Newsletter'),
        ];
        return view('admin_v2.newsletter.create', compact('html_breadcrumbs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'subject' => ['required'],
            'html' => ['required'],
            'receivers' => ['required', Rule::in(['specific', 'all'])],
            'specific_users' => [Rule::requiredIf(function () {
                \request()->receivers == 'specific';
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
        $request->merge(['from_name' => config('newsletter.mail.from.name'), 'from_email' => config('newsletter.mail.from.address'), 'specific_users' => ($request->specific_users ? implode(',', $request->specific_users) : '')]);
        $newsletter = Newsletter::create($request->only('subject', 'from_name', 'from_email', 'html', 'receivers', 'specific_users'));

        SendNewsletter::dispatch($newsletter);

        \Session::flash('success', trans('admin.success_add'));
        return redirect()->route('admin.newsletter.index');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
