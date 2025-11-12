@foreach($chatQueues as $chatQueue)
<div class="user-item data-queue-id='{{$chatQueue['id']}}' data-receiver-id='{{$chatQueue->sender_id}}'">
    <div>
        <span class="glyphicon glyphicon-user"></span> {{$chatQueue->getReceiver()->name}}
    </div>
    <div class="date"><span class="glyphicon glyphicon-calendar"></span>
        <?php
        $unreadCount = $chatQueue->unreadMessagesCount();
        ?>
        @if ($unreadCount > 0)
            <span class="font-bold p-px px-2 text-xs shrink-0 rounded-full bg-blue-500 text-white">
                {{$unreadCount}}
            </span>
        @endif
    </div>
</div>
@endforeach
