'use strict';

Object.defineProperty(exports, '__esModule', {
  value: true
});
exports['default'] = configureStore;

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) arr2[i] = arr[i]; return arr2; } else { return Array.from(arr); } }

var _expect = require('expect');

var _expect2 = _interopRequireDefault(_expect);

var _redux = require('redux');

var isFunction = function isFunction(arg) {
  return typeof arg === 'function';
};

function configureStore() {
  var middlewares = arguments.length <= 0 || arguments[0] === undefined ? [] : arguments[0];

  return function mockStore(_getState, expectedActions, done) {
    if (!expectedActions) {
      throw new Error('expectedActions should be an expected action or an array of actions.');
    } else if (!Array.isArray(expectedActions)) {
      expectedActions = [expectedActions];
    } else {
      expectedActions = Array.prototype.slice.call(expectedActions);
    }

    if (typeof done !== 'undefined' && !isFunction(done)) {
      throw new Error('done should either be undefined or function.');
    }

    function mockStoreWithoutMiddleware() {
      var self = {
        getState: function getState() {
          return isFunction(_getState) ? _getState() : _getState;
        },

        dispatch: function dispatch(action) {
          if (isFunction(action)) {
            return action(self);
          }

          var expectedAction = expectedActions.shift();

          try {
            if (isFunction(expectedAction)) {
              expectedAction(action);
            } else {
              (0, _expect2['default'])(action).toEqual(expectedAction);
            }

            if (done && !expectedActions.length) {
              done();
            }

            return action;
          } catch (e) {
            if (done) {
              done(e);
            }
            throw e;
          }
        }
      };

      return self;
    }

    var mockStoreWithMiddleware = _redux.applyMiddleware.apply(undefined, _toConsumableArray(middlewares))(mockStoreWithoutMiddleware);

    return mockStoreWithMiddleware();
  };
}

module.exports = exports['default'];