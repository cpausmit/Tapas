// Avoid `console` errors in browsers that lack a console.
(function() {
  var method;
  var noop = function () {};
  var methods = [
    'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
    'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
    'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
    'timeStamp', 'trace', 'warn'
  ];
  var length = methods.length;
  var console = (window.console = window.console || {});
  
  while(length--) {
    method = methods[length];
    // Only stub undefined methods.
    if(!console[method]) {
      console[method] = noop;
    }
  }
}());

// Sample plugin
/*
(function($) {
  $.fn.example = function(options) {

    var settings = $.extend({
      param1: "default",
      param2: 78
    }, options);

    var plugin_fn = function() {
      var self = this;
      // do some stuff on self
      return this;
    };
  
    return this.each(plugin_fn);
  };
}(jQuery));
*/
