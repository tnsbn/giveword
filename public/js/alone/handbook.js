/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!****************************************!*\
  !*** ./resources/js/alone/handbook.js ***!
  \****************************************/
;
(function ($, window, document) {
  "use strict";

  /**
   * Name of this component
   * @type {string}
   */
  var componentName = "handbook";

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
     * Shared variables
     */

    /**
     * Initialize method
     * Called on component start
     */
    initialize: function initialize() {
      this.listenMessage(this.element);
    },
    listenMessage: function listenMessage(el) {
      var local = this;
      $(document).ready(function () {
        // Get child elements
        local.modalEdit = $(local.element).find('#modal-edit');
        local.modalDelete = $(local.element).find('#modal-delete');

        // Listen on menu items of each word
        $(el).on('click', '.edit-message .list-group-item', function (event) {
          event.preventDefault();
          var actionItem = $(this);
          var action = actionItem.data('edit') == undefined || actionItem.data('edit') == "" ? "" : "edit";
          action = actionItem.data('delete') == undefined || actionItem.data('delete') == "" ? action : "delete";
          if (action == "" || ("tem" + actionItem.data(action)).indexOf('none') > 0) {
            return null;
          }
          local.currentId = actionItem.data(action);
          action = "showModal" + action;
          if (typeof local[action] !== 'undefined' && $.isFunction(local[action])) {
            local[action](local.currentId);
          }
        });
        local.modalEdit.on('click', '.close, .btn.cancel', function (event) {
          local.modalEdit.toggle('hide');
        });

        // Listen on button Ok Edit
        local.modalEdit.on('click', '.btn-ok', function (event) {
          event.preventDefault();
          var form = local.modalEdit.find('form');
          var word = {
            'id': form.find('#item-id').val(),
            'message': form.find('#message').val(),
            'tags': form.find('#tags').val(),
            'from': 'handbook'
          };
          $.ajax({
            type: "GET",
            url: '/ajax-update-handbook-item',
            data: word
          }).done(function (data) {
            if (data.html !== undefined) {
              $(local.element).find('.handbook-item.' + local.currentId).html($(data.html).html());
              $.fn.showNotify('success', 'The word has been updated.', 5000);
            } else {
              $.fn.showNotify('danger', data.error, 5000);
            }
          });
          local.modalEdit.toggle('hide');
        });
        local.modalDelete.on('click', '.close, .btn.cancel', function (event) {
          local.modalDelete.toggle('hide');
        });

        // Listen on button Ok Delete
        local.modalDelete.on('click', '.btn-ok', function (event) {
          local.modalDelete.toggle('hide');
          var data = {
            'id': local.currentId,
            '_token': csrfToken
          };
          $.ajax({
            type: "DELETE",
            url: '/ajax-delete-handbook-item/' + local.currentId,
            data: data
          }).done(function (data) {
            if (data.count !== undefined) {
              if (data.count == 1) {
                $.fn.showNotify('success', 'The word has been deleted.', 5000);
                $(local.element).find('.handbook-item.' + local.currentId).remove();
              } else {
                $.fn.showNotify('danger', data.error, 5000);
              }
            }
          });
        });
      });
    },
    // This is magic function. Search "showModal" to know where use this
    showModaledit: function showModaledit(id) {
      var local = this;
      $.ajax({
        type: "GET",
        url: '/ajax-get-handbook-item/' + id
      }).done(function (data) {
        if (data.html !== undefined) {
          local.modalEdit.find('.modal-body').html(data.html);
          local.modalEdit.toggle('show');
        }
      });
    },
    // This is magic function. Search "showModal" to know where use this
    showModaldelete: function showModaldelete(id) {
      var message = $(this.element).find('.handbook-item.' + id).find('.read-more-wrap').text();
      this.modalDelete.find('.message').text(message);
      this.modalDelete.toggle('show');
    }
  });

  /**
   * Preventing against multiple instantiations
   * @param options
   * @returns {*}
   */
  if ($.fn[componentName] === undefined) {
    $.fn[componentName] = function (options) {
      return new Component('.handbook-section', options);
    };

    // Init component
    new Component('.handbook-section');
  }
})(jQuery, window, document);
/******/ })()
;