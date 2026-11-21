@extends('layouts.app')

@section('title', 'Головна — Складський облік')

@section('content')
    <div class="p-5 mb-4 bg-white rounded-3 shadow-sm text-center">
        <h1 class="display-5 fw-bold">Система складського обліку</h1>
        <p class="fs-5 text-muted">Лабораторна робота №7 — Laravel Blade SSR</p>
        <a href="{{ route('stock-items.index') }}" class="btn btn-primary btn-lg mt-3">
            Перейти до товарів
        </a>
    </div>
@endsection
