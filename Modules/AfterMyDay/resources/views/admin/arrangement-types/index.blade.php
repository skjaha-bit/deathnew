@extends('admin::layouts.app')

@section('title', 'Arrangement Types')

@section('content')
    @include('admin::components.page-header', ['title' => 'Arrangement Types', 'subtitle' => 'Arrangement types customers can select when planning a funeral.'])
    @include('admin::components.reference-data-table', ['items' => $items, 'routeName' => $routeName, 'extraColumns' => $extraColumns])
@endsection
