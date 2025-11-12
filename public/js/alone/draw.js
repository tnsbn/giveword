/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!************************************!*\
  !*** ./resources/js/alone/draw.js ***!
  \************************************/
;
(function ($, window, document) {
  "use strict";

  /**
   * Name of this component
   * @type {string}
   */
  var componentName = "draw";

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
      this.listenEvents(this.element);
    },
    listenEvents: function listenEvents(el) {
      var local = this;
      $(document).ready(function () {
        var canvas = document.getElementById('canvas');
        var btnRandom = $(local.element).find('.btn-random');
        var btnUndo = $(local.element).find('.btn-undo');
        var btnClear = $(local.element).find('.btn-clear');
        var btnSubmit = $(local.element).find('.btn-submit');
        var ctx = canvas.getContext('2d');
        ctx.lineJoin = 'round';
        ctx.lineCap = 'round';
        ctx.strokeStyle = 'blue';
        var drawing = false;
        var drawnPaths = [];
        var points = [];
        var isSubmitted = false;
        var mouse = {
          x: 0,
          y: 0
        };
        var previous = {
          x: 0,
          y: 0
        };
        resize();
        window.addEventListener('resize', resize);
        canvas.addEventListener('mousedown', function (e) {
          if (isSubmitted) {
            clear();
            isSubmitted = false;
          }
          drawing = true;
          previous = {
            x: mouse.x,
            y: mouse.y
          };
          mouse = getMousePos(canvas, e);
          points = [];
          points.push({
            x: mouse.x,
            y: mouse.y
          });
        });
        canvas.addEventListener('mousemove', function (event) {
          if (drawing) {
            previous = {
              x: mouse.x,
              y: mouse.y
            };
            mouse = getMousePos(canvas, event);
            points.push({
              x: mouse.x,
              y: mouse.y
            });
            ctx.beginPath();
            ctx.moveTo(previous.x, previous.y);
            ctx.lineTo(mouse.x, mouse.y);
            ctx.stroke();
          }
        }, false);
        canvas.addEventListener('mouseup', function () {
          drawing = false;
          drawnPaths.push(points);
        }, false);
        var drawCollapse = document.getElementById('collapse-draw');
        drawCollapse.addEventListener('shown.bs.collapse', function () {
          resize();
        });
        btnRandom.on('click', randomWord);
        btnUndo.on('click', undo);
        btnClear.on('click', clear);
        btnSubmit.on('click', submitDraw);
        function drawPaths() {
          ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
          drawnPaths.forEach(function (path) {
            ctx.beginPath();
            ctx.moveTo(path[0].x, path[0].y);
            for (var i = 1; i < path.length; i++) {
              ctx.lineTo(path[i].x, path[i].y);
            }
            ctx.stroke();
          });
        }
        function undo() {
          drawnPaths.splice(-1, 1);
          drawPaths();
        }
        function clear() {
          ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
          drawnPaths = [];
          ctx.strokeStyle = 'blue';
        }
        function getMousePos(canvas, evt) {
          var ClientRect = canvas.getBoundingClientRect();
          return {
            x: Math.round(evt.clientX - ClientRect.left),
            y: Math.round(evt.clientY - ClientRect.top)
          };
        }
        function resize() {
          ctx.canvas.width = canvas.getBoundingClientRect().width;
          ctx.canvas.height = canvas.getBoundingClientRect().height;
          ctx.strokeStyle = 'blue';
          drawPaths();
        }
        function randomWord() {
          var data = {
            _token: csrf === undefined ? '' : csrf,
            salt: Date.now()
          };
          $.ajax({
            type: "POST",
            url: '/api/random-word',
            data: data
          }).done(function (response) {
            if (response.error !== undefined) {
              $.fn.showNotify('danger', response.error, 5000);
              $(local.element).find('.message').hide();
            } else {
              $(local.element).find('.message').text(response.msg);
              $(local.element).find('.message').show();
            }
          });
        }
        function submitDraw() {
          $('.btn-submit .processing').css('display', 'inline-block');
          var base64 = canvas.toDataURL("image/jpeg").split(';base64,')[1];
          var data = {
            _token: csrf === undefined ? '' : csrf,
            img: base64
          };
          $.ajax({
            type: "POST",
            url: '/api/word-by-draw',
            data: data
          }).done(function (response) {
            if (response.error !== undefined) {
              $(local.element).find('.message').text(response.error);
            } else {
              isSubmitted = true;
              $(local.element).find('.message').text(response.msg);
            }
            $(local.element).find('.message').show();
            $('.btn-submit .processing').css('display', 'none');
            // $('.btn-submit .processing').hide();
          }).fail(function () {
            $('.btn-submit .processing').css('display', 'none');
            // $('.btn-submit .processing').hide();
          });
        }
      });
    }
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
/******/ })()
;