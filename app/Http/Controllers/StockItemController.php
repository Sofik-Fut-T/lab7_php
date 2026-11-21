<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\StockItem;
use App\Models\Supplier;
use Illuminate\Http\Request;

class StockItemController extends Controller
{
    public function index()
    {
        $items = StockItem::with(['category', 'suppliers'])->latest()->get();
        return view('stock_items.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('stock_items.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:stock_items,sku',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'suppliers' => 'nullable|array',
            'suppliers.*' => 'exists:suppliers,id',
        ]);

        $item = StockItem::create($validated);
        $item->suppliers()->sync($request->input('suppliers', []));

        return redirect()->route('stock-items.index')
            ->with('success', 'Товар успішно додано!');
    }

    public function edit(StockItem $stockItem)
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('stock_items.edit', compact('stockItem', 'categories', 'suppliers'));
    }

    public function update(Request $request, StockItem $stockItem)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:stock_items,sku,' . $stockItem->id,
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'suppliers' => 'nullable|array',
            'suppliers.*' => 'exists:suppliers,id',
        ]);

        $stockItem->update($validated);
        $stockItem->suppliers()->sync($request->input('suppliers', []));

        return redirect()->route('stock-items.index')
            ->with('success', 'Товар успішно оновлено!');
    }

    public function destroy(StockItem $stockItem)
    {
        $stockItem->delete();

        return redirect()->route('stock-items.index')
            ->with('success', 'Товар видалено!');
    }
}
