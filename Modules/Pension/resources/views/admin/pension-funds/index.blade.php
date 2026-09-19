@extends('admin::layouts.app')

@section('title', 'Pension Funds')

@section('content')
    @include('admin::components.page-header', ['title' => 'Pension Funds', 'subtitle' => 'Pension funds customers can select when adding a pension entry.'])
    @include('admin::components.reference-data-table', ['items' => $items, 'routeName' => $routeName, 'extraColumns' => $extraColumns])
@endsection
