@extends('admin::layouts.app')

@section('title', 'Property Types')

@section('content')
    @include('admin::components.page-header', ['title' => 'Property Types', 'subtitle' => 'Property types customers can select when adding a property.'])
    @include('admin::components.reference-data-table', ['items' => $items, 'routeName' => $routeName, 'extraColumns' => $extraColumns])
@endsection
