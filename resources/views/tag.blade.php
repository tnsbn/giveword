@extends(count($words ?? null) == 0 ? 'layouts.default' : 'layouts.long-page')
@section('style')
    <link rel="stylesheet" href="/css/alone/takeword.css">
    <link rel="stylesheet" href="/css/alone/right-panel.css">
@endsection
@section('title')Tags #{{$tagName}} @stop
@section('content')
  <div class="door-section">
    <div class="container">
        @include('element.menubar')
    </div>
  </div>


  @if(count($words ?? null) == 0)
    <div class="text-center well">
      <h3>There's no message for this tag</h3>
    </div>
  @else
  <div class="building-section takeword-section">
    <div class="container">
        <div class="main-panel">
          @php $selectedIpp = app('request')->input('ipp') ?? 5 ; @endphp
          <div class="custom-paging">
            <form class="form-horizontal item-per-page" method="get" action="{{ route('tag', $tagName) }}">
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
          <div class="elements">
            @foreach($words ?? [] as $word)
              @php ($word = $word->toArray()) @endphp
              @include('element.takeword.takeword-item', ['data' => $word])
            @endforeach
          </div>
          <div class="nav-paging">{{$words->render()}}</div>
      </div>

      @include('element.right-panel', ['tags' => $limitTags])

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
  </div>
  @endif
@endsection
