App.controller("indexController", function ($scope, $state, USER_ROLES, AUTH_EVENTS, AuthService, REST, Session, $rootScope, AuthEvent) {
  $('#LOGIN_MODAL').modal({ show: false})

  $scope.User = {};
  $scope.data = { data: [], selected:{}, new:{} };
  $scope.objectName = "";
  $scope.errorColumns = [];
  $scope.extraButtons = [];
  $scope.shoppingCartCounter = 0;
  $scope.credentials = {};
  $scope.Columns = {};
  $scope.Nav = {};
  $scope.RightNav = {};
  $scope.errorClasses = {};

  $scope.pageSize = 5;
  $scope.currentPage = 1;
  $scope.user = {};

  $scope.SignUpFields =
  [
    { title: "Käyttäjänimi", id: "username"  },
    { title: "Etunimi",      id: "firstName"  },
    { title: "Sukunimi",     id: "lastName"  },
    { title: "Katuosoite",   id: "address"  },
    { title: "Postinumero",  id: "postcode"  },
    { title: "Kaupunki",     id: "city"  },
    { title: "Maa",          id: "country"  },
    { title: "Sähköposti",   id: "email"  }
  ];
  var AuthError = function()
  {
    toggleLogin(true);
    $scope.updateUser();
  }
  var AuthSuccess = function()
  {
    toggleLogin(false);
    $scope.updateUser();
  }
  AuthEvent.LISEN_ERROR(AuthError);
  AuthEvent.LISEN_SUCCESS(AuthSuccess);

  $scope.format = function(f, val)
  {
    return FORMAT(f, val);
  }
  $scope.isset = function(o){return isset(o);}
  $scope.INIT = function(obName, update, extraButtons, columns)
  {
    $scope.objectName = (isset(obName)) ? obName : "";
    $scope.errorColumns = [];
    $scope.data = { data: [], selected:{}, new:{} };
    $scope.UPDATE = (isset(update)) ? update : function() {};
    $scope.extraButtons = (isset(extraButtons)) ? extraButtons : [];
    $scope.Columns = columns;
    $scope.UPDATE();
  }

  $scope.setData = function(d, s, n)
  {
    $scope.data = { data: [], selected:{}, new:{} };
    if(isset(d)) $scope.data.data     = d;
    if(isset(s)) $scope.data.selected = s;
    if(isset(n)) $scope.data.new      = n;
  }

  $scope.showLogin = function()
  {
    toggleLogin(true);
  }

  $scope.hasSubMenu = function(nav)
  {
    return (isset(nav) && isset(nav.subMenu));
  }

  $scope.EmptyDate = function (date) { return emptyDate(date); };

  $scope.updateShoppingCartCounter = function()
  {
    REST.shoppingCart("GET", "COUNT").then(
      function (data) //SUCCESS
      {
        $scope.shoppingCartCounter = data;
      }, function (errorColumns)//FAILURE
      {}
    );
  }

  $scope.clearError = function(id)
  {
    toggleError(id, false);
  }

  $scope.clearErrorClasses = function()
  {
    $scope.errorClasses = {};
  }
  $scope.clearErrorClass = function(name)
  {
    $scope.errorClasses[name] = false;
  }

  $scope.setErrorClasses = function(ids)
  {
    angular.forEach(ids,
      function(value, key)
      {
        $scope.errorClasses[value] = true;
      }
    );
  }

  $scope.updateUser = function()
  {
    $scope.User = isset(Session.user) ? Session.user : {};
    var group = isset($scope.User.userGroup) ? $scope.User.userGroup : USER_ROLES.guest;
    $scope.Nav = {};
    $scope.RightNav = {};
    $scope.Nav.Store = { title:"Kauppa", link:"store" };
    $scope.RightNav.Cart = { title:"Ostoskori", link:"cart" };
    if (isset(group) && group && group != USER_ROLES.guest)
    {
      $scope.RightNav.Login = { title:"Kirjaudu ulos", click: $scope.LOGOUT };
      $scope.Nav.User = { title:"Oma Sivu", link:"user" };
      if(group == "admin" || group == "manager" || group == "worker")
      {
          $scope.Nav.System = { title:"Hallinta", link:"" };
          $scope.Nav.System.subMenu = [
            { title:"Tuotteet", link:"systemProducts" },
            { title:"Tuotetyypit", link:"systemProductTypes" },
            { title:"Toimitus", link:"systemShipping" },
            { title:"Tilaukset", link:"systemOrders" }
          ];
          if(group == "admin") $scope.Nav.System.subMenu.push({ title:"Käyttäjät", link:"systemUsers" });
      }
    }
    else
    {
      $scope.RightNav.Login = { title:"Kirjaudu", click:$scope.showLogin };
    }
  };

  $scope.clearLogin = function ()
  {
    $scope.credentials = {};
    $scope.user = {};
  }

  $scope.SIGNUP = function(user)
  {
    $scope.clearErrorClasses();
    REST.SIGNUP(user).then(
      function (email)
      {
        toggleLogin(false);
        alert("Salasana lähetetty osoitteeseen "+email );
        $scope.clearLogin();
      }, function (errorColumns)
      {
        $scope.setErrorClasses(errorColumns);
      }
    );
  }

  $scope.LOGIN = function(credentials)
  {
    $scope.clearErrorClasses();
    AuthService.LOGIN(credentials).then(
      function (user)
      {
        $scope.updateUser();
        toggleLogin(false);
        $state.go(STATE);
        $scope.clearLogin();
      }, function (errorColumns)
      {
        $scope.updateUser();
        $scope.setErrorClasses(errorColumns);
      }
    );
  }

  $scope.LOGOUT = function()
  {
    AuthService.LOGOUT().then(
      function (data) //SUCCESS
      {
        $scope.updateUser();
        $state.go("store");
      }, function (errorColumns)//FAILURE
      {}
    );
  }

  $scope.REMOVE = function(object)
  {
    var promiseOfREMOVE = REST.REMOVE($scope.objectName, object.id);

    promiseOfREMOVE.then(
      function (data) //SUCCESS
      {
        $scope.UPDATE();
      }, function (errorColumns)//FAILURE
      {}
    );
  }

  $scope.REMOVEProduct = function(productID)
  {
    var promiseOfremoveProduct = REST.removeProduct($scope.data.show.id, productID);

    promiseOfremoveProduct.then(
      function (data) //SUCCESS
      {
        $scope.UPDATE();
        $scope.data.show = data;
        REST.getOrderProducts($scope.data.show.id).then(
          function (data) //SUCCESS
          {
            $scope.data.show.products = data;
          }, function (errorColumns)//FAILURE
          {}
        );
      }, function (errorColumns)//FAILURE
      {}
    );
  }

  $scope.EDIT = function(object)
  {
    $scope.clearErrorClasses();
    $scope.data.selected = angular.copy(object);
  }

  $scope.SAVE = function(object, idStart)
  {
    $scope.clearErrorClasses();
    var promiseOfEDIT = REST.EDIT($scope.objectName, object);
    var start = isset(idStart) ? idStart : "";
    promiseOfEDIT.then(
      function (data) //SUCCESS
      {
        if(object === $scope.data.new ) $scope.data.new = {};
        $scope.UPDATE();
      }, function (errorColumns)//FAILURE
      {
        var ids = [];
        angular.forEach(errorColumns,
          function(value, key)
          {
            ids.push(start+value);
          }
        );
        $scope.setErrorClasses(ids);
      }
    );
  }

  $scope.TEMPLATE = function (object) {
    if(isEmpty(object)) return 'new';
    else if (object.id === $scope.data.selected.id) return 'edit';
    else return 'display';
   };

   $scope.RESET = function () {
     $scope.clearErrorClasses();
    $scope.data.selected = {};
    };

    $scope.clearErrorClasses();
    $scope.clearLogin();
    $scope.updateShoppingCartCounter();
    $scope.updateUser();
});
