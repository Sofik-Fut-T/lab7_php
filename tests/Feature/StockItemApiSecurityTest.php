<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\StockItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockItemApiSecurityTest extends TestCase
{
    use RefreshDatabase;

   
    public function test_unauthenticated_user_cannot_create_stock_item(): void
    {
        $response = $this->postJson('/api/v1/stock-items', [
            'title' => 'Неавторизований товар',
            'sku' => 'UNAUTH-01',
            'quantity' => 5,
            'category_id' => 1,
        ]);

        $response->assertStatus(401);
    }


    public function test_authenticated_user_can_create_stock_item_and_user_id_is_saved(): void
    {
        // 1. Створюємо користувача та генеруємо Sanctum-токен
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // 2. Створюємо категорію та постачальника для проходження валідації
        $category = Category::create(['title' => 'Електроніка']);
        $supplier = Supplier::create(['name' => 'ТОВ ТехноПостач']);

        // 3. Надсилаємо POST-запит із Bearer-токеном
        $response = $this->withToken($token)->postJson('/api/v1/stock-items', [
            'title' => 'Ноутбук Lenovo ThinkPad',
            'sku' => 'TP-2026-99',
            'quantity' => 7,
            'category_id' => $category->id,
            'suppliers' => [$supplier->id],
        ]);

        // 4. Перевіряємо статус 201 та наявність запису в БД з правильним user_id
        $response->assertStatus(201);

        $this->assertDatabaseHas('stock_items', [
            'title' => 'Ноутбук Lenovo ThinkPad',
            'sku' => 'TP-2026-99',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Сценарій 3: Спроба оновити або видалити чужий товар повертає 403 Forbidden.
     */
    public function test_user_cannot_update_or_delete_another_users_stock_item(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $intruderToken = $intruder->createToken('intruder-token')->plainTextToken;

        $category = Category::create(['title' => 'Периферія']);

        $stockItem = StockItem::create([
            'user_id' => $owner->id,
            'title' => 'Монітор Dell UltraSharp',
            'sku' => 'DELL-2026-01',
            'quantity' => 4,
            'category_id' => $category->id,
        ]);

        // Спроба змінити чужий товар
        $updateResponse = $this->withToken($intruderToken)->putJson("/api/v1/stock-items/{$stockItem->id}", [
            'title' => 'Зламаний заголовок',
            'sku' => 'DELL-2026-01',
            'quantity' => 1,
            'category_id' => $category->id,
        ]);
        $updateResponse->assertStatus(403);

        // Спроба видалити чужий товар
        $deleteResponse = $this->withToken($intruderToken)->deleteJson("/api/v1/stock-items/{$stockItem->id}");
        $deleteResponse->assertStatus(403);
    }

    /**
     * Сценарій 4: Власник може успішно оновити (200 OK) та видалити (204 No Content) свій товар.
     */
    public function test_owner_can_update_and_delete_own_stock_item(): void
    {
        $owner = User::factory()->create();
        $ownerToken = $owner->createToken('owner-token')->plainTextToken;

        $category = Category::create(['title' => 'Офісна техніка']);

        $stockItem = StockItem::create([
            'user_id' => $owner->id,
            'title' => 'Принтер HP LaserJet',
            'sku' => 'HP-2026-10',
            'quantity' => 3,
            'category_id' => $category->id,
        ]);

        // Оновлення власного товару
        $updateResponse = $this->withToken($ownerToken)->putJson("/api/v1/stock-items/{$stockItem->id}", [
            'title' => 'Принтер HP LaserJet Pro',
            'sku' => 'HP-2026-10',
            'quantity' => 10,
            'category_id' => $category->id,
        ]);
        $updateResponse->assertStatus(200);

        $this->assertDatabaseHas('stock_items', [
            'id' => $stockItem->id,
            'title' => 'Принтер HP LaserJet Pro',
            'quantity' => 10,
        ]);

        
        $deleteResponse = $this->withToken($ownerToken)->deleteJson("/api/v1/stock-items/{$stockItem->id}");
        $deleteResponse->assertStatus(200);

        $this->assertDatabaseMissing('stock_items', [
            'id' => $stockItem->id,
        ]);
    }
}
