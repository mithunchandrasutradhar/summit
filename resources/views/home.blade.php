@extends('layouts.app')

@section('title', __('Freelancer Summit Bangladesh 2026'))

@section('content')
    @foreach ($sections as $section)
        @includeIf('home.sections.'.$section->section_key, ['section' => $section])
    @endforeach
@endsection
