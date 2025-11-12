
;(function ($, window, document) {
  "use strict";

  /**
   * Name of this component
   * @type {string}
   */
  let componentName = "search";

  /**
   * Default options
   * @type {{onInit: onInit}}
   */
  let defaults = {
    onInit: function () {}
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
    initialize: function () {
      this.listenMessage(this.element);
    },
    listenMessage: function (el) {
      let local = this;
      $(document).ready(function () {
        // Get child elements
        local.modalEdit = $(local.element).find('#modal-edit');
        local.modalDelete = $(local.element).find('#modal-delete');

        // Listen on menu items of each word
        $(el).on('click', '.edit-message .list-group-item', function (event) {
          event.preventDefault();
          let actionItem = $(this);
          let action = (actionItem.data('edit') == undefined || actionItem.data('edit') == "") ? "" : "edit";
          action = (actionItem.data('delete') == undefined || actionItem.data('delete') == "") ? action : "delete";
          if (action == "" || ("tem" + actionItem.data(action)).indexOf('none') > 0) {
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
          let form = local.modalEdit.find('form');
          let word = {
            'id': form.find('#item-id').val(),
            'type': form.find('#type').val(),
            'to': form.find('#to').val(),
            'message': form.find('#message').val(),
            'tags': form.find('#tags').val(),
            'from': 'search'
          };

          $.ajax({
            type: "GET",
            url: '/ajax-update-search-item',
            data: word
          })
          .done(function(data) {
            if (data.html !== undefined) {
              $(local.element).find('.search-item.' + local.currentId).html($(data.html).html());
              $.fn.showNotify('success', 'The post has been updated.', 2000);
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
            url: '/ajax-delete-search-item/'+ local.currentId
          })
          .done(function(data) {
            if (data.count !== undefined) {
              if (data.count === 1) {
                $.fn.showNotify('success', '1 message has been deleted.', 2000);
                $(local.element).find('.search-item.' + local.currentId).remove();
              } else {
                $.fn.showNotify('danger', 'Sorry. Have something wrong.', 2000);
              }
            }
          });
        })
      });
    },

    showModaledit: function (id) {
      let local = this;
      $.ajax({
        type: "GET",
        url: '/ajax-get-search-item/'+ id
      })
      .done(function(data) {
        if (data.html !== undefined) {
          local.modalEdit.find('.modal-body').html(data.html);
          local.modalEdit.modal('show');
        }
      });
    },

    showModaldelete: function (id) {
      let messageTo = $(this.element).find('.search-item.' + id).find('.to .text').text();
      this.modalDelete.find('.message-to').text(messageTo);
      this.modalDelete.modal('show');
    },
  });

  /**
   * Preventing against multiple instantiations
   * @param options
   * @returns {*}
   */
  if ($.fn[componentName] === undefined) {
    $.fn[componentName] = function (options) {
      return new Component('.search-section', options);
    };

    // Init component
    new Component('.search-section');
  }

})(jQuery, window, document);
