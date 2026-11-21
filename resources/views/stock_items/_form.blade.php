<div class="mb-3">
    <label class="form-label">Назва товару</label>
    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
           value="{{ old('title', $stockItem->title ?? '') }}">
    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Артикул (SKU)</label>
    <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
           value="{{ old('sku', $stockItem->sku ?? '') }}">
    @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Кількість</label>
    <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
           value="{{ old('quantity', $stockItem->quantity ?? 0) }}">
    @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Категорія</label>
    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
        <option value="">-- Оберіть категорію --</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}"
                @selected(old('category_id', $stockItem->category_id ?? '') == $category->id)>
                {{ $category->title }}
            </option>
        @endforeach
    </select>
    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label d-block">Постачальники</label>
    @php
        $selectedSuppliers = old('suppliers', isset($stockItem) ? $stockItem->suppliers->pluck('id')->toArray() : []);
    @endphp
    @foreach($suppliers as $supplier)
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="suppliers[]"
                   value="{{ $supplier->id }}" id="sup_{{ $supplier->id }}"
                   @checked(in_array($supplier->id, $selectedSuppliers))>
            <label class="form-check-label" for="sup_{{ $supplier->id }}">{{ $supplier->name }}</label>
        </div>
    @endforeach
</div>
