<?php

namespace App\Jobs;

use App\Models\Contributor;
use App\Models\ContributorImage;
use App\Models\LegalRelease;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpdateContributorImageSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $update;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($update)
    {
        $this->onQueue('heavy');
        $this->update = $update;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $update = $this->update;
        $update->updated([
            'status' => 'processing',
        ]);
        if (!Storage::disk('public')->exists($update->file)) {
            $update->updated([
                'status' => 'error',
            ]);
            return;
        }
        Storage::disk('s3')->put($update->file, Storage::disk('public')->get($update->file));
        $dir = public_path(pathinfo($update->file, PATHINFO_DIRNAME));
        $zip = new \ZipArchive();
        $res = $zip->open(public_path($update->file));
        if ($res === TRUE) {
            $zip->extractTo($dir);
            $zip->close();
        } else {
            $update->update(['status' => 'error']);
            Storage::disk('public')->deleteDirectory($dir);
            return;
        }
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load("$dir/data.xlsx");
        $data = $spreadsheet->getSheet(0)->toArray();
        $keys = array_shift($data);
        $fname_index = array_search('fname', $keys);
        $title_en_index = array_search('title_en', $keys);
        $title_ar_index = array_search('title_ar', $keys);
        $tags_en_index = array_search('tags_en', $keys);
        $tags_ar_index = array_search('tags_ar', $keys);
        $releases_index = array_search('releases', $keys);
        $license_path_index = array_search('license_path', $keys);
        $license_files_index = array_search('license_files', $keys);
        $updated_files = [];
        $data = collect($data)->keyBy($fname_index);
        foreach ($data as $r) {
            /**@var $image ContributorImage */
            $image = ContributorImage::where('contributor_id', $update->contributor_id)
                ->where('original_name', 'like', "%{$r[$fname_index]}.%")->first();
            if ($image) {
                if (isset($r[$releases_index]) && isset($r[$license_path_index]) && isset($r[$license_files_index])) {
                    $files = explode(',', $r[$license_files_index]);
                    if ($files) {
                        DB::table('contributor_image_legal_release')->where('image_id', $image->id)->delete();
                        foreach ($files as $file) {
                            $file = trim($file);
                            $license_path = trim($r[$license_path_index]);
                            $license_file = "$dir/{$license_path}/{$file}";
                            if (file_exists($license_file)) {
                                $license_path = "uploads/contributor_releases/$update->contributor_id/" . Str::random() . '.' . pathinfo($license_file, PATHINFO_EXTENSION);
                                Storage::disk('s3')->put($license_path, file_get_contents($license_file));
                                $release = LegalRelease::create([
                                    'contributor_id' => $update->contributor_id,
                                    'name' => pathinfo($license_file, PATHINFO_BASENAME),
                                    'type' => trim($r[$releases_index]),
                                    'file' => $license_path,
                                    'ethnicity' => "",
                                    'age' => "",
                                    'gender' => "",
                                ]);
                                DB::table('contributor_image_legal_release')->insert([
                                    'release_id' => $release->id,
                                    'image_id' => $image->id,
                                    'created_at' => now(),
                                ]);
                            }
                        }
                    }

                }
                $updated_files[] = $image->id;
                DB::table('contributor_images')->where('id', $image->id)->update([
                    'title_en' => $r[$title_en_index],
                    'title_ar' => $r[$title_ar_index],
                ]);
                $r[$tags_ar_index] = str_replace('،', ',', $r[$tags_ar_index]);
                $r[$tags_en_index] = str_replace('،', ',', $r[$tags_en_index]);
                $tags_ar = explode(',', $r[$tags_ar_index]);
                $tags_en = explode(',', $r[$tags_en_index]);
                DB::table('image_tags_contributor')->where('image_id', $image->id)->delete();
                foreach ($tags_ar as $tag) {
                    DB::table('image_tags_contributor')->insert([
                        'image_id' => $image->id,
                        'tag' => $tag,
                        'local' => 'ar',
                    ]);
                }
                foreach ($tags_en as $tag) {
                    DB::table('image_tags_contributor')->insert([
                        'image_id' => $image->id,
                        'tag' => $tag,
                        'local' => 'en',
                    ]);
                }
                Log::channel('info')->info("contributor_image:{$image->id} updated");
            }
        }

        $update->updated([
            'status' => 'done',
            'updated_files' => json_encode($updated_files, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]);
        Storage::disk('public')->deleteDirectory($dir);
    }
}
