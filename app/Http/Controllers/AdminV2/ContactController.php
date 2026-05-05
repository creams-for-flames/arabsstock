<?php

namespace App\Http\Controllers\AdminV2;


use App\Export\ContactExport;
use App\Jobs\ExportModelsPdf;
use App\Jobs\UploadAttatchmentToS3;
use App\Models\Attatchments;
use App\Models\Contact;
use App\Models\Skills;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use SendGrid\Mail\Mail;

class ContactController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $index_url = route('admin.super.contact.datatable');
        $edit_url = route('admin.super.contact.edit', 0);
        $destroy_url = route('admin.super.contact.destroy', 0);

        $object = new Contact();

        $html_breadcrumbs = [
            'title' => __('views.contact'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                ]
            ],
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.super.contact.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        $is_super_site = true;

        return view(
            'admin_v2_super.contact.index',
            compact('html_breadcrumbs', 'subheader_actions', 'index_url', 'edit_url', 'destroy_url', 'is_super_site')
        );
    }

    public function datatable(Request $request)
    {
        $data = process_datatable_query(Contact::with('images')->orderBy('id', 'desc'), function (
            $query,
            $search
        ) {
            return $query
                ->where(function ($query) use ($search) {
                    if (\is_numeric($search) && \in_array(strlen($search), [1, 2, 3])) {
                        list($year, $month, $day) = explode("-", date("Y-m-d"));
                        $search = $year - $search;
                    }
                    $query->where('mobile', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('birth_date', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }


    public function edit($id)
    {
        $contact = Contact::findOrFail($id);
        $index_url = route('admin.super.contact.index');
        $html_breadcrumbs = [
            'title' => __('views.contact'),
            'subtitle' => __('views.Edit'),
        ];
        $is_super_site = true;
        return view('admin_v2_super.contact.edit', compact('html_breadcrumbs', 'contact', 'index_url', 'is_super_site'));
    }

    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $contact->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'nationality' => $request->nationality,
            'nationality_one' => $request->nationality_one,
            'skill' => $request->skill,
            'city' => $request->city,
            'sex' => $request->sex,
            'social' => $request->social,
            'length' => $request->length,
            'weight' => $request->weight,
            'work_field' => $request->work_field,
            'birth_date' => $request->birth_date,
            'skills' => $request->skills,
            'status' => $request->status,
            'date' => $request->date,
        ]);
        return redirect()->route('admin.super.contact.index', ['return_page' => $request->return_page]);
    }


    public function upload(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $this->validate($request, [
            'file' => ['required', 'mimes:jpg,png,jpeg']
        ]);
        if ($request->has('file')) {
            $file = $request->file('file');
            $path = $file->store('uploads2/contact', ['disk' => 'public']);
            $image = $contact->images()->create([
                'type' => 1,
                'image' => $path,
            ]);
            dispatch(new UploadAttatchmentToS3($path))->onConnection('sync');
            return ['status' => 1, 'id' => $image->id, 'path' => cdn($path)];
        }
        return ['status' => 0];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        // TODO allow multiple delete

        $id = explode(',', $id)[0];
        Contact::where('id', $id)->delete($id);
        return redirect()->route('admin.super.contact.index', ['return_page' => $request->return_page]);
    }

    function reverse_birthday($age)
    {
        list($year, $month, $day) = explode("-", date("Y-m-d"));
        $range = $year - $age;
        return strtotime("{$range}-{$month}-{$day}");
    }

    public function export()
    {
        dispatch(new ExportModelsPdf());
        return redirect()->back()->with('success', 'سيتم تجهيز القائمة وارسال ايميل ');
    }


    public function delete_image($id)
    {
        Attatchments::findOrFail($id)->delete();
        return ['status' => 1];
    }
}
