<?php

namespace App\Export;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\{ImageDownload};
use Carbon\Carbon;

class DownloadStatisticsExport implements WithHeadings, FromArray, WithColumnWidths
{
    private $data;
    private $from;
    private $to;
    private $type;
    private $case;

    public function __construct($data,$form = NULL, $to = NULL,$type, $case = 'all')
    {
        $this->data = $data;
        $this->from = $form;
        $this->to = $to;
        $this->type = $type;
        $this->case = $case;
    }

    public function array(): array
    {
        $from = $this->from;
        $to = $this->to;
        $date_range = $from && $to?"من {$from} الى {$to}":now();
        $data = $this->data;
        $type = $this->type;

                $title = __("global.downloads_count");
        switch ($this->case) {
            case 'arabsstock':
            $result = [
                    $title,
                    $data['total'],
                    $data['total_deleted']?$data['total_deleted']:'0',
                    $data['permanently_delete']?$data['permanently_delete']:'0',
                    $data['arabsstock'],
                    $date_range
                ];
                break;
            case 'contributor':
                $result = [
                        $title,
                        $data['total'],
                        $data['total_deleted']?$data['total_deleted']:'0',
                        $data['permanently_delete']?$data['permanently_delete']:'0',
                        $data['contributors'], 
                        $date_range
                    ];
                    break;            
            default:
                # code...
               $result= [
                        $title,
                        $data['total'],
                        $data['total_deleted']?$data['total_deleted']:'0',
                        $data['permanently_delete']?$data['permanently_delete']:'0',
                        $data['arabsstock'],
                        $data['contributors'], 
                        $date_range
                   ];
                break;
        }
        return [ $result];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        switch ($this->case) {
            case 'arabsstock':
                    $heading = [
                        "#".__("global.".$this->type),'اجمالي','اجمالي محذوف','حذف نهائي','عربستوك',];
                break;
            case 'contributor':
                    $heading = ["#".__("global.".$this->type),'اجمالي','اجمالي محذوف','حذف نهائي','مساهمين'];
                break;
            default:
                # code...
                $heading = 
                ["#".__("global.".$this->type),'اجمالي','اجمالي محذوف','حذف نهائي','عربستوك','مساهمين',];
                break;
        }
        
        return $heading;
    }


    public function columnWidths(): array
    {
        return [
            'G' => 40,
        ];
    }
}
