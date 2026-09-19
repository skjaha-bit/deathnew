@extends('admin::layouts.app')

@section('title', 'Insurance Companies')

@section('content')
    @include('admin::components.page-header', ['title' => 'Insurance Companies', 'subtitle' => 'Companies customers can select when adding an insurance policy.'])
    @include('admin::components.reference-data-table', ['items' => $items, 'routeName' => $routeName, 'extraColumns' => $extraColumns])
@endsection
