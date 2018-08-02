App.factory('AuthService', function ($q, $http, Session, REST)
{
  var authService = {};
  var checked = false;
  var guest = {
    id: 0,
    username: "Vieras",
    userGroup: "guest"
  }

  var tmpUserGroups = [];

  authService.LOGIN = function (credentials)
  {
    var def = $q.defer();

    REST.LOGIN(credentials).then(
      function(user) //SUCCESS
      {
        if(isset(user.id))
        {
          Session.create(user);
          def.resolve(user);
        }
        else {
          def.reject([]);
        }
      }, function(errorColumns) //FAILURE
      {
        var columns = (isset(errorColumns)) ? errorColumns : [];
        def.reject(columns);
      }
    );

    return def.promise;
  }

  authService.LOGOUT = function (credentials) {
    var promise = REST.LOGOUT();
    return promise.then(function (res) {
      if(res)
      {
        Session.destroy();
        checked = false;
      }
    });
  };

  authService.CHECK_SERVER = function(page)
  {
    var def = $q.defer();

    REST.CHECKAUTH(page).then(
      function(user) //SUCCESS
      {
        if(isset(user.id)) Session.create(user);
        else Session.create(guest);
        checked = page;
        def.resolve(page);
      }, function(error) //FAILURE
      {
        def.reject(false);
      }
    );
    return def.promise;

  }
  authService.CHECK = function(page, userGroups)
  {
    var success = false;
    if(isset(page) && isset(userGroups) && isset(Session) &&
      isset(Session.user) && isset(Session.user.userGroup) && isset(checked) &&
      checked && page == checked)
    {
      success = (userGroups.indexOf(Session.user.userGroup) !== -1);
    }

    checked = false;
    return success;
  }
  return authService;

});
