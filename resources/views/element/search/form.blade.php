@if(($action ?? '') == 'search_elastic' && !env('ELASTICSEARCH_ENABLED'))
    <form class="form-horizontal search-form col-xs-12">
        <div class="col-sm-8 col-xs-12">
            <input id="keyword" type="text" class="form-control" disabled>
        </div>
        <div class="col-sm-2 col-xs-12">
            <span class="btn btn-danger col-xs-12" disabled="">Search</span>
        </div>
        <div class="col-xs-12 text-danger bg-danger">Elastic service is not available now. Please use normal search feature!</div>
    </form>
@else
    <form class="form-horizontal search-form col-xs-12" method="post" action="{{ route($action ?? 'search_takeword') }}">
        {{ csrf_field() }}
        <div class="col-sm-8 col-xs-12">
            <input id="keyword" type="text" class="form-control" name="keyword" value="{{ $query['keyword'] ?? '' }}" autofocus>
        </div>
        <div class="col-sm-2 col-xs-12">
            <button type="submit" class="btn btn-primary col-xs-12">Search</button>
        </div>
    </form>
@endif

