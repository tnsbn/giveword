@foreach($chatQueues as $chatQueue)
    <div class="user-item data-queue-id='{{$chatQueue['id']}}' data-receiver-id='{{$chatQueue->sender_id}}'">
        <div>
            <span class="glyphicon glyphicon-user"></span> {{$chatQueue->getReceiver()->name}}
        </div>
        <div class="date"><span class="glyphicon glyphicon-calendar"></span>
        </div>
    </div>
@endforeach
