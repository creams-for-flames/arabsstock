<?php
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegalReleaseFormTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('legal_release_forms')->insert(
            [
                [
                    'file' => "/uploads/releases/نموذج موافقة العارضين البالغين - عربستوك.pdf",
                    'local' => 'ar',
                    'type' => 'adult',
                    'description' =>"نموذج موافقة العارضين البالغين",
                    ],
                    [
                    'file' => "/uploads/releases/Approval form adult models.pdf",
                    'local' => 'en',
                    'type' => 'adult',
                    'description' =>"Approval form adult models",
                    ],
                    [
                    'file' => "/uploads/releases/نموذج موافقة العارضين الأطفال - عربستوك.pdf",
                    'local' => 'ar',
                    'type' => 'minor',
                    'description' =>"نموذج موافقة العارضين الأطفال",
                    ],
                    [
                    'file' => "/uploads/releases/Approval form Minor models.pdf",
                    'local' => 'en',
                    'type' => 'minor',
                    'description' =>"Approval form Minor models",
                    ],
        
                    [
                    'file' => "/uploads/releases/نموذج موافقة المالك - عربستوك.pdf",
                    'local' => 'ar',
                    'type' => 'property',
                    'description' =>"نموذج موافقة المالك",
                    ],
                    [
                    'file' => "/uploads/releases/Approval form Property.pdf",
                    'local' => 'en',
                    'type' => 'property',
                    'description' =>"Approval form Property",
                    ]
            ]

    );
    }
}
