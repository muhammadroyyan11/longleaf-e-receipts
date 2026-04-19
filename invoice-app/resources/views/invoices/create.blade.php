@extends('layouts.app')
@section('title', 'Buat Invoice')
@section('page-title', 'Buat Invoice Baru')

@section('content')
<form method="POST" action="{{ route('invoices.store') }}">
    @csrf
    @include('invoices._form', ['invoice' => $invoice])
</form>
@endsection
