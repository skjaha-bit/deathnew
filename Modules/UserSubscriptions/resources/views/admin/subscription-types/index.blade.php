@extends('admin::layouts.app')

@section('title', 'Subscription Types')

@section('content')
    @include('admin::components.page-header', ['title' => 'Subscription Types', 'subtitle' => 'Subscription types customers can select.'])
    @include('admin::components.reference-data-table', ['items' => $items, 'routeName' => $routeName, 'extraColumns' => $extraColumns])
@endsection
