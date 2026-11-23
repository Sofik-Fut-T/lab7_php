@extends('layouts.app')

@section('title', 'Головна сторінка')

@section('content')
<div class="p-5 mb-4 bg-white rounded-3 shadow-sm">
    <h1 class="display-5 fw-bold">📦 Система складського обліку</h1>
    <p class="col-md-8 fs-4">Лабораторна робота №8: автентифікація, авторизація та документація REST API.</p>
    <a href="{{ route('stock-items.index') }}" class="btn btn-primary btn-lg">Перейти до товарів</a>
    <a href="/api/documentation" class="btn btn-outline-dark btn-lg ms-2">Відкрити Swagger UI</a>
</div>
@endsection
