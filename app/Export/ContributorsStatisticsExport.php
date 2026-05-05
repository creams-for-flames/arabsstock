<?php

namespace App\Export;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContributorsStatisticsExport implements WithMapping, WithHeadings, FromCollection, ShouldAutoSize, WithColumnFormatting, WithStyles
{
    protected $Objects;
    protected $headings;

    /**
     * @param $Objects
     */
    public function __construct($Objects, $headings = null)
    {
        $this->headings = [
            'Contributor ID',
            __('auth.name'),
            __('auth.email'),
            __('Total sales'),
            __('global.profit_ratio'),
            __('Total contributor profit'),
            __('Total contributor profit from :type', ['type' => __('Images')]),
            __('Total contributor profit from :type', ['type' => __('Videos')]),
            __('Total contributor profit from :type', ['type' => __('Vectors')]),
            __('views.total_withdraws'),
            __('views.current_profit'),
            __('Sold images'),
            __('Sold videos'),
            __('Sold vectors'),
        ];
        $this->Objects = $Objects;
        $this->headings = $headings;
    }


    /**
     * @param mixed $object
     * @return array
     */
    public function map($object): array
    {
        return [
            $object->id,
            $object->name,
            $object->email,
            $object->total_purchases,
            "{$object->profit_ratio}%",
            $object->total_profit,
            $object->images_purchases,
            $object->videos_purchases,
            $object->vectors_purchases,
            $object->total_withdrawals ?: 0,
            $object->current_profit,
            $object->sold_images,
            $object->sold_videos,
            $object->sold_vectors,
        ];
    }

    /**
     */
    public function collection()
    {
        return $this->Objects;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        if (request()->has('date_to') && request()->has('with_date')) {
            $date = request('date_to');
            return [
                'Contributor ID',
                __('auth.name'),
                __('auth.email'),
                __('Sales') . ' ' . __('to') . " ($date)",
                __('global.profit_ratio'),
                __('Contributor profit') . ' ' . __('to') . " ($date)",
                __('Total contributor profit from :type', ['type' => __('Images')]),
                __('Total contributor profit from :type', ['type' => __('Videos')]),
                __('Total contributor profit from :type', ['type' => __('Vectors')]),
                __('Withdraws') . ' ' . __('to') . " ($date)",
                __('Total Profit') . ' ' . __('to') . " ($date)",
                __('Sold images') . ' ' . __('to') . " ($date)",
                __('Sold videos') . ' ' . __('to') . " ($date)",
                __('Sold vectors') . ' ' . __('to') . " ($date)",
            ];
        }
        return [
            'Contributor ID',
            __('auth.name'),
            __('auth.email'),
            __('Total sales'),
            __('global.profit_ratio'),
            __('Total contributor profit'),
            __('Total contributor profit from :type', ['type' => __('Images')]),
            __('Total contributor profit from :type', ['type' => __('Videos')]),
            __('Total contributor profit from :type', ['type' => __('Vectors')]),
            __('views.total_withdraws'),
            __('views.current_profit'),
            __('Sold images'),
            __('Sold videos'),
            __('Sold vectors'),
        ];
    }

    public
    function columnFormats(): array
    {
        return [
//            'D' => \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC,
//            'E' => \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC,
//            'F' => \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC,
//            'G' => \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC,
//            'H' => \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC,
        ];
    }


    public
    function styles(Worksheet $sheet)
    {
        return [
            'A' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]],
            'B' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,]],
            'C' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,]],
            'D' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]],
            'E' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]],
            'F' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]],
            'G' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]],
            'H' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]],
            'I' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]],
            'J' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]],
            'K' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]],
        ];
    }
}
