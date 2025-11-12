@extends('layouts.long-page')
@section('title')My Handbook @stop
@section('script')
    <script type="text/javascript" src="/js/alone/handbook.js"></script>
    <script type="text/javascript" src="/js/alone/chat.js"></script>
    <script type="text/javascript">
        {{--const csrfToken = '{{csrf_token()}}';--}}
    </script>
@endsection
@section('style')
    <link rel="stylesheet" href="/css/alone/chat.css">
@endsection
@section('content')
    <div class="door-section">
        <div class="container">
            @include('element.menubar')
        </div>
    </div>

    <div class="building-section handbook-section chat-section">
        <div class="container">
            @php $selectedIpp = app('request')->input('ipp') ?? 5 ; @endphp
            <div class="custom-paging">
                <form class="form-horizontal item-per-page" method="get" action="{{ route('handbook') }}">
                    <select name="ipp">
                        @foreach([5, 10, 15, 20] as $ipp)
                            @php $str = ($ipp == $selectedIpp ? "selected='selected'" : ""); @endphp
                            <option value="{{$ipp}}" {{$str}}>View {{$ipp}} items on page</option>
                        @endforeach
                    </select>
                    <button class="btn btn-light text-right">Go</button>
                </form>
            </div>

            <div class="nav-paging">{{$words->render()}}</div>
            <div class="handbook">
                @foreach($words ?? [] as $word)
                    @php ($word = $word->toArray()) @endphp
                    @include('element.handbook.item', ['data' => $word])
                @endforeach
            </div>
            <div class="nav-paging">{{$words->render()}}</div>

            <div class="modal" id="modal-edit" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title">Edit message</h2>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn cancel" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary btn-ok">Update</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal" id="modal-delete" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title">Delete confirmation</h2>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete the message:<br>
                            <span class="message"></span>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn cancel" data-dismiss="modal">No</button>
                            <button type="button" class="btn btn-primary btn-ok">Yes</button>
                        </div>
                    </div>
                </div>
            </div>

            @if($canChat ?? false)
                <div class="btn-chat">
                    <div class="bubble">
                        <div class="bubble-outer-dot">
                            <span class="bubble-inner-dot"></span>
                        </div>
                    </div>
                    <img src="/images/chat/chat-icon.png">
                </div>

                @if((isset($isMainUser) && $isMainUser) || (isset($chatStatus) && $chatStatus == 'start'))
                <?php
                $extraInfo = '';
                if (isset($isMainUser) && $isMainUser && empty($chatPartnerName)) {
                    $extraInfo = '<a href="#" class="load-name">load name</a>';
                }
                ?>
                <div class="modal" id="modal-chat" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="modal-title">Live chat with {{$chatPartnerName}} </h2>
                                @if (isset($isMainUser) && $isMainUser && empty($chatPartnerName))
                                <a href="#" class="load-name">load name</a>
                                @endif
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&minus;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                            </div>
                            <div class="modal-footer">
                                <form action="/api/message" method="post" class="w-[700px]">
                                    <div class="text-box">
                                        <textarea id="message" name="message" cols="20" rows="3"></textarea>
                                    </div>
                                    <div class="send">
                                        <button type="button" class="btn btn-primary btn-send">Send</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="modal" id="modal-chat" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="modal-title">Live chat with {{$chatPartnerName}}</h2>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&minus;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                {{$chatPartnerName}} is not in chatroom, please wait!
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endif

        </div>

    </div>
@endsection
