@extends('auth4you::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('auth4you.name') !!}</p>
@endsection
