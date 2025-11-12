/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!****************************************!*\
  !*** ./resources/js/alone/takeword.js ***!
  \****************************************/
;
(function ($, window, document) {
  "use strict";

  /**
   * Name of this component
   * @type {string}
   */
  var componentName = "takeword";

  /**
   * Default options
   * @type {{onInit: onInit}}
   */
  var defaults = {
    onInit: function onInit() {}
  };

  /**
   * Component constructor
   * @param element
   * @param options
   * @constructor
   */
  function Component(element, options) {
    this.element = element;
    this.settings = $.extend({}, defaults, options);
    this._name = componentName;
    this.currentId = 0;
    this.modalDelete = null;
    this.initialize();
  }

  /**
   * Avoid Component.prototype conflicts
   * @extends Component.prototype
   */
  $.extend(Component.prototype, {
    /**
     * Initialize method
     * Called on component start
     */
    initialize: function initialize() {
      this.listenMoreTakeword(this.element);
      this.listenMessageMenu(this.element);
    },
    listenMoreTakeword: function listenMoreTakeword(el) {
      $(document).ready(function () {
        $(el).find('.btn-more-takeword').on('click', function (event) {
          event.preventDefault();
          var btnMore = $(this);
          var nextPage = btnMore.data('nextPage') === undefined ? 2 : btnMore.data('nextPage');
          btnMore.data('nextPage', 1 + btnMore.data('nextPage'));
          var keyword = $(el).find('#keyword').val();
          var actionStr = action !== '' ? '&action=' + action : '';
          $.ajax({
            type: "GET",
            url: '/more-word?page=' + nextPage + '&keyword=' + keyword + actionStr
          }).done(function (data) {
            if (data.html !== undefined && data.html !== "") {
              $(data.html).insertBefore($('.btn-more-wrap'));
            }
            if (data.hasMore !== undefined && data.hasMore === true) {
              btnMore.data('nextPage', 1 + nextPage);
            } else {
              btnMore.hide();
            }
          });
        });
      });
    },
    listenMessageMenu: function listenMessageMenu(el) {
      var local = this;
      $(document).ready(function () {
        // Get child elements
        local.modalEdit = $(local.element).find('#modal-edit');
        local.modalDelete = $(local.element).find('#modal-delete');

        // Listen on menu items of each word
        $(el).on('click', '.edit-message .list-group-item', function (event) {
          event.preventDefault();
          var actionItem = $(this);
          var action = actionItem.data('edit') === undefined || actionItem.data('edit') === "" ? "" : "edit";
          action = actionItem.data('delete') === undefined || actionItem.data('delete') === "" ? action : "delete";
          if (action === "" || ("tem" + actionItem.data(action)).indexOf('none') > 0) {
            return null;
          }
          local.currentId = actionItem.data(action);
          action = "showModal" + action;
          if (typeof local[action] !== 'undefined' && $.isFunction(local[action])) {
            local[action](local.currentId);
          }
        });

        // Listen on button Ok Edit
        local.modalEdit.on('click', '.btn-ok', function (event) {
          event.preventDefault();
          var form = local.modalEdit.find('form');
          var word = {
            'id': form.find('#item-id').val(),
            'type': form.find('#type').val(),
            'to': form.find('#to').val(),
            'message': form.find('#message').val(),
            'tags': form.find('#tags').val(),
            'from': 'takeword'
          };
          $.ajax({
            type: "GET",
            url: '/ajax-update-word-item',
            data: word
          }).done(function (data) {
            if (data.html !== undefined && data.html !== "") {
              if (data.html === "<!-- Not takeword element -->") {
                $(local.element).find('.takeword-item.' + local.currentId).remove();
                $.fn.showNotify('success', 'The post has been removed from Take Words.', 2000);
              } else {
                $(local.element).find('.takeword-item.' + local.currentId).html($(data.html).html());
                $.fn.showNotify('success', 'The post has been updated.', 2000);
              }
            } else {
              $.fn.showNotify('danger', 'Sorry. Have something wrong.', 2000);
            }
          });
          local.modalEdit.modal('hide');
        });

        // Listen on button Ok Delete
        local.modalDelete.on('click', '.btn-ok', function (event) {
          local.modalDelete.modal('hide');
          $.ajax({
            type: "GET",
            url: '/ajax-delete-word-item/' + local.currentId
          }).done(function (data) {
            if (data.count !== undefined) {
              if (data.count == 1) {
                $.fn.showNotify('success', '1 message has been deleted.', 2000);
                $(local.element).find('.word.' + local.currentId).remove();
              } else {
                $.fn.showNotify('danger', 'Sorry. Have something wrong.', 2000);
              }
            }
          });
        });
      });
    },
    showModaledit: function showModaledit(id) {
      var local = this;
      $.ajax({
        type: "GET",
        url: '/ajax-get-word-item/' + id
      }).done(function (data) {
        if (data.html !== undefined) {
          local.modalEdit.find('.modal-body').html(data.html);
          local.modalEdit.modal('show');
        }
      });
    },
    showModaldelete: function showModaldelete(id) {
      var messageTo = $(this.element).find('.word.' + id).find('.to .text').text();
      this.modalDelete.find('.message-to').text(messageTo);
      this.modalDelete.modal('show');
    }
  });

  /**
   * Preventing against multiple instantiations
   * @param options
   * @returns {*}
   */
  if ($.fn[componentName] === undefined) {
    $.fn[componentName] = function (options) {
      return new Component('.takeword-section', options);
    };

    // Init component
    new Component('.takeword-section');
  }
})(jQuery, window, document);
/******/ })()
;