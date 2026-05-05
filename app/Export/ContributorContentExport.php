<?php

namespace App\Export;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContributorContentExport implements WithMapping, WithHeadings, FromCollection, ShouldAutoSize, WithColumnWidths, WithStyles
{
    protected $Objects;

    /**
     * @param $Objects
     */
    public function __construct($Objects)
    {
        $this->Objects = $Objects;
    }


    /**
     * @param mixed $object
     * @return array
     */
    public function map($object): array
    {
        return [
            $object->id,
            $object->title_ar,
            $object->user ? $object->user->name : 'deleted',
            $object->date,
            $object->deleted_at ?: '-',
            $object->deleted_at ? 'deleted' : $object->status,
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
        return [
            '#',
            __('admin.title'),
            __('misc.uploaded_by'),
            __('admin.date'),
            __('تاريخ الحذف'),
            __('views.Status'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]],
            'C' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]],
            'D' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]],
            'E' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]],
            'F' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'B' => 50,
        ];
    }
}
