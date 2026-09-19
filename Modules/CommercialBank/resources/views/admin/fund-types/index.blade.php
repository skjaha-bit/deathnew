@extends('admin::layouts.app')

@section('title', 'Fund Types')

@section('content')
    @include('admin::components.page-header', ['title' => 'Fund Types', 'subtitle' => 'Fund types available when a customer records a fund.'])
    @include('admin::components.reference-data-table', ['items' => $items, 'routeName' => $routeName, 'extraColumns' => $extraColumns])
@endsection
