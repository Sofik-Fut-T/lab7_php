<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockItemResource;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

class StockItemApiController extends Controller
{
    #[OA\Get(
        path: "/stock-items",
        summary: "Отримати список товарів на складі із пагінацією",
        tags: ["StockItems"],
        responses: [
            new OA\Response(response: 200, description: "Успішне отримання списку")
        ]
    )]
    public function index()
    {
        $items = StockItem::with(['category', 'suppliers'])->paginate(10);

        return StockItemResource::collection($items);
    }

    #[OA\Get(
        path: "/stock-items/{id}",
        summary: "Отримати детальну інформацію про товар за ID",
        tags: ["StockItems"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Успішне отримання товару"),
            new OA\Response(response: 404, description: "Товар не знайдено")
        ]
    )]
    public function show(StockItem $stockItem)
    {
        $stockItem->load(['category', 'suppliers']);

        return new StockItemResource($stockItem);
    }

    #[OA\Post(
        path: "/stock-items",
        summary: "Створити новий товар на складі",
        security: [["bearerAuth" => []]],
        tags: ["StockItems"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title", "sku", "quantity", "category_id"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Ноутбук ASUS ZenBook"),
                    new OA\Property(property: "sku", type: "string", example: "NB-2026-01"),
                    new OA\Property(property: "quantity", type: "integer", example: 12),
                    new OA\Property(property: "category_id", type: "integer", example: 1),
                    new OA\Property(property: "suppliers", type: "array", items: new OA\Items(type: "integer"), example: [1, 2])
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Товар успішно створено"),
            new OA\Response(response: 401, description: "Неавторизований доступ (відсутній токен)"),
            new OA\Response(response: 422, description: "Помилка валідації вхідних даних")
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'min:2', 'max:255'],
            'sku'         => ['required', 'string', 'unique:stock_items,sku'],
            'quantity'    => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'suppliers'   => ['nullable', 'array'],
            'suppliers.*' => ['integer', 'exists:suppliers,id'],
        ]);

        $validated['user_id'] = $request->user()->id;

        $item = StockItem::create($validated);

        if (!empty($validated['suppliers'])) {
            $item->suppliers()->sync($validated['suppliers']);
        }

        $item->load(['category', 'suppliers']);

        return (new StockItemResource($item))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Put(
        path: "/stock-items/{id}",
        summary: "Оновити існуючий товар (лише власник запису)",
        security: [["bearerAuth" => []]],
        tags: ["StockItems"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title", "sku", "quantity", "category_id"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Ноутбук ASUS ZenBook Pro"),
                    new OA\Property(property: "sku", type: "string", example: "NB-2026-01"),
                    new OA\Property(property: "quantity", type: "integer", example: 15),
                    new OA\Property(property: "category_id", type: "integer", example: 1),
                    new OA\Property(property: "suppliers", type: "array", items: new OA\Items(type: "integer"), example: [1])
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Товар успішно оновлено"),
            new OA\Response(response: 401, description: "Неавторизований доступ"),
            new OA\Response(response: 403, description: "Заборонено: ви не є власником цього товару"),
            new OA\Response(response: 422, description: "Помилка валідації")
        ]
    )]
    public function update(Request $request, StockItem $stockItem)
    {
        $this->authorize('update', $stockItem);

        $validated = $request->validate([
            'title'       => ['required', 'string', 'min:2', 'max:255'],
            'sku'         => ['required', 'string', Rule::unique('stock_items', 'sku')->ignore($stockItem->id)],
            'quantity'    => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'suppliers'   => ['nullable', 'array'],
            'suppliers.*' => ['integer', 'exists:suppliers,id'],
        ]);

        $stockItem->update($validated);
        $stockItem->suppliers()->sync($validated['suppliers'] ?? []);
        $stockItem->load(['category', 'suppliers']);

        return new StockItemResource($stockItem);
    }

    #[OA\Delete(
        path: "/stock-items/{id}",
        summary: "Видалити товар (лише власник запису)",
        security: [["bearerAuth" => []]],
        tags: ["StockItems"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Товар успішно видалено"),
            new OA\Response(response: 401, description: "Неавторизований доступ"),
            new OA\Response(response: 403, description: "Заборонено: ви не є власником цього товару")
        ]
    )]
    public function destroy(StockItem $stockItem)
    {
        $this->authorize('delete', $stockItem);

        $stockItem->delete();

        return response()->json(['message' => 'Товар успішно видалено.']);
    }
}