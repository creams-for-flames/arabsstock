<?php

// SessionManagementTrait.php

namespace App\Traits;

use App\Jobs\ConvertContractToPDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\ImageFolder;
use App\Models\SessionLocation;
use App\Models\Photographer;
use App\Models\Actor;
use App\Models\Cities;
use App\Models\Countries;
use App\Models\SessionInvoice;
use App\Models\VideoFolder;
use Illuminate\Support\Facades\{Log,Storage};

trait SessionManagementTrait
{
    public function createSession($requestData,$folderType)
    {
        
        try {
            DB::beginTransaction();
            $folder_data = Arr::only($requestData, [
                'folder','country_id','city_id','session_date','notes'
            ]);
            $folder = $folderType::create($folder_data);
            
            $this->syncPhotographersToFolder($folder, Arr::get($requestData, 'photographers', []));
            $this->syncActorsToFolder($folder, Arr::get($requestData, 'actors', []));
            $locationId = Arr::get($requestData, 'location_id');
            $this->getOrCreateLocation($folder,$requestData,$locationId);
            $this->attachInvoices($folder, Arr::get($requestData, 'invoices', []));

            DB::commit();

            return $folder;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function updateSession($requestData,$folderType,$id)
    {
        try {
            DB::beginTransaction();
            $folder_data = Arr::only($requestData, [
                'folder','country_id','city_id','session_date','notes'
            ]);
            $folder = $folderType::findOrFail($id);
            $folder_updated  = $folder->update($folder_data);
            
            if ($folder_updated) {
                $folder = $folderType::findOrFail($id);
            }

            $this->syncPhotographersToFolder($folder, Arr::get($requestData, 'photographers', []));
            $this->syncActorsToFolder($folder, Arr::get($requestData, 'actors', []));
            $locationId = Arr::get($requestData, 'location_id');
            $this->getOrCreateLocation($folder,$requestData,$locationId);
            $this->attachInvoices($folder, Arr::get($requestData, 'invoices', []));

            DB::commit();

            return $folder;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    private function getOrCreateLocation($folder, $requestData, $locationId = null)
    {
        $locationData = Arr::only($requestData, [
            'location_name', 'location_admin', 'location_email',
            'location_mobile', 'license_code', 'location',
        ]);
        $data_location = [
            'name' => $locationData['location_name'],
            'admin' => $locationData['location_admin'],
            'email' => $locationData['location_email'],
            'mobile' => $locationData['location_mobile'],
            'license_code' => $locationData['license_code'],
            'location' => $locationData['location'],
        ];
    
        if ($locationId) {
            // Location ID is provided, attempt to update the existing location
            $location = SessionLocation::find($locationId);
            if ($location) {
                $location->update($data_location);
            }
        } else {
            $location = SessionLocation::firstOrCreate($data_location);
        }
    
        // Attach the location to the folder
        $folder->locations()->sync([$location->id => ['contract' => Arr::get($requestData, 'contract')]]);
        foreach ($folder->locations()->get() as $key => $value) {
            $table = $value->pivot->getTable();
            $user_id = $value->id;
            $folder_id = $folder->id;
            //  dispatch_now(new ConvertContractToPDF($table, $folder_id,$user_id)); 
         }
    }
    
    

    private function syncPhotographersToFolder($folder, $photographersData)
    {
        $data = [];
        $photographer_file = Photographer::class;
        foreach ($photographersData as $photographerData) {
            $Photographer = $photographer_file::
            where('email', $photographerData['email'])
            ->where('id_number', $photographerData['id_number'])
            ->first();
    
            if ($Photographer) {
                $Photographer->update(Arr::except($photographerData, ['contract', 'email','id_number']));
                $photographerId = $Photographer->id;
            } else {
                $photographer = $photographer_file::create(Arr::except($photographerData, 'contract'));
                $photographerId = $photographer->id;
            }
    
            $data[$photographerId]  = ['contract' => $photographerData['contract']];
        }
        //commint delete when update  |  update now not work
        // foreach ($folder->photographers()->get() as $key => $value) {
        //     $contract_file = $value->pivot->contract_file;
        //     if (Storage::disk('s3')->exists($contract_file)) {
        //         Storage::disk('s3')->delete($contract_file);
        //         // dd($contract_file);
        //     }
        //     $value->pivot->update([
        //         'contract_file' => NULL,
        //         'is_uploaded' => 0,
        //     ]);
        // }
            $folder->photographers()->sync($data);
            foreach ($folder->photographers()->get() as $key => $value) {
               $table = $value->pivot->getTable();
               $user_id = $value->id;
               $folder_id = $folder->id;
                // dispatch_now(new ConvertContractToPDF($table, $folder_id,$user_id)); 
            }

    }

    private function syncActorsToFolder($folder, $actorsData)
    {
        $data = [];
        foreach ($actorsData as $actorData) {
            $actor = Actor::
            where('email', $actorData['email'])
            ->where('id_number', $actorData['id_number'])
            ->first();
            if ($actor) {
                $actorId = $actor->id;
                $actor->update(Arr::except($actorData, ['contract','file', 'email','id_number']));
            } else {
                $actor = Actor::create(Arr::except($actorData, ['contract','file']));
                $actorId = $actor->id;
            }
            if (isset($actorData['file']) && $actorData['file']->isValid()) {
                if (isset($actor->file) && Storage::disk('s3')->exists($actor->file)) {
                    Storage::disk('s3')->delete($actor->file);
                }
                $tableDirectory = 'actors';
                $recordIdDirectory = $actorId;
                $randomFileName = uniqid() . '.' . $actorData['file']->getClientOriginalExtension();
                $fullDirectoryPath = "uploads/{$tableDirectory}/{$recordIdDirectory}";
                $actorData['file']->storeAs($fullDirectoryPath, $randomFileName,'s3');
                $file_path = "{$fullDirectoryPath}/{$randomFileName}";
                $actor->update(['file' => $file_path]);
            }
            $data[$actorId] = ['contract' => $actorData['contract']];
    
        }
        $folder->actors()->sync($data);
        foreach ($folder->actors()->get() as $key => $value) {
            $table = $value->pivot->getTable();
            $user_id = $value->id;
            $folder_id = $folder->id;
            //  dispatch_now(new ConvertContractToPDF($table, $folder_id,$user_id)); 
         }
    }
    
    

    private function attachInvoices($folder, $invoicesData)
    {
        foreach ($invoicesData as $invoiceData) {
            // Check if an invoice with the same ID exists (for update)
            $invoiceId = $invoiceData['id'] ?? null;
            $invoice = $invoiceId ? SessionInvoice::find($invoiceId) : new SessionInvoice();
    
            // Update invoice attributes
            $invoice->fill(Arr::except($invoiceData, 'file'));
    
            // Save or update the invoice
            $folder->invoices()->save($invoice);
    
            if (isset($invoiceData['file']) && $invoiceData['file']->isValid()) {
                if (isset($invoice->file) && Storage::disk('s3')->exists($invoice->file)) {
                    Storage::disk('s3')->delete($invoice->file);
                }
                $tableDirectory = 'session_invoices';
                $recordIdDirectory = $invoice->id;
                $randomFileName = uniqid() . '.' . $invoiceData['file']->getClientOriginalExtension();
                $fullDirectoryPath = "uploads/{$tableDirectory}/{$recordIdDirectory}";
                $invoiceData['file']->storeAs($fullDirectoryPath, $randomFileName,'s3');
                $file_path = "{$fullDirectoryPath}/{$randomFileName}";
                $invoice->update(['file' => $file_path]);
            }
        }
    }
    

    private function selectSessionType($type)
    {
        $folder_type = NULL;
        switch ($type) {
            case 'images':
                $folder_type = ImageFolder::class;
                break;
            case 'videos':
                $folder_type = VideoFolder::class;
                break;

        }
        return $folder_type;
    }

    private function getCountries()
    {
        $gulfCountries = array(
            "SA",
            "AE",
            "OM",
            "QA",
            "BH",
            "KW"
        );
        $countries = Countries::whereIn('iso_code_2', $gulfCountries)->get();
        return $countries;
    }

    private function getCities($id)
    {
        $cities = Cities::where('country_id', $id)->get();
        return $cities;

    }


public function convertToPdf()
{
    $html = '<p>Your Summernote content goes here...</p>'; // Replace with your Summernote content
    $pdf = PDF::loadHTML($html);

    // Generate a unique file name for the PDF
    $fileName = 'summernote_' . uniqid() . '.pdf';

    // Save the PDF to Amazon S3
    Storage::disk('s3')->put($fileName, $pdf->output());

    // Optionally, you can return a response or redirect as needed
}

}
