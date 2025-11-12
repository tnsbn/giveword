@extends('layouts.long-page')
@section('title')Write some words @stop
@section('content')
  <div class="door-section">
    <div class="container">
        @include('element.menubar')
    </div>
  </div>

  <div class="building-section write-section">
    <div class="container">
      <form class="form-horizontal" method="post" action="{{ route('writing') }}">
        {{ csrf_field() }}
        <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
          <label for="message" class="col-md-4 control-label">(<label class="text-danger">*</label>) Message</label>

          <div class="col-md-6">
            <textarea id="message" type="text" class="form-control" name="message" rows="5" required>{{ old('message') }}</textarea>

            @if ($errors->has('message'))
              <span class="help-block">
                  <strong>{{ $errors->first('message') }}</strong>
              </span>
            @endif
          </div>
        </div>

        <div class="form-group">
          <label for="tags" class="col-md-4 control-label">Tags</label>

          <div class="col-md-6">
            <input id="tags" type="text" class="form-control" name="tags" value="{{ old('tags') }}" placeholder="Separated by comma">
          </div>
        </div>

        <div class="form-group">
          <div class="col-md-6 col-md-offset-4 text-center">
            <button type="submit" class="btn btn-primary">
              Post my words
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
@endsection
