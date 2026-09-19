@extends('admin::layouts.app')

@section('title', 'Bond Types')

@section('content')
    @include('admin::components.page-header', ['title' => 'Bond Types', 'subtitle' => 'Bond types available when a customer records a bond.'])
    @include('admin::components.reference-data-table', ['items' => $items, 'routeName' => $routeName, 'extraColumns' => $extraColumns])
@endsection
