<?php

namespace App\Exports;

use App\Models\Client;
use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientTransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(protected Client $client) {}

    public function title(): string
    {
        return mb_substr($this->client->name, 0, 30);
    }

    public function collection()
    {
        return $this->client->transactions()->with('user')->latest('transaction_date')->get();
    }

    public function headings(): array
    {
        return [
            'ژمارەی مامەڵە',
            'بەروار',
            'جۆری مامەڵە',
            'دراو',
            'بڕی ئەسڵی',
            'بڕی دۆلار',
            'بڕی دینار',
            'ڕێژەی گۆڕین (تۆماركراو)',
            'وەسف',
            'تێبینی',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->reference_number,
            $transaction->transaction_date->format('Y-m-d'),
            Transaction::TYPES[$transaction->type] ?? $transaction->type,
            $transaction->currency,
            number_format($transaction->amount, 2),
            number_format($transaction->amount_usd, 2),
            number_format($transaction->amount_iqd, 2),
            number_format($transaction->exchange_rate_usd_to_iqd, 4),
            $transaction->description,
            $transaction->notes ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF0F4C75']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
