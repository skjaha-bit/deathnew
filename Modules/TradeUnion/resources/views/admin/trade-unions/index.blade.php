@extends('admin::layouts.app')

@section('title', 'Trade Unions')

@section('content')
    @include('admin::components.page-header', ['title' => 'Trade Unions', 'subtitle' => 'Trade unions customers can select when adding their membership.'])
    @include('admin::components.reference-data-table', ['items' => $items, 'routeName' => $routeName, 'extraColumns' => $extraColumns])
@endsection
