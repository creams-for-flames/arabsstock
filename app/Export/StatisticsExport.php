<?php

namespace App\Export;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class StatisticsExport implements WithHeadings, FromArray, WithStyles, WithColumnWidths
{
    private $from;
    private $to;

    public function __construct($form, $to)
    {
        $this->from = $form;
        $this->to = $to;
    }

    public function array(): array
    {
        $from = $this->from;
        $to = $this->to;
        $data = [
            'images' => [
                'total' => DB::table('images')->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'arabsstock' => DB::table('images')->whereNotIn('contributor_image_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_images');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'contributors' => DB::table('images')->whereIn('contributor_image_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_images');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
            ],
            'videos' => [
                'total' => DB::table('videos')->whereNull('parent_id')->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'arabsstock' => DB::table('videos')->whereNull('parent_id')->whereNotIn('contributor_video_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_videos');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'contributors' => DB::table('videos')->whereNull('parent_id')->whereIn('contributor_video_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_videos');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
            ],
            'vectors' => [
                'total' => DB::table('vectors')->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'arabsstock' => DB::table('vectors')->whereNotIn('contributor_vector_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_vectors');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'contributors' => DB::table('vectors')->whereIn('contributor_vector_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_vectors');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
            ],
            'active_images' => [
                'total' => DB::table('images')->where('status', '=', 'active')->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'arabsstock' => DB::table('images')->where('status', '=', 'active')->whereNotIn('contributor_image_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_images');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'contributors' => DB::table('images')->where('status', '=', 'active')->whereIn('contributor_image_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_images');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
            ],
            'active_videos' => [
                'total' => DB::table('videos')->whereNull('parent_id')->where('status', '=', 'active')->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'arabsstock' => DB::table('videos')->whereNull('parent_id')->where('status', '=', 'active')->whereNotIn('contributor_video_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_videos');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'contributors' => DB::table('videos')->whereNull('parent_id')->where('status', '=', 'active')->whereIn('contributor_video_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_videos');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
            ],
            'active_vectors' => [
                'total' => DB::table('vectors')->where('status', '=', 'active')->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'arabsstock' => DB::table('vectors')->where('status', '=', 'active')->whereNotIn('contributor_vector_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_vectors');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'contributors' => DB::table('vectors')->where('status', '=', 'active')->whereIn('contributor_vector_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_vectors');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
            ],
            'inactive_images' => [
                'total' => DB::table('images')->where('status', '!=', 'active')->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'arabsstock' => DB::table('images')->where('status', '!=', 'active')->whereNotIn('contributor_image_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_images');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'contributors' => DB::table('images')->where('status', '!=', 'active')->whereIn('contributor_image_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_images');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
            ],
            'inactive_videos' => [
                'total' => DB::table('videos')->whereNull('parent_id')->where('status', '!=', 'active')->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'arabsstock' => DB::table('videos')->whereNull('parent_id')->where('status', '!=', 'active')->whereNotIn('contributor_video_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_videos');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'contributors' => DB::table('videos')->whereNull('parent_id')->where('status', '!=', 'active')->whereIn('contributor_video_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_videos');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
            ],
            'inactive_vectors' => [
                'total' => DB::table('vectors')->where('status', '!=', 'active')->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'arabsstock' => DB::table('vectors')->where('status', '!=', 'active')->whereNotIn('contributor_vector_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_vectors');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
                'contributors' => DB::table('vectors')->where('status', '!=', 'active')->whereIn('contributor_vector_id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('contributor_vectors');
                })->where('date', '>', $from)->where('date', '<', $to)->whereNull('deleted_at')->count(),
            ],
        ];
        return [
            ['الصور المرفوعة', $data['images']['total'], $data['images']['arabsstock'], $data['images']['contributors'], "من {$from} الى {$to}"],
            ['الفيديوهات المرفوعة', $data['videos']['total'], $data['videos']['arabsstock'], $data['videos']['contributors'],],
            ['الفكتور المرفوعة', $data['vectors']['total'], $data['vectors']['arabsstock'], $data['vectors']['contributors'],],
            ['الصور المرفوعة (فعال)', $data['active_images']['total'], $data['active_images']['arabsstock'], $data['active_images']['contributors'],],
            ['الفيديوهات المرفوعة (فعال)', $data['active_videos']['total'], $data['active_videos']['arabsstock'], $data['active_videos']['contributors'],],
            ['الفكتور المرفوعة (فعال)', $data['active_vectors']['total'], $data['active_vectors']['arabsstock'], $data['active_vectors']['contributors'],],
            ['الصور المرفوعة (معطل)', $data['inactive_images']['total'], $data['inactive_images']['arabsstock'], $data['inactive_images']['contributors'],],
            ['الفيديوهات المرفوعة(معطل)', $data['inactive_videos']['total'], $data['inactive_videos']['arabsstock'], $data['inactive_videos']['contributors'],],
            ['الفكتور المرفوعة(معطل)', $data['inactive_vectors']['total'], $data['inactive_vectors']['arabsstock'], $data['inactive_vectors']['contributors'],],
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            '#',
            'اجمالي',
            'عربستوك',
            'مساهمين',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A8:A10' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFC7CE'],
                ],
            ],
            'A5:A7' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'C6EFCE'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'E' => 40,
        ];
    }
}
