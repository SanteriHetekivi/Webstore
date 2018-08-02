App.run(function ($rootScope,$state, AUTH_EVENTS, AuthService, USER_ROLES, AuthEvent) {
  var counter = 0;
  var last = "";
  $rootScope.$on('$stateChangeStart', function (event, next) {
    STATE = next;
    var page = next.name;

    var authorizedRoles = next.data.authorizedRoles;

    if(counter > 1)
    {
      $rootScope.$broadcast(AUTH_EVENTS.notAuthenticated);
      last = "";
      counter = 0;
      return;
    }
    else if(!AuthService.CHECK(page, authorizedRoles))
    {
      if(page == last) ++counter;
      else
      {
        last = page;
        counter = 0;
      }

      event.preventDefault();

      AuthService.CHECK_SERVER(page).then(
        function(p) //SUCCESS
        {
          if(page == p) $state.go(STATE);
          else AuthEvent.SEND_ERROR();
          return;
        }, function(error) //FAILURE
        {
          AuthEvent.SEND_ERROR();
          return;
        }
      );
    }
    else
    {
      AuthEvent.SEND_SUCCESS();
      last = "";
      counter = 0;
    }
  });
})

App.config(function ($httpProvider) {
  $httpProvider.interceptors.push([
    '$injector',
    function ($injector) {
      return $injector.get('AuthInterceptor');
    }
  ]);
})
