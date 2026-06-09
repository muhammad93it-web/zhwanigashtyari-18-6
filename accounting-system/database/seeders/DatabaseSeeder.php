<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ExchangeRate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
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
    }
}
