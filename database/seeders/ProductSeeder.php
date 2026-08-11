<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => "Type-C kabel 1m (Baseus)", 'category' => "Kabellar", 'price' => 3.54, 'cost' => 2.20, 'icon' => 'cable'],
            ['name' => "Quvvat banki 10000mAh (Xiaomi)", 'category' => "Quvvat banki", 'price' => 14.57, 'cost' => 10.24, 'icon' => 'battery-charging'],
            ['name' => "Simsiz zaryadlovchi (Anker)", 'category' => "Zaryadlovchilar", 'price' => 17.32, 'cost' => 12.20, 'icon' => 'zap'],
            ['name' => "Avto zaryadlovchi 2xUSB", 'category' => "Zaryadlovchilar", 'price' => 5.12, 'cost' => 2.99, 'icon' => 'plug'],
            ['name' => "Simsiz quloqchin (JBL)", 'category' => "Aksessuarlar", 'price' => 48.82, 'cost' => 33.86, 'icon' => 'headphones'],
            ['name' => "Telefon g'ilofi (silikon)", 'category' => "Aksessuarlar", 'price' => 5.12, 'cost' => 2.52, 'icon' => 'smartphone'],
            ['name' => "Quvvat banki 20000mAh", 'category' => "Quvvat banki", 'price' => 26.77, 'cost' => 18.90, 'icon' => 'battery-charging'],
            ['name' => "Type-C tezkor zaryadlovchi 33W", 'category' => "Zaryadlovchilar", 'price' => 8.66, 'cost' => 5.67, 'icon' => 'plug-zap'],
            ['name' => "Lightning kabel 1m", 'category' => "Kabellar", 'price' => 4.33, 'cost' => 2.68, 'icon' => 'cable'],
            ['name' => "Mikro SD karta 64GB", 'category' => "Xotira", 'price' => 7.48, 'cost' => 5.35, 'icon' => 'save'],
            ['name' => "Ekran himoya oynasi", 'category' => "Aksessuarlar", 'price' => 2.76, 'cost' => 1.18, 'icon' => 'shield'],
            ['name' => "Kamera linza tozalash to'plami", 'category' => "Kamera aksessuarlari", 'price' => 3.78, 'cost' => 2.13, 'icon' => 'camera'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
