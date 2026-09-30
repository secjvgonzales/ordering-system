<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'old_name' => 'Classic Burger',
                'name' => 'Core Black Tee',
                'description' => 'A clean everyday black T-shirt with a relaxed streetwear fit.',
                'category' => 'Basics',
                'aliases' => ['Relaxed Black Tee'],
                'price' => 799.00,
                'stock_quantity' => 40,
                'status' => 'active',
                'image_path' => 'images/items/core-black-tee.jpg',
            ],
            [
                'old_name' => 'Chicken Pasta',
                'name' => 'Essential White Tee',
                'description' => 'Minimal white crew-neck tee designed for everyday wear.',
                'category' => 'Basics',
                'aliases' => ['Every day White Tee'],
                'price' => 699.00,
                'stock_quantity' => 35,
                'status' => 'active',
                'image_path' => 'images/items/essential-white-tee.jpg',
            ],
            [
                'old_name' => 'Iced Coffee',
                'name' => 'Slate Oversized Tee',
                'description' => 'Relaxed oversized shirt with a modern neutral streetwear silhouette.',
                'category' => 'Oversized Tees',
                'aliases' => ['Gray Oversized Tee'],
                'price' => 899.00,
                'stock_quantity' => 25,
                'status' => 'active',
                'image_path' => 'images/items/slate-oversized-tee.jpg',
            ],
            [
                'old_name' => 'French Fries',
                'name' => 'Signal Graphic Tee',
                'description' => 'Statement graphic T-shirt made for casual streetwear styling.',
                'category' => 'Graphic Tees',
                'aliases' => ['Coffee Tee'],
                'price' => 949.00,
                'stock_quantity' => 20,
                'status' => 'active',
                'image_path' => 'images/items/signal-graphic-tee.jpg',
            ],
            [
                'old_name' => 'Chocolate Cake',
                'name' => 'Midnight Street Tee',
                'description' => 'Dark premium casual tee with a clean urban look.',
                'category' => 'T-Shirts',
                'aliases' => ['Bazik Tee'],
                'price' => 849.00,
                'stock_quantity' => 30,
                'status' => 'active',
                'image_path' => 'images/items/midnight-street-tee.jpg',
            ],
        ];

        foreach ($items as $item) {
            $existingItem = Item::where('name', $item['old_name'])->first()
                ?? Item::where('name', $item['name'])->first();

            if (! $existingItem) {
                $renamedItem = Item::whereIn('name', $item['aliases'])->first();

                if ($renamedItem) {
                    $renamedItem->update(['category' => $item['category']]);

                    continue;
                }
            }

            unset($item['old_name']);
            unset($item['aliases']);

            if ($existingItem) {
                $existingItem->update($item);
            } else {
                Item::create($item);
            }
        }
    }
}
