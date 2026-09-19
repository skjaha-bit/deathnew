@extends('admin::layouts.app')

@section('title', 'Account Types')

@section('content')
    @include('admin::components.page-header', ['title' => 'Account Types', 'subtitle' => 'Account types available when a customer records a bank account.'])
    @include('admin::components.reference-data-table', ['items' => $items, 'routeName' => $routeName, 'extraColumns' => $extraColumns])
@endsection
