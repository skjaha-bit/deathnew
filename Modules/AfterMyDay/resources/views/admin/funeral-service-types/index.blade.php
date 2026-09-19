@extends('admin::layouts.app')

@section('title', 'Funeral Service Types')

@section('content')
    @include('admin::components.page-header', ['title' => 'Funeral Service Types', 'subtitle' => 'Service types customers can select when planning a funeral.'])
    @include('admin::components.reference-data-table', ['items' => $items, 'routeName' => $routeName, 'extraColumns' => $extraColumns])
@endsection
