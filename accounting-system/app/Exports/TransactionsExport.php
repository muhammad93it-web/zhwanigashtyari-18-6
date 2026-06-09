<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        protected string $fromDate,
        protected string $toDate,
        protected ?int $clientId = null
    ) {}

    public function collection()
    {
        return Transaction::with('client', 'user')
            ->whereDate('transaction_date', '>=', $this->fromDate)
            ->whereDate('transaction_date', '<=', $this->toDate)
            ->when($this->clientId, fn($q) => $q->where('client_id', $this->clientId))
            ->latest('transaction_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ژمارەی مامەڵە',
            'بەروار',
            'کڕیار',
            'جۆری مامەڵە',
            'دراو',
            'بڕی ئەسڵی',
            'بڕی دۆلار',
            'بڕی دینار',
            'ڕێژەی گۆڕین (تۆماركراو)',
            'وەسف',
            'تێبینی',
            'تۆمارکەر',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->reference_number,
            $transaction->transaction_date->format('Y-m-d'),
            $transaction->client->name ?? '',
            Transaction::TYPES[$transaction->type] ?? $transaction->type,
            $transaction->currency,
            number_format($transaction->amount, 2),
            number_format($transaction->amount_usd, 2),
            number_format($transaction->amount_iqd, 2),
            number_format($transaction->exchange_rate_usd_to_iqd, 4),
            $transaction->description,
            $transaction->notes ?? '',
            $transaction->user->name ?? '',
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
