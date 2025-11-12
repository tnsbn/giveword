@extends('layouts.long-page')
@section('style')
  <link rel="stylesheet" href="/css/alone/takeword.css">
  <link rel="stylesheet" href="/css/alone/search.css">
  <link rel="stylesheet" href="/css/alone/right-panel.css">
@endsection
@section('script')
  <script type="text/javascript" src="/js/alone/takeword.js"></script>
  <script type="text/javascript" src="/js/alone/search.js"></script>
  <script type="text/javascript">
    const action = '{{$action ?? ""}}';
  </script>
@endsection
@section('title')Take Words @stop
@section('content')
  <div class="door-section">
    <div class="container">
        @include('element.menubar')
    </div>
  </div>

  <div class="building-section takeword-section search-section">
    <div class="container">
      @include('element.search.form', ['action' => $action ?? null])

      @if (isset($words, $query))
        <div class="col-xs-12 search-info">
            <?php
            $msg = "Doesn't found any data for keyword '{$query['keyword']}'";
            $searchName = isset($searchName) ? " with " . $searchName : "";
            if (isset($words) && !empty($words->items())) {
                $msg = $words->total() . ($words->total() > 1 ? ' words' : ' word') . ' found';
            }
            $msg .= '. Result in ' . number_format($time, 3) . 's' . $searchName;
            ?>
            <span class="info">{{ $msg }}</span>
        </div>
      @endif
      <div class="main-panel">
          <div class="elements">
            @foreach($words ?? [] as $word)
              @php ($word = $word->toArray()) @endphp
              @include('element.takeword.takeword-item', ['data' => $word])
            @endforeach
            @if($words->hasMorePages())
              <div class="text-right btn-more-wrap">
                <input type="hidden" id="keyword" value="{{$query['keyword'] ?? ''}}">
                <button class="btn-danger btn-more-takeword">Load more . . .</button>
              </div>
            @endif
          </div>
      </div>

      @include('element.right-panel', ['tags' => $limitTags])
    </div>

    <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog">
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
            <button type="button" class="btn" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary btn-ok">Update</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
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
            <span class="message-to"></span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn" data-dismiss="modal">No</button>
            <button type="button" class="btn btn-primary btn-ok">Yes</button>
          </div>
        </div>
      </div>
    </div>

  </div>
@endsection
