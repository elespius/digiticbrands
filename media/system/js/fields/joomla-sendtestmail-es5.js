(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

var _typeof2 = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _typeof = 'function' == typeof Symbol && 'symbol' == _typeof2(Symbol.iterator) ? function (a) {
  return typeof a === 'undefined' ? 'undefined' : _typeof2(a);
} : function (a) {
  return a && 'function' == typeof Symbol && a.constructor === Symbol && a !== Symbol.prototype ? 'symbol' : typeof a === 'undefined' ? 'undefined' : _typeof2(a);
},
    _createClass = function () {
  function a(a, b) {
    for (var c, d = 0; d < b.length; d++) {
      c = b[d], c.enumerable = c.enumerable || !1, c.configurable = !0, 'value' in c && (c.writable = !0), Object.defineProperty(a, c.key, c);
    }
  }return function (b, c, d) {
    return c && a(b.prototype, c), d && a(b, d), b;
  };
}();function _classCallCheck(a, b) {
  if (!(a instanceof b)) throw new TypeError('Cannot call a class as a function');
}function _possibleConstructorReturn(a, b) {
  if (!a) throw new ReferenceError('this hasn\'t been initialised - super() hasn\'t been called');return b && ('object' == (typeof b === 'undefined' ? 'undefined' : _typeof2(b)) || 'function' == typeof b) ? b : a;
}function _inherits(a, b) {
  if ('function' != typeof b && null !== b) throw new TypeError('Super expression must either be null or a function, not ' + (typeof b === 'undefined' ? 'undefined' : _typeof2(b)));a.prototype = Object.create(b && b.prototype, { constructor: { value: a, enumerable: !1, writable: !0, configurable: !0 } }), b && (Object.setPrototypeOf ? Object.setPrototypeOf(a, b) : a.__proto__ = b);
}var JoomlaFieldSendTestMail = function (a) {
  function b() {
    return _classCallCheck(this, b), _possibleConstructorReturn(this, (b.__proto__ || Object.getPrototypeOf(b)).apply(this, arguments));
  }return _inherits(b, a), _createClass(b, [{ key: 'connectedCallback', value: function connectedCallback() {
      var a = this,
          b = document.getElementById('sendtestmail');b && b.addEventListener('click', function () {
        a.sendTestMail(a);
      });
    } }, { key: 'sendTestMail', value: function sendTestMail() {
      var a = { smtpauth: this.querySelector('[name="jform[smtpauth]"]').value, smtpuser: this.querySelector('[name="jform[smtpuser]"]').value, smtppass: this.querySelector('[name="jform[smtppass]"]').value, smtphost: this.querySelector('[name="jform[smtphost]"]').value, smtpsecure: this.querySelector('[name="jform[smtpsecure]"]').value, smtpport: this.querySelector('[name="jform[smtpport]"]').value, mailfrom: this.querySelector('[name="jform[mailfrom]"]').value, fromname: this.querySelector('[name="jform[fromname]"]').value, mailer: this.querySelector('[name="jform[mailer]"]').value, mailonline: this.querySelector('[name="jform[mailonline]"]').value };Joomla.removeMessages(), Joomla.request({ url: this.getAttribute('url'), method: 'POST', data: JSON.stringify(a), perform: !0, headers: { "Content-Type": 'application/x-www-form-urlencoded' }, onSuccess: function onSuccess(a) {
          a = JSON.parse(a), 'object' === _typeof(a.messages) && null !== a.messages && Joomla.renderMessages(a.messages);
        }, onError: function onError(a) {
          Joomla.renderMessages(Joomla.ajaxErrorsMessages(a));
        } });
    } }]), b;
}(HTMLElement);customElements.define('joomla-field-sendtestmail', JoomlaFieldSendTestMail);

},{}]},{},[1]);
