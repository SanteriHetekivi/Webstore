App.service("AuthEvent",function($rootScope) {
  var error = "AuthError";
  this.SEND_ERROR = function() {$rootScope.$emit(error);}
  this.LISEN_ERROR = function(func) {$rootScope.$on(error,func);}
  var success = "AuthSuccess";
  this.SEND_SUCCESS = function() {$rootScope.$emit(success);}
  this.LISEN_SUCCESS = function(func) {$rootScope.$on(success,func);}
})
