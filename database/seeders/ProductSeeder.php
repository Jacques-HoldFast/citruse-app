<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductPrice;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Citruse product catalog with pricing for 2023, 2024, 2025
        $products = [
            [
                'product_code' => 'FR-001a',
                'description' => 'Citrusdal Oranges - Grade A',
                'prices' => [
                    2023 => 125.50,
                    2024 => 135.50,
                    2025 => 150.90,
                ]
            ],
            [
                'product_code' => 'FR-001b',
                'description' => 'Citrusdal Oranges - Grade B',
                'prices' => [
                    2023 => 136.75,
                    2024 => 148.75,
                    2025 => 195.00,
                ]
            ],
            [
                'product_code' => 'FR-002',
                'description' => 'Citrusdal Limes',
                'prices' => [
                    2023 => 131.50,
                    2024 => 151.95,
                    2025 => 165.00,
                ]
            ],
            [
                'product_code' => 'FR-003',
                'description' => 'Citrusdal Grapefruit - Grade 1',
                'prices' => [
                    2023 => 109.50,
                    2024 => 132.50,
                    2025 => 141.00,
                ]
            ],
            [
                'product_code' => 'FR-004',
                'description' => 'Citrusdal Lemons - Grade 1',
                'prices' => [
                    2023 => 99.25,
                    2024 => 120.50,
                    2025 => 159.00,
                ]
            ],
            [
                'product_code' => 'GT01',
                'description' => 'Grahamstown Oranges - Type A',
                'prices' => [
                    2023 => 98.50,
                    2024 => 101.45,
                    2025 => 115.60,
                ]
            ],
            [
                'product_code' => 'GT02',
                'description' => 'Grahamstown Oranges - Type B',
                'prices' => [
                    2023 => 97.60,
                    2024 => 103.65,
                    2025 => 123.50,
                ]
            ],
        ];

        foreach ($products as $productData) {
            // Create or update the product
            $product = Product::updateOrCreate(
                ['product_code' => $productData['product_code']],
                ['description' => $productData['description']]
            );

            // Create or update pricing for each year
            foreach ($productData['prices'] as $year => $price) {
                ProductPrice::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'year' => $year
                    ],
                    ['price_per_kg' => $price]
                );
            }

            $this->command->info("Created/Updated product: {$product->product_code} - {$product->description}");
        }

        $this->command->info('Product catalog seeded successfully!');
    }
}
