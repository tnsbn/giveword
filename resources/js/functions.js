
if ($.fn['showNotify'] === undefined) {
  $.fn['showNotify'] = function (type, msg, milisec) {
    type = type === "" ? 'info' : type;
    msg = msg === "" ? 'No information' : msg;
    milisec = milisec === "" ? 500 : milisec;
    let htmlNotify =
      '<div class="modal" id="webapp-notify" tabindex="-1" role="dialog" data-backdrop="false">' +
      '  <div class="modal-dialog" role="document">' +
      '      <div class="modal-content">' +
      '          <div class="modal-header">' +
      '              <h2 class="modal-title text-' + type + '">' + msg + '</h2>' +
      '              <button type="button" class="btn-notify close" data-dismiss="modal" aria-label="Close">' +
      '                  <span aria-hidden="true">&times;</span>' +
      '              </button>' +
      '          </div>' +
      '      </div>' +
      '  </div>' +
      '</div>';

    if ($('body').find("#webapp-notify").length === 0) {
      $('body').append($(htmlNotify));
    }
    $('body').find("#webapp-notify").toggle('show');

    $('body').find("#webapp-notify").on('click', '.btn-notify.close', function (event) {
      $('body').find("#webapp-notify").toggle('hide');
    });

    setTimeout(function () {
      $('body').find("#webapp-notify").toggle('hide');
    }, milisec);
  };
}
