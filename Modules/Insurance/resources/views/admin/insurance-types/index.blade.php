@extends('admin::layouts.app')

@section('title', 'Insurance Types')

@section('content')
    @include('admin::components.page-header', ['title' => 'Insurance Types', 'subtitle' => 'Types available when a customer records an insurance policy.'])
    @include('admin::components.reference-data-table', ['items' => $items, 'routeName' => $routeName, 'extraColumns' => $extraColumns])
@endsection
