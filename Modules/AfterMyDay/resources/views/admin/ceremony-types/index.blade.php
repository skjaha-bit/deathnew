@extends('admin::layouts.app')

@section('title', 'Ceremony Types')

@section('content')
    @include('admin::components.page-header', ['title' => 'Ceremony Types', 'subtitle' => 'Ceremony types customers can select when planning a funeral.'])
    @include('admin::components.reference-data-table', ['items' => $items, 'routeName' => $routeName, 'extraColumns' => $extraColumns])
@endsection
