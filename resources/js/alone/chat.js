import {toNumber} from "lodash/lang.js";
import {padStart} from "lodash/string.js";

;(function ($, window, document) {
  "use strict";

  /**
   * Name of this component
   * @type {string}
   */
  let componentName = "chat";

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
    this.modalChat = null;
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
      this.listenBubbleModal(this.element);
      this.listenChatCenter(this.element);
    },

    listenBubbleModal: function (el) {
      let local = this;
      $(document).ready(function () {
        local.modalChat = $(local.element).find('#modal-chat');
        local.modalBody = local.modalChat.find('.modal-body');
        local.itvListener = null;

        $(el).on('click', '.btn-chat', function (event) {
          event.preventDefault();
          local.showModalChat('');
          const currentStatus = $(this).data('status');
          if (currentStatus !== '') {
            $.ajax({
              type: "GET",
              url: '/ajax-show-modal-chat'
            })
            .done(function (data) {
              if (data.status !== undefined && data.status === 'start') {
                local.itvListener = setInterval(listener, 1000);
              }
            });
          }
        });

        $(el).on('click', '.close', function (event) {
          local.modalChat.toggle('hide')
        });

        local.modalChat.on('click', '.load-name', function (event) {
          $.ajax({
            type: "GET",
            url: '/chat/load-name',
          })
          .done(function(data) {
            if (data.name !== undefined) {
              let title = local.modalChat.find('.modal-title');
              title.text(title.text() + ' ' + data['name']);
              local.modalChat.find('.load-name').remove();
            } else {
              local.modalChat.find('.load-name').text('try again');
            }
          });

        });

        function listener() {
          const data = { '_token': csrfToken };
          $.ajax({
            type: "POST",
            url: '/chat/receive',
            data: data,
          })
          .done(function(data) {
            if (data.messages !== undefined) {
              let sortKeys = Object.keys(data['messages']);
              sortKeys.sort();
              sortKeys.map((key) => {
                let time = (new Date(toNumber(key) * 1000));
                time = padStart(time.getHours().toString(), 2, '0') + ':' +
                  padStart(time.getMinutes().toString(), 2, '0') + ':' +
                  padStart(time.getSeconds().toString(), 2, '0');
                local.modalBody.append('<div class="their a-speak">' + data['messages'][key] + ' <span class="time">' + time + '</span></div>');
                local.modalBody.scrollTop(local.modalBody[0].getBoundingClientRect().bottom + 100);
              });
            }
          });
        }
      });
    },

    listenChatCenter: function (el) {
      let local = this;
      $(document).ready(function () {
        local.chatSection = $(el).find('.chat-section');

        $(el).on('change', '#online-chat', function (event) {
          let chkbox = document.getElementById('online-chat')
          const data = { 'is_online': chkbox.checked, '_token': csrfToken };
          $.ajax({
            type: "POST",
            url: '/admax/set-online',
            data: data,
          })
          .done(function(data) {
            if (data.status !== undefined && data.status === 'error') {
              $.fn.showNotify('danger', data['error-msg'], 5000);
            } else {
              $.fn.showNotify('success', 'Status updated', 2000);
            }
          });
        });

        local.modalChat.on('click', '.user-item', function (event) {
          let queueId = $(this).data('queue-id')
          let receiverId = $(this).data('receiver-id')
          const data = { 'queue_id': queueId, 'receiver_id': receiverId, '_token': csrfToken };
          $.ajax({
            type: "POST",
            url: '/chat/change-user',
            data: data,
          })
          .done(function(data) {
            let chatBox = $(local.element).find('.chat-box');
            if (data.html !== undefined) {
              let time = new Date();
              time = padStart(time.getHours().toString(), 2, '0') + ':' +
                padStart(time.getMinutes().toString(), 2, '0') + ':' +
                padStart(time.getSeconds().toString(), 2, '0');
              chatBox.html(data.html);
              chatBox.scrollTop(chatBox[0].getBoundingClientRect().bottom + 100);
              chatBox.find('#message').val('');
              chatBox.find('#message').select();
            } else if (data.status !== undefined && data.status === 'error') {
              $.fn.showNotify('danger', data['error-msg'], 5000);
            }
          });
        })

        local.modalChat.on('keypress', '#message', function (event) {
          if (event.keyCode === 13) {
            event.preventDefault();
            $('.btn-send').trigger('click');
          }
        });

        local.modalChat.on('click', '.btn-send', function (event) {
          let msg = local.modalChat.find('#message').val();
          if (msg.trim() === '') {
            return '';
          }
          const data = { 'message': msg, '_token': csrfToken };
          $.ajax({
            type: "POST",
            url: '/chat/send',
            data: data,
          })
          .done(function(data) {
            if (data.status !== undefined && data.status === 'sent') {
              let time = new Date(parseInt(data.time) * 1000);
              time = padStart(time.getHours().toString(), 2, '0') + ':' +
                padStart(time.getMinutes().toString(), 2, '0') + ':' +
                padStart(time.getSeconds().toString(), 2, '0');
              local.modalBody.append('<div class="mine a-speak">' + msg + ' <span class="time">' + time + '</span></div>');
              local.modalBody.scrollTop(local.modalBody[0].getBoundingClientRect().bottom + 100);
              local.modalChat.find('#message').val('')
              local.modalChat.find('#message').select()
            } else if (data.status !== undefined && data.status === 'error') {
              $.fn.showNotify('danger', data['error-msg'], 5000);
            }
          });
        })
      });
    },

    showModalChat: function (id) {
      // this.modalChat.find('.message').text(message);
      this.modalChat.toggle('show');
    },
  });

  /**
   * Preventing against multiple instantiations
   * @param options
   * @returns {*}
   */
  if ($.fn[componentName] === undefined) {
    $.fn[componentName] = function (options) {
      return new Component('.chat-section', options);
    };

    // Init component
    new Component('.chat-section');
  }

})(jQuery, window, document);
