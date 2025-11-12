<div class="a-row col-xs-12 random-buttons">
    <button type="button" class="btn btn-secondary btn-random col-sm-4 col-xs-12">Today's words!</button>
    <button type="button" class="btn btn-secondary btn-by-drawing col-sm-6 col-xs-12" data-bs-toggle="collapse" data-bs-target="#collapse-draw" aria-expanded="false" aria-controls="collapse-draw">Get words by drawing ...</button>
</div>

<div class="a-row collapse col-xs-12" id="collapse-draw">
    <form class="form-horizontal draw-form" method="post" action="{{ route('api.word-by-draw') }}">
        {{ csrf_field() }}
        <div class="col-sm-10 col-xs-12">
            <canvas id="canvas"></canvas>
        </div>
        <div class="col-sm-10 col-xs-12">
            <button type="button" class="btn btn-secondary btn-undo col-sm-3 col-xs-12">Undo</button>
            <button type="button" class="btn btn-secondary btn-clear col-sm-3 col-xs-12">Clear</button>
            <button type="button" class="btn btn-secondary btn-submit col-sm-3 col-xs-12">
                Submit
                <span class="processing spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
            </button>
        </div>
    </form>
</div>

<div class="_a-row col-sm-10 col-xs-12 message">
</div>
