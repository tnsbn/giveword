/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*****************************************!*\
  !*** ./resources/js/alone/long-page.js ***!
  \*****************************************/
;
(function ($, window, document) {
  "use strict";

  /**
   * Name of this component
   * @type {string}
   */
  var componentName = "long-page";

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
      // this.listenNavbarMobile(this.element);
    }
    // listenNavbarMobile: function (el) {
    //   let navbar = $(el).find('#navbar-mobile');
    //   navbar.on('shown.bs.collapse', function () {
    //     navbar.attr("tabindex", -1).focus();
    //   });
    //   navbar.on('focusout', function (event) {
    //     if (event.relatedTarget === null || event.relatedTarget.closest('#navbar-mobile') === null || event.relatedTarget.closest('#navbar-mobile').length > 0) {
    //       // navbar.toggle('hide');
    //       navbar.collapse('hide');
    //     }
    //   });
    // },
  });

  /**
   * Preventing against multiple instantiations
   * @param options
   * @returns {*}
   */
  $(document).ready(function () {
    if ($.fn[componentName] === undefined) {
      $.fn[componentName] = function (options) {
        return new Component('.long-page', options);
      };

      // Init component
      new Component('.long-page');
    }
  });
})(jQuery, window, document);
/******/ })()
;