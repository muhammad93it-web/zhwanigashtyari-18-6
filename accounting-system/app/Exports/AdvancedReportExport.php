<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdvancedReportExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        protected Collection $rows,
        protected array      $headings,
        protected array      $columns,
        protected string     $title = 'ڕاپۆرت'
    ) {}

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function map($row): array
    {
        $out = [];
        foreach ($this->columns as $col) {
            if (is_callable($col)) {
                $out[] = $col($row);
            } else {
                $out[] = data_get($row, $col, '');
            }
        }
        return $out;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 12],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF0F4C75']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return mb_substr($this->title, 0, 31);
    }
}
