/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!***************************************!*\
  !*** ./resources/js/alone/profile.js ***!
  \***************************************/
;
(function ($, window, document) {
  "use strict";

  /**
   * Name of this component
   * @type {string}
   */
  var componentName = "profile";

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
    this.formProfile = null;
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
      this.listenProfile(this.element);
    },
    listenProfile: function listenProfile(el) {
      var local = this;
      var focusoutTime = 0;
      $(document).ready(function () {
        // Get child elements
        local.formProfile = $(local.element).find('.form-profile');
        local.formPassword = $(local.element).find('#form-password');
        local.btnLinkPassword = $(local.element).find('.btn-link');

        // Utility functions
        var clearFormPassword = function clearFormPassword() {
          local.formPassword.find('.help-block').each(function () {
            $(this).remove();
          });
          local.formPassword.find('.has-error').each(function () {
            $(this).removeClass('has-error');
          });
          local.formPassword.find('input[required]').each(function () {
            $(this).val('');
          });
          local.formPassword.find('.panel-body').remove();
        };

        // Listen edit small info buttons
        local.formProfile.on('click', '.btn-edit', function (event) {
          // Only allow click event after focusout event happen in 1000 milliseconds
          // To fix the concurrency of "control focusout" and "button click"
          if (new Date().getTime() - focusoutTime < 1000) {
            return;
          }
          local.formProfile.find('.panel-success').remove();
          var controlId = $(this).data('edit');
          var control = local.formProfile.find('#' + controlId);
          if ($(this).text() === 'Edit') {
            control.removeAttr('disabled').attr('enabled', true);
            $(this).text('Close');
            local.formProfile.find('.btn-save-info[for="' + controlId + '"]').closest('.form-group').removeClass('hidden');
          }
        });

        // Listen focusout of enabled controls on profile
        local.formProfile.on('focusout', '.form-control[enabled]', function (event) {
          focusoutTime = new Date().getTime();
          var group = $(this).closest('.form-group');
          local.checkRequire($(this));
          $(this).removeAttr('enabled').attr('disabled', true);
          group.find('.btn-edit').text('Edit');
          if ($(this).val() === $(this).data('original')) {
            local.formProfile.find('.btn-save-info[for="' + this.id + '"]').closest('.form-group').addClass('hidden');
          }
        });
        local.formProfile.on('click', '.btn.cancel', function (event) {
          clearFormPassword();
          local.formPassword.collapse('hide');
        });

        // Listen save small info buttons
        local.formProfile.on('click', '.btn-save-info', function (event) {
          var controlId = $(this).attr('for');
          var control = local.formProfile.find('#' + controlId);
          if (local.checkRequire(control)) {
            var data = {
              'name': local.formProfile.find('#name').val()
            };
            $.ajax({
              type: "GET",
              url: '/profile/change-name',
              data: data
            }).done(function (response) {
              if (response.redirect !== undefined && response.redirect_url !== undefined) {
                window.location.href = response.redirect_url;
              }
            });
          }
        });

        // Listen change password event
        local.formPassword.on('show.bs.collapse', function (event) {
          local.formProfile.find('.panel-success').remove();
        });
        local.formPassword.on('hide.bs.collapse', function (event) {
          clearFormPassword();
          local.btnLinkPassword.text('Change password');
        });

        // Check success/error of form password
        if (local.formPassword.find('.panel-body .alert').length >= 1) {
          local.formPassword.collapse('show');
        }

        // Listen button save profile form
        local.formProfile.find('.btn-save-profile').on('click', function (event) {
          if (local.validateProfile()) {
            var data = {
              'name': local.formProfile.find('#name').val()
            };
            $.ajax({
              type: "GET",
              url: '/profile/update',
              data: data
            }).done(function (response) {
              if (response.redirect !== undefined && response.redirect_url !== undefined) {
                window.location.href = response.redirect_url;
              }
            });
          }
        });
      });
    },
    checkRequire: function checkRequire(ele) {
      var valid = true;
      var group = $(ele).closest('.form-group');
      if ($(ele).val() === "") {
        valid = false;
        group.addClass('has-error');
        if (group.find('.help-block').length === 0) {
          $(ele).parent().append('<span class="help-block"></span>');
        }
        group.find('.help-block').text('Please fill out this field.').removeClass('hidden');
      } else {
        group.removeClass('has-error');
        group.find('.help-block').remove();
      }
      return valid;
    },
    validateProfile: function validateProfile() {
      var valid = true;

      // Check required
      this.formProfile.find('input:visible[required]').each(function () {
        if ($(this).val() === "") {
          valid = false;
          var group = $(this).closest('.form-group');
          group.addClass('has-error');
          if (group.find('.help-block').length === 0) {
            $(this).parent().append('<span class="help-block"></span>');
          }
          group.find('.help-block').text('Please fill out this field.').removeClass('hidden');
        }
      });
      return valid;
    }
  });

  /**
   * Preventing against multiple instantiations
   * @param options
   * @returns {*}
   */
  if ($.fn[componentName] === undefined) {
    $.fn[componentName] = function (options) {
      return new Component('.profile-section', options);
    };

    // Init component
    new Component('.profile-section');
  }
})(jQuery, window, document);
/******/ })()
;