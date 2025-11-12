@extends('layouts.long-page')
@section('title')My Handbook @stop
@section('style')
  <link rel="stylesheet" href="/css/alone/profile.css">
@endsection
@section('script')
  <script type="text/javascript" src="/js/alone/profile.js"></script>
@endsection
@section('content')
  <div class="door-section">
    <div class="container">
        @include('element.menubar')
    </div>
  </div>

  <div class="building-section profile-section">
    <div class="container">
      <div class="form-horizontal form-profile col-md-12">
        {{ csrf_field() }}
        @if (isset($update_profile_success))
          <div class="panel-success">
            <div class="alert alert-success">
              {{ $update_profile_success }}
            </div>
          </div>
        @endif
        @if (isset($update_profile_error))
          <div class="panel-success">
            <div class="alert alert-danger">
              {{ $update_profile_error }}
            </div>
          </div>
        @endif
        <div class="form-group">
          <label class="col-md-2 control-label">Name</label>

          <div class="col-md-8">
            <input id="name" type="text" class="form-control" disabled name="name" data-original="{{ Auth::user()->name }}" value="{{ Auth::user()->name }}" required>

            <span class="help-block hidden"></span>
          </div>
          <label for="name" class="col-md-1 control-label glyphicon btn-edit" data-edit="name">Edit</label>
        </div>
        <div class="form-group hidden">
          <div class="text-right col-md-10">
            <button type="button" class="btn btn-primary btn-save-info" for="name">Save</button>
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-2 control-label">Password</label>
          <div class="col-md-9 text-left">
            <label class="form-control link-change-password"><span class="btn-link" data-bs-toggle="collapse" data-bs-target="#form-password">Change password</span></label>
            @if (isset($update_password_success))
            <div class="panel-success">
              <div class="alert alert-success">
                {{ $update_password_success }}
              </div>
            </div>
            @endif
            <div class="collapse" id="form-password">
              <div class="card card-body">
                <form method="post" action="{{ route('profile_password') }}">
                  {{ csrf_field() }}
                  @if ($errors->any() || session('change-password-error'))
                  <div class="panel-body">
                    <div class="alert alert-danger">
                      {{ session('change-password-error') ?? 'Password change failed!' }}
                    </div>
                  </div>
                  @endif
                  <div class="form-group{{ $errors->has('current') ? ' has-error' : '' }}">
                    <label for="current" class="col-md-3 control-label">Current</label>
                    <div class="col-md-8">
                      <input id="current" type="password" class="form-control" name="current" required>

                      @if ($errors->has('current'))
                        <span class="help-block">{{ $errors->first('current') }}</span>
                      @endif
                    </div>
                  </div>

                  <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password" class="col-md-3 control-label">New</label>
                    <div class="col-md-8">
                      <input id="password" type="password" class="form-control" name="password" required>

                      @if ($errors->has('password'))
                        <span class="help-block">{{ $errors->first('password') }}</span>
                      @endif
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="password-confirm" class="col-md-3 control-label">Re-type New</label>

                    <div class="col-md-8">
                      <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-md-8 col-md-offset-3 text-right">
                      <button type="button" class="btn btn-warning cancel">Cancel</button>
                      <button type="submit" class="btn btn-primary btn-save-password">Save Password</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
@endsection
