<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ResetService
{
    /**
     * بەشە دارایییەکان کە دەکرێت زیرۆ بکرێنەوە (مامەڵە/وەسڵ/پارەدان دەسڕێنەوە،
     * بەڵام ناوە بنەڕەتییەکان [دابینکەر، کاڵا، شۆفێر، کرێکار، ...] دەمێننەوە).
     */
    public const SECTIONS = [
        'finance'      => 'دارایی (داهات، خەرجی، قەرز)',
        'trading'      => 'کۆگا و جووڵەی کاڵا',
        'suppliers'    => 'دابینکەران (وەسڵی کڕین و پارەدان)',
        'drivers'      => 'گواستنەوە و شۆفێر',
        'contractors'  => 'وەستا و بەڵێندەرایەتی',
        'labor'        => 'کرێی کار و کرێکاران',
        'transactions' => 'مامەڵە گشتییەکان',
    ];

    public static function isValidSection(string $section): bool
    {
        return array_key_exists($section, self::SECTIONS);
    }

    /** زیرۆکردنەوەی یەک بەش بە تەواوی، لەناو ترانزاکشنێکدا. */
    public function resetSection(string $section): void
    {
        DB::transaction(function () use ($section) {
            $this->deleteSection($section);
            $this->recomputeBalances();
        });
    }

    /** زیرۆکردنەوەی گشتی — هەموو بەشە دارایییەکان، بەڵام ناوەکان دەمێننەوە. */
    public function resetMaster(): void
    {
        DB::transaction(function () {
            foreach (array_keys(self::SECTIONS) as $section) {
                $this->deleteSection($section);
            }
            $this->recomputeBalances();
        });
    }

    /**
     * سڕینەوەی تۆمارەکانی بەشێک. بە ڕیزی منداڵ→باوک کار دەکات.
     * خشتە منداڵەکان (details) بە ئاشکرا دەسڕێنەوە بۆ پشتگیری SQLite و MySQL.
     */
    private function deleteSection(string $section): void
    {
        switch ($section) {
            case 'finance':
                DB::table('incomes')->delete();
                DB::table('expenses')->delete();
                DB::table('debts')->delete();
                break;

            case 'trading':
                DB::table('material_movements')->delete();
                break;

            case 'suppliers':
                DB::table('purchase_invoice_details')->delete();
                DB::table('purchase_invoices')->delete();
                DB::table('supplier_transactions')->delete();
                break;

            case 'drivers':
                DB::table('driver_transactions')->delete();
                DB::table('driver_trip_details')->delete();
                DB::table('driver_trip_logs')->delete();
                break;

            case 'contractors':
                DB::table('contractor_payments')->delete();
                break;

            case 'labor':
                DB::table('labor_payments')->delete();
                break;

            case 'transactions':
                DB::table('transactions')->delete();
                break;
        }
    }

    /**
     * نوێکردنەوەی باڵانس و کۆگا بەپێی ئەو تۆمارانەی ماونەتەوە.
     * گرنگە: کۆگا لە هەردوو material_movements و purchase_invoice_details پێکدێت،
     * بۆیە دەبێت دووبارە حیساب بکرێت نەک تەنها زیرۆ بکرێتەوە.
     */
    private function recomputeBalances(): void
    {
        $this->recomputeMaterialStock();
        $this->recomputeSupplierBalances();
        $this->recomputeDriverBalances();
    }

    private function recomputeMaterialStock(): void
    {
        foreach (DB::table('materials')->pluck('id') as $id) {
            $fromDetails = (float) DB::table('purchase_invoice_details')
                ->where('material_id', $id)->sum('quantity');

            $purchased = (float) DB::table('material_movements')
                ->where('material_id', $id)->where('type', 'purchase')->sum('quantity');
            $sold = (float) DB::table('material_movements')
                ->where('material_id', $id)->where('type', 'sale')->sum('quantity');

            DB::table('materials')->where('id', $id)->update([
                'current_stock' => $fromDetails + $purchased - $sold,
            ]);
        }
    }

    private function recomputeSupplierBalances(): void
    {
        foreach (DB::table('suppliers')->pluck('id') as $id) {
            $iqd = (float) (DB::table('supplier_transactions')
                ->where('supplier_id', $id)->where('currency', 'IQD')
                ->orderByDesc('id')->value('balance_after') ?? 0);
            $usd = (float) (DB::table('supplier_transactions')
                ->where('supplier_id', $id)->where('currency', 'USD')
                ->orderByDesc('id')->value('balance_after') ?? 0);

            DB::table('suppliers')->where('id', $id)->update([
                'balance_iqd' => $iqd,
                'balance_usd' => $usd,
                'balance'     => $iqd,
            ]);
        }
    }

    private function recomputeDriverBalances(): void
    {
        foreach (DB::table('drivers')->pluck('id') as $id) {
            $iqd = (float) (DB::table('driver_transactions')
                ->where('driver_id', $id)->where('currency', 'IQD')
                ->orderByDesc('id')->value('balance_after') ?? 0);
            $usd = (float) (DB::table('driver_transactions')
                ->where('driver_id', $id)->where('currency', 'USD')
                ->orderByDesc('id')->value('balance_after') ?? 0);

            DB::table('drivers')->where('id', $id)->update([
                'balance_iqd' => $iqd,
                'balance_usd' => $usd,
                'balance'     => $iqd,
            ]);
        }
    }
}
