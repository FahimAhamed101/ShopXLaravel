@extends('frontend.layouts.app')

@section('contents')
    <x-frontend.breadcrumb :items="[['label' => 'Home', 'url' => '/'], ['label' => $page->title]]" />

    <div class="page-content pt-70">
        <div class="container">
            <div class="row mb-50">
                <h1>{{ $page->title }}</h1>
                <div>{!! $page->content !!}</div>
            </div>
        </div>
    </div>
@endsection
