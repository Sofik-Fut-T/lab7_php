@extends('layouts.app')

@section('title', 'Список товарів')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Товари на складі</h2>
        <a href="{{ route('stock-items.create') }}" class="btn btn-success">+ Додати товар</a>
    </div>

    <div class="card shadow-sm">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Назва</th>
                    <th>Артикул (SKU)</th>
                    <th>Кількість</th>
                    <th>Категорія</th>
                    <th>Постачальники</th>
                    <th class="text-end">Дії</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->title }}</td>
                        <td><code>{{ $item->sku }}</code></td>
                        <td>{{ $item->quantity }} шт.</td>
                        <td>{{ $item->category->title ?? '—' }}</td>
                        <td>
                            @foreach($item->suppliers as $supplier)
                                <span class="badge bg-secondary">{{ $supplier->name }}</span>
                            @endforeach
                        </td>
                        <td class="text-end">
                            <a href="{{ route('stock-items.edit', $item) }}" class="btn btn-sm btn-warning">Редагувати</a>
                            <form action="{{ route('stock-items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Видалити цей товар?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Видалити</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Товарів поки немає.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
