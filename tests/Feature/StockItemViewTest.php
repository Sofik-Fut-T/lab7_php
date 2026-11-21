<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\StockItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockItemViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_displays_entities(): void
    {
        $category = Category::create(['title' => 'Електроніка']);
        StockItem::create([
            'title' => 'Унікальний Тестовий Товар',
            'sku' => 'TEST-999',
            'quantity' => 5,
            'category_id' => $category->id,
        ]);

        $response = $this->get('/stock-items');

        $response->assertStatus(200)
            ->assertSee('Унікальний Тестовий Товар');
    }

    public function test_create_form_validates_input(): void
    {
        $response = $this->post('/stock-items', [
            'title' => '',
            'sku' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'sku', 'category_id']);
    }
}
