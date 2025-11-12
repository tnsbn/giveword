<form class="form-horizontal">
  {{ csrf_field() }}
  <input type="hidden" id="item-id" value="{{ old('id', $data['id'] ?? "") }}">

  <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
    <label for="message" class="col-md-4 control-label">(<label class="text-danger">*</label>) Message</label>
    <div class="col-md-6">
      <textarea id="message" type="text" class="form-control" name="message" rows="5" required>{{ old('message', $data['message'] ?? "") }}</textarea>
      @if ($errors->has('message'))
        <span class="help-block">
                  <strong>{{ $errors->first('message') }}</strong>
              </span>
      @endif
    </div>
  </div>

  <div class="form-group">
    <label for="tags" class="col-md-4 control-label">Tags</label>
    <div class="col-md-6">
      <input id="tags" type="text" class="form-control" name="tags" value="{{ old('tags', $data['tags'] ?? "") }}" placeholder="Separated by comma">
    </div>
  </div>
</form>
