@extends('layouts.app')

@section('content')
@php
    $item = $item ?? $stockItem;
@endphp
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h4 class="mb-0">Редагувати товар</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('stock-items.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')

            @include('stock_items._form')

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Оновити</button>
                <a href="{{ route('stock-items.index') }}" class="btn btn-secondary">Скасувати</a>
            </div>
        </form>
    </div>
</div>
@endsection
