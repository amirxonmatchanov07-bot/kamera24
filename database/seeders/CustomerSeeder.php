<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => "Aziz Karimov", 'phone' => "+998 90 123 45 67", 'debt' => 66.93, 'days_ago' => 15],
            ['name' => "Dilnoza Yusupova", 'phone' => "+998 91 234 56 78", 'debt' => 33.07, 'days_ago' => 9],
            ['name' => "Bahodir Tursunov", 'phone' => "+998 93 345 67 89", 'debt' => 98.43, 'days_ago' => 22],
            ['name' => "Shaxzod Rahimov", 'phone' => "+998 94 456 78 90", 'debt' => 57.48, 'days_ago' => 7],
        ];

        foreach ($customers as $customer) {
            Customer::create([
                'name' => $customer['name'],
                'phone' => $customer['phone'],
                'debt' => $customer['debt'],
                'last_payment_at' => Carbon::now()->subDays($customer['days_ago']),
            ]);
        }
    }
}
