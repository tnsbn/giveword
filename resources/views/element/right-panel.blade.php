<div class="right-panel">
  <ul class="nav nav-tabs">
{{--    <li><a data-bs-toggle="tab" href="#popular">Popular</a></li>--}}
    <li class="active"><a data-bs-toggle="tab" href="#tags">Words Tags</a></li>
  </ul>

  <div class="tab-content">
{{--    <div id="popular" class="tab-pane fade">--}}
{{--      <p>No Hottest Comments</p>--}}
{{--    </div>--}}
    <div id="tags" class="tab-pane fade in active">
      @foreach($tags as $tag)
        <a href="/tag/{{$tag}}" class="badge badge-takeword">{{$tag}}</a>
      @endforeach
    </div>
  </div>
</div>
