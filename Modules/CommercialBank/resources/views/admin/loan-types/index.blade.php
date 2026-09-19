@extends('admin::layouts.app')

@section('title', 'Loan Types')

@section('content')
    @include('admin::components.page-header', ['title' => 'Loan Types', 'subtitle' => 'Loan types available when a customer records a bank loan.'])
    @include('admin::components.reference-data-table', ['items' => $items, 'routeName' => $routeName, 'extraColumns' => $extraColumns])
@endsection
