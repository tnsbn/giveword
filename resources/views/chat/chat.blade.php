@extends('layouts.long-page')
@section('title')Chat Center @stop
@section('script')
    <script type="text/javascript" src="/js/alone/chat.js"></script>
    <script type="text/javascript">
      const csrfToken = '{{csrf_token()}}';
    </script>
@endsection
@section('style')
    <link rel="stylesheet" href="/css/alone/chat.css">
@endsection
@section('content')
    <div class="building-section chat-section admin">
        <div class="container">
            <div class="control col-sx-12">
                <?php
                $strChecked = \App\Helpers\ChatHelper::isOnline() ? ' checked' : ''
                ?>
                <label for="online-chat">Set online</label>
                <input type="checkbox" id="online-chat" {{$strChecked}}>
            </div>
            <div class="chat-center">
                <div class="user-list col-md-3">
                    @include('chat.user-list')
                </div>
                <div class="chat-box col-md-9">
                    @include('chat.chat-box')
                </div>
            </div>
        </div>

        <div>
            Routes:<br>
            User online: <a href="{{route('admin.user-online-status')}}">Go to</a><br>
        </div>
    </div>
@endsection
