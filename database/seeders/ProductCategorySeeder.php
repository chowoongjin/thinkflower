<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            '근조화환',
            '축하화환',
            '꽃바구니',
            '관엽식물',
            '동양란',
            '서양란',
            '근조바구니',
            '근조오브제',
            '근조스텐드',
            '근조쌀화환',
            '축하쌀화환',
        ];

        foreach ($items as $name) {
            DB::table('product_categories')->updateOrInsert(
                ['name' => $name],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
