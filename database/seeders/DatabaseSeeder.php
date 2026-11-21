<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\StockItem;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $cat1 = Category::create(['title' => 'Електроніка']);
        $cat2 = Category::create(['title' => 'Меблі']);
        $cat3 = Category::create(['title' => 'Офісна техніка']);

        $sup1 = Supplier::create(['name' => 'ТОВ "ТехноПостач"']);
        $sup2 = Supplier::create(['name' => 'ПП "МебліСвіт"']);
        $sup3 = Supplier::create(['name' => 'ФОП Іваненко']);

        $item = StockItem::create([
            'title' => 'Ноутбук Lenovo IdeaPad',
            'sku' => 'NB-1001',
            'quantity' => 15,
            'category_id' => $cat1->id,
        ]);
        $item->suppliers()->attach([$sup1->id, $sup3->id]);
    }
}
