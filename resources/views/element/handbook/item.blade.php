<?php
$data = $data ?? [
        'price' => 1,
        'message' => '',
        'tags' => [],
        'created_at' => '',
        'updated_at' => '1',
        'id' => 'none-' . uniqid(),
    ];
$shortTextLen = 400;
$limitRows = 4;
?>
<div class="handbook-item {{$data['id']}}">
  @if (isset(Auth::user()->id, $data['user_id']) && $data['user_id'] == Auth::user()->id)
  <div class="edit-message">
    <a class="btn dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">. . .</a>
    <div class="dropdown-menu">
      <a class="list-group-item" href="#" data-edit="{{$data['id']}}">Edit this</a>
      <a class="list-group-item" href="#" data-delete="{{$data['id']}}">Delete this</a>
    </div>
  </div>
  @endif

  <div>
      <span class="glyphicon glyphicon-user"></span> {{$data['username']}}
  </div>

  <div class="date"><span class="glyphicon glyphicon-calendar"></span>
      {{$data['short_date']}}{{ $data['created_at'] != $data['updated_at'] ? " (Editted)" : "" }}
  </div>

  <div class="message">
    <input type="checkbox" class="read-more-state" id="post-{{$data['id']}}">

    <div class="read-more-wrap">
      <?php
        $shortHtml = nl2br(substr($data['message'], 0, $shortTextLen), false);
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
        $moreHtml .= nl2br(substr($data['message'], $shortTextLen));
        $html = $shortHtml . '<p class="read-more-target">' . $moreHtml . '</p>';
        echo $html;
      ?>
    </div>
    @if(strlen($data['message']) > $shortTextLen || count($shortLines) > $limitRows)
      <label for="post-{{$data['id']}}" class="read-more-trigger"></label>
    @endif
  </div>

  <div class="taken-count">{{$data['taken_count']}} {{$data['taken_count'] > 1 ? 'Steels' : 'Steel'}}</div>

  <div class="tags">
    @foreach($data['tags'] as $tag)
      <a href="/tag/{{$tag}}" class="btn-primary badge ">{{$tag}}</a>
    @endforeach
  </div>

{{--  <div class="comment">--}}
{{--    @if ($data['id'] % 3 < 2)--}}
{{--      <input type="checkbox" class="comment-state" id="chb-comment-{{$data['id']}}">--}}
{{--      <label for="chb-comment-{{$data['id']}}" class="count" data-bs-toggle="collapse" data-bs-target="#comment-{{$data['id']}}">--}}
{{--        <span class="glyphicon glyphicon-comment"></span> (4 comments) . . .--}}
{{--      </label>--}}

{{--      <div id="comment-{{$data['id']}}" class="collapse">--}}
{{--        - Comment 1<br>--}}
{{--        - Comment 2<br>--}}
{{--        - Comment 3<br>--}}
{{--        - Comment 4<br>--}}
{{--      </div>--}}
{{--    @else--}}
{{--      <span class="glyphicon glyphicon-comment"></span> No comments--}}
{{--    @endif--}}
{{--  </div>--}}
</div>
