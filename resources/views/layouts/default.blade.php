<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title')</title>

  <!-- Styles -->
  <link rel="stylesheet" href="/css/app.css">
  @yield('style')

  <!-- Javascript -->
  <script src="/js/jquery.js"></script>
  <script src="/js/bootstrap.bundle.js"></script>
  <script src="/js/app.js"></script>
  <script src="/js/alone/default.js"></script>
  @yield('script')
</head>
<body class="default">
<div class="flex-center position-ref full-height with-bg-img">
  <div class="main_menu_bg navbar-fixed-top">
    <div class="container">
      <div class="row">
        <div class="nave_menu">
          <nav class="navbar navbar-default" id="navmenu">
            <div class="container-fluid">
              <!-- Brand and toggle get grouped for better mobile display -->
              <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-bs-toggle="collapse" data-bs-target="#navbar-mobile" aria-expanded="false">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
{{--                <a class="navbar-brand" href="#"><img src="images/logo.png" alt=""></a>--}}
              </div>

              <!-- Collect the nav links, forms, and other content for toggling -->
              <div class="collapse navbar-collapse" id="navbar-mobile">
                <ul class="nav navbar-nav navbar-right">
                  <li class=""><a href="/">Home</a></li>
                  <li class=""><a href="{{ url('/portfolio') }}">Technologies</a></li>
                  @auth
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                        {{ Auth::user()->name }} <span class="caret"></span>
                      </a>

                      <ul class="dropdown-menu">
                        <li><a href="{{ url('/handbook') }}">My Handbook</a></li>
                        <li><a href="{{ url('/taken-words') }}">Taken Words</a></li>
                        <li><a href="{{ url('/writing') }}">Write something</a></li>
                        <li><a href="{{ url('/profile') }}">Profile</a></li>
                        <li>
                          <a href="{{ route('logout') }}"
                             onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            Logout
                          </a>

                          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                          </form>
                        </li>
                      </ul>
                    </li>
                  @else
                    <li><a href="{{ url('login') }}">Login</a></li>
                    <li><a href="{{ url('register') }}">Register</a></li>
                  @endauth
                </ul>
              </div>
            </div>
          </nav>
        </div>
      </div>
    </div>
  </div>

  @yield('content')

  <div class="bubble-chat chat-section user">
    @include('chat.bubble-chat')
  </div>

</div>
</body>
</html>
