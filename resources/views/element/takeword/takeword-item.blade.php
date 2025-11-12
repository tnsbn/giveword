<?php
$data = $data ?? [
        'price' => '',
        'message' => '',
        'short_date' => '',
        'created_at' => '',
        'updated_at' => '1',
        'id' => 'none-' . uniqid(),
    ];
$limitChars = 400;
$limitRows = 4;
$num = ($data['id'] % \App\Constants\AppConst::NUM_IMG_ITEMS) + 1;
$defaultSrc = "/images/thumbs/thumb-" . $num . ".jpg";
$imgSrc = getCachedThumb("thumb-" . $num . ".jpg", $defaultSrc);
?>
<div class="takeword-item {{ $data['id'] }}">
  <div class="item-image">
    <img src="{{$imgSrc}}">
  </div>
  <div class="word">
{{--    @if (isset(Auth::user()->id, $data['user_id']) && $data['user_id'] == Auth::user()->id)--}}
{{--      <div class="edit-message">--}}
{{--        <a class="btn dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">. . .</a>--}}
{{--        <div class="dropdown-menu">--}}
{{--          <a class="list-group-item" href="#" data-edit="{{$data['id']}}">Edit this</a>--}}
{{--          <a class="list-group-item" href="#" data-delete="{{$data['id']}}">Delete this</a>--}}
{{--        </div>--}}
{{--      </div>--}}
{{--    @endif--}}

    <div class="author">
      <span class="glyphicon glyphicon-user"></span> {{$data['username']}}
    </div>

    <div class="date"><span class="glyphicon glyphicon-calendar"></span>
        {{$data['short_date']}}{{ $data['created_at'] != $data['updated_at'] ? " (Editted)" : "" }}
    </div>

    <div class="taken-count">{{$data['taken_count']}} {{$data['taken_count'] > 1 ? 'Steels' : 'Steel'}}</div>

    <div class="message">
      <input type="checkbox" class="read-more-state" id="message-{{$data['id']}}">

      <div class="read-more-wrap">
          <?php
          $shortHtml = nl2br(substr($data['message'], 0, $limitChars), false);
          $moreHtml = "";
          $shortLines = explode('<br>', $shortHtml);
          $html = "";
          if (count($shortLines) > $limitRows) {
              $shortHtml = "";
              for ($i = 0; $i < $limitRows; $i++) {
                  $shortHtml .= $shortLines[$i] . "<br>";
              }
              for ($i = $limitRows; $i < count($shortLines); $i++) {
                  $moreHtml .= $shortLines[$i] . "<br>";
              }
          }
          $moreHtml .= nl2br(substr($data['message'], $limitChars));

          $html = $shortHtml . '<p class="read-more-target">' . $moreHtml . '</p>';
          echo $html;
          ?>

      </div>
      @if(strlen($data['message']) > $limitChars || count($shortLines) > $limitRows)
        <label for="message-{{$data['id']}}" class="read-more-trigger"></label>
      @endif
    </div>

{{--    <div>--}}
{{--        Price: <label class="text">{{$data['price']}}</label>--}}
{{--    </div>--}}

    <div class="tags">
        @foreach($data['tags'] as $tag)
            <a href="/tag/{{$tag}}" class="btn-primary badge ">{{$tag}}</a>
        @endforeach
    </div>

    @if ($data['already_taken'])
    <div class="take-this">
        <span class="btn-already-taken">Already taken</span>
    </div>
    @elseif ($data['can_take_this'])
    <div class="take-this">
        <form id="take-this-form" action="{{ route('take_this_word') }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="word_id" value="{{$data['id']}}">
            <button class="btn-take-this" type="submit" data-id="{{$data['id']}}">Take this</button>
        </form>
    </div>
    @endif
  </div>

{{--  <div class="comment">--}}
{{--    @if ($data['id'] % 3 < 2)--}}
{{--    <input type="checkbox" class="comment-state" id="chb-comment-{{$data['id']}}">--}}
{{--    <label for="chb-comment-{{$data['id']}}" class="count" data-bs-toggle="collapse" data-bs-target="#comment-{{$data['id']}}">--}}
{{--      <span class="glyphicon glyphicon-comment"></span> (4 comments) . . .--}}
{{--    </label>--}}

{{--    <div id="comment-{{$data['id']}}" class="collapse">--}}
{{--      - Comment 1<br>--}}
{{--      - Comment 2<br>--}}
{{--      - Comment 3<br>--}}
{{--      - Comment 4<br>--}}
{{--    </div>--}}
{{--    @else--}}
{{--      <span class="glyphicon glyphicon-comment"></span> No comments--}}
{{--    @endif--}}
{{--  </div>--}}
</div>
