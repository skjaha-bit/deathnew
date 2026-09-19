@extends('admin::layouts.app')

@section('title', 'Banks')

@section('content')
    @include('admin::components.page-header', ['title' => 'Banks', 'subtitle' => 'Banks customers can select when adding a commercial bank account.'])
    @include('admin::components.reference-data-table', ['items' => $items, 'routeName' => $routeName, 'extraColumns' => $extraColumns])
@endsection
