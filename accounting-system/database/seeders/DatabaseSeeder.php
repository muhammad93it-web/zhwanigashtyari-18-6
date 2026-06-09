<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Contractor;
use App\Models\ContractorPayment;
use App\Models\Debt;
use App\Models\Document;
use App\Models\ExchangeRate;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Material;
use App\Models\MaterialMovement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@jwani.com'],
            [
                'name'     => 'بەڕێوەبەر',
                'password' => Hash::make('password'),
            ]
        );

        // Initial exchange rate
        ExchangeRate::firstOrCreate(
            ['usd_to_iqd' => 1310.00],
            [
                'notes'          => 'ڕێژەی سەرەتایی',
                'set_by'         => 'بەڕێوەبەر',
                'effective_from' => now(),
            ]
        );
        $rate = ExchangeRate::currentRate();

        // Sample clients
        $clients = [
            ['name' => 'ئەحمەد حسێن', 'phone' => '07501234567', 'address' => 'هەولێر'],
            ['name' => 'سارا محمد', 'phone' => '07709876543', 'address' => 'سلێمانی'],
            ['name' => 'کارزان عبدوللا', 'phone' => '07701112233', 'address' => 'دهۆک'],
            ['name' => 'شرکەتی نمونە', 'phone' => '07701234567', 'address' => 'هەولێر، گەڕەکی کەرکووک'],
        ];
        foreach ($clients as $client) {
            Client::firstOrCreate(['name' => $client['name']], array_merge($client, ['is_active' => true]));
        }

        // ===== Finance samples =====
        if (Income::count() === 0) {
            Income::create([
                'user_id' => $admin->id, 'source' => 'کرێی ژوور', 'category' => 'کرێ',
                'currency' => 'IQD', 'amount' => 750000, 'amount_iqd' => 750000,
                'amount_usd' => round(750000 / $rate, 4), 'exchange_rate_usd_to_iqd' => $rate,
                'description' => 'کرێی مانگانە', 'income_date' => now()->subDays(3),
            ]);
            Income::create([
                'user_id' => $admin->id, 'source' => 'فرۆشتنی خزمەتگوزاری', 'category' => 'خزمەتگوزاری',
                'currency' => 'USD', 'amount' => 500, 'amount_usd' => 500,
                'amount_iqd' => round(500 * $rate, 2), 'exchange_rate_usd_to_iqd' => $rate,
                'description' => 'پاکێجی گەشت', 'income_date' => now()->subDay(),
            ]);
        }

        if (Expense::count() === 0) {
            Expense::create([
                'user_id' => $admin->id, 'payee' => 'کارەبا', 'category' => 'خزمەتگوزاری',
                'currency' => 'IQD', 'amount' => 120000, 'amount_iqd' => 120000,
                'amount_usd' => round(120000 / $rate, 4), 'exchange_rate_usd_to_iqd' => $rate,
                'description' => 'پارەی کارەبا', 'expense_date' => now()->subDays(2),
            ]);
        }

        if (Debt::count() === 0) {
            Debt::create([
                'user_id' => $admin->id, 'party_name' => 'ئەحمەد حسێن', 'direction' => 'receivable',
                'currency' => 'IQD', 'amount' => 300000, 'amount_iqd' => 300000,
                'amount_usd' => round(300000 / $rate, 4), 'exchange_rate_usd_to_iqd' => $rate,
                'status' => 'open', 'description' => 'قەرزی ماوە', 'debt_date' => now()->subDays(10),
                'due_date' => now()->addDays(20),
            ]);
            Debt::create([
                'user_id' => $admin->id, 'party_name' => 'کۆمپانیای دابینکردن', 'direction' => 'payable',
                'currency' => 'USD', 'amount' => 200, 'amount_usd' => 200,
                'amount_iqd' => round(200 * $rate, 2), 'exchange_rate_usd_to_iqd' => $rate,
                'status' => 'open', 'description' => 'پارەی مەواد', 'debt_date' => now()->subDays(5),
            ]);
        }

        // ===== Trading & inventory samples =====
        if (Material::count() === 0) {
            $cement = Material::create(['name' => 'چیمەنتۆ', 'unit' => 'کیس', 'category' => 'بیناسازی', 'current_stock' => 0, 'min_stock' => 20]);
            $tile = Material::create(['name' => 'کاشی', 'unit' => 'مەتر', 'category' => 'بیناسازی', 'current_stock' => 0, 'min_stock' => 50]);
            Material::create(['name' => 'بۆیە', 'unit' => 'گەلەن', 'category' => 'تەواوکاری', 'current_stock' => 0, 'min_stock' => 10]);

            // Purchase movements (increase stock)
            $this->movement($cement, 'purchase', 100, 9000, 'IQD', $rate, $admin->id, 'کۆگای ناوەندی');
            $this->movement($tile, 'sale', 30, 15000, 'IQD', $rate, $admin->id, 'سارا محمد');
        }

        // ===== Contractors samples =====
        if (Contractor::count() === 0) {
            $c1 = Contractor::create([
                'name' => 'وەستا عومەر', 'phone' => '07501112233', 'work_type' => 'per_meter',
                'rate_per_meter' => 12000, 'currency' => 'IQD',
            ]);
            $c2 = Contractor::create([
                'name' => 'وەستا دلێر', 'phone' => '07709998877', 'work_type' => 'contract',
                'contract_amount' => 5000000, 'currency' => 'IQD',
            ]);

            ContractorPayment::create([
                'contractor_id' => $c1->id, 'user_id' => $admin->id, 'currency' => 'IQD',
                'amount' => 240000, 'amount_iqd' => 240000, 'amount_usd' => round(240000 / $rate, 4),
                'exchange_rate_usd_to_iqd' => $rate, 'meters' => 20, 'description' => 'پارەی ٢٠ مەتر',
                'payment_date' => now()->subDays(4),
            ]);
            ContractorPayment::create([
                'contractor_id' => $c2->id, 'user_id' => $admin->id, 'currency' => 'IQD',
                'amount' => 1000000, 'amount_iqd' => 1000000, 'amount_usd' => round(1000000 / $rate, 4),
                'exchange_rate_usd_to_iqd' => $rate, 'description' => 'پێشەکی قۆنتەرات',
                'payment_date' => now()->subDays(2),
            ]);
        }

        // ===== Administration samples =====
        if (Document::count() === 0) {
            Document::create([
                'user_id' => $admin->id, 'title' => 'نووسراوی ڕاسپاردن', 'doc_type' => 'ڕاسپاردن',
                'recipient' => 'بەڕێز ئەحمەد حسێن',
                'body' => "سڵاو و ڕێز،\n\nئەمە نووسراوێکی نمونەیە بۆ تاقیکردنەوەی سیستەمی چاپکردن.\n\nسوپاس.",
                'doc_date' => now(),
            ]);
        }
    }

    private function movement(Material $material, string $type, float $qty, float $unitPrice, string $currency, float $rate, int $userId, string $party): void
    {
        $amount = round($qty * $unitPrice, 2);
        $usd = $currency === 'USD' ? $amount : round($amount / $rate, 4);
        $iqd = $currency === 'USD' ? round($amount * $rate, 2) : $amount;

        MaterialMovement::create([
            'material_id' => $material->id, 'user_id' => $userId, 'type' => $type,
            'quantity' => $qty, 'unit_price' => $unitPrice, 'currency' => $currency,
            'amount' => $amount, 'amount_usd' => $usd, 'amount_iqd' => $iqd,
            'exchange_rate_usd_to_iqd' => $rate, 'party_name' => $party, 'movement_date' => now()->subDays(3),
        ]);

        $material->current_stock = $type === 'purchase'
            ? (float) $material->current_stock + $qty
            : (float) $material->current_stock - $qty;
        $material->save();
    }
}
