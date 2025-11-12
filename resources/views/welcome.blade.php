@extends('layouts.default')
@section('style')
  <link rel="stylesheet" href="/css/alone/draw.css">
@endsection
@section('script')
  <script type="text/javascript" src="/js/alone/draw.js"></script>
  <script type="text/javascript">
    const csrf = '{{csrf_token()}}';
  </script>

@endsection
@section('title')Home @stop
@section('content')
  <div class="door-section">
    <div class="container">
        @include('element.menubar')
    </div>
  </div>

  <div class="search-section">
    <div class="container">
      <div class="half-page">
        @include('element.search.form')
        @include('element.draw.canvas')
      </div>
    </div>
  </div>

{{--  <div class="draw-section">--}}
{{--    <div class="container">--}}
{{--      @include('element.draw.canvas')--}}
{{--    </div>--}}
{{--  </div>--}}
@endsection
