<link rel="stylesheet" href="/css/alone/chat.css">
<script type="text/javascript" src="/js/alone/chat.js"></script>
<script type="text/javascript">
  const csrfToken = '{{csrf_token()}}';
</script>

<div class="btn-chat" data-status="{{auth()->user() ? auth()->user()->id : ''}}">
    <div class="bubble">
        <div class="bubble-outer-dot">
            <span class="bubble-inner-dot"></span>
        </div>
    </div>
    <img src="/images/chat/chat-icon.png">
</div>

<div class="modal" id="modal-chat" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content {{auth()->user() ? '' : 'not-logged-in'}}">
            @if(auth()->user())
                <div class="modal-header">
                    <h2 class="modal-title">Live chat with {{\App\Helpers\ChatHelper::getPartnerName()}}</h2>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&minus;</span>
                    </button>
                </div>
                <div class="modal-body">
    {{--                {{$chatPartnerName}} is not in chatroom, please wait!--}}
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
            @else
                <div class="modal-header">
                    <h2 class="modal-title">Help Center</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&minus;</span>
                    </button>
                </div>
                <div class="modal-body not-logged-in">
                    Please login to use this service.
                </div>
            @endif
        </div>
    </div>
</div>
