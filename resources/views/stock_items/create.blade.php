@extends('layouts.app')

@section('title', 'Додати товар')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header"><h4>Додати новий товар</h4></div>
        <div class="card-body">
            <form action="{{ route('stock-items.store') }}" method="POST">
                @csrf
                @include('stock_items._form')
                <button type="submit" class="btn btn-primary">Зберегти</button>
                <a href="{{ route('stock-items.index') }}" class="btn btn-secondary">Скасувати</a>
            </form>
        </div>
    </div>
@endsection
