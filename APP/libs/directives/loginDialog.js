App.directive('loginDialog', function (AUTH_EVENTS) {
  return {
    restrict: 'A',
    template: '<div ng-if="visible" ng-include="\'pages/login/page.html\'">',
    link: function (scope) {
      var showDialog = function () {
        toggleLogin(false);
      };
      var hideDialog = function () {
        toggleLogin(false);
      };

      scope.$on(AUTH_EVENTS.notAuthenticated, showDialog);
      scope.$on(AUTH_EVENTS.sessionTimeout, showDialog);
      scope.$on(AUTH_EVENTS.loginSuccess, hideDialog);
      scope.$on(AUTH_EVENTS.authenticated, hideDialog);
    }
  };
})
