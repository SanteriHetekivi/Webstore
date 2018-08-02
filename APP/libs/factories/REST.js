App.factory("REST", function ($http, $q, $localStorage, $window) {
    var factoryObject = {};
    var $storage = $localStorage;
    var url = "../REST/index.php/"

    var CHECK_RESP = function(resp)
    {
      var sys = (isset(resp.system)) ? resp.system : {};
      var error = (isset(sys.error)) ? sys.error : {};

      var data = (isset(resp.data)) ? resp.data : {};
      var result = (isset(sys.result) && sys.result == true) ? true : false;
      var errorColumns = (isset(error.columns)) ? error.columns : [];

      return { data: data, result: result, errorColumns: errorColumns };
    }

    var RUN = function(PoG, func, ob, id, object)
    {
      var deferred = $q.defer();
      var func = func;
      var ob = (isset(ob)) ? "/"+ob : "";
      var id = (isset(id)) ? "/"+id : "";
      var object = (isset(object)) ? object : {};
      var callUrl = url+func+ob+id;

      if(PoG == "POST")
      {
        $http.post(callUrl, object).success (function(resp)
        {
          var r = CHECK_RESP(resp);

          if(isset(r.result) && r.result) deferred.resolve(r.data);
          else  deferred.reject(r.errorColumns);
        });
      }
      else {
        $http.get(callUrl, object).success (function(resp)
        {
          var r = CHECK_RESP(resp);

          if(isset(r.result) && r.result) deferred.resolve(r.data);
          else  deferred.reject(r.errorColumns);
        });
      }

      return deferred.promise;
    }

    //AUTH
    factoryObject.CHECKLOGIN = function ()
    {
      var func = "CHECKLOGIN";
      return RUN("POST", func, null, null, null);
    };

    factoryObject.CHECKAUTH = function (page)
    {
      var func = "CHECKAUTH";
      return RUN("POST", func, page, null, null);
    };

    factoryObject.SIGNUP = function (user)
    {
      var func = "SIGNUP";
      return RUN("POST", func, null, null, {User: user});
    };

    factoryObject.LOGIN = function (credentials)
    {
      var func = "LOGIN";
      var object = {
        "username": credentials.username,
        "password": credentials.password
      }
      return RUN("POST", func, null, null, object);
    };

    factoryObject.LOGOUT = function () {
        var func = "LOGOUT";
        return RUN("POST", func, null, null, null);
    };

    //GET
    factoryObject.GET = function (objectName, id, noKeys)
    {
        var func = "GET";
        var noKeys = (isset(noKeys) && !noKeys) ? false : true;
        var object = {noKeys: noKeys};
        return RUN("GET", func, objectName, id, object);
    };

    factoryObject.getColumn = function (objectName, column)
    {
        var func = "getColumn";
        return RUN("GET", func, objectName, column, null);
    };

    factoryObject.getUserOrders = function ()
    {
      var func = "getUserOrders";
      return RUN("GET", func, null, null, null);
    }

    factoryObject.getOrderProducts = function (id)
    {
      var func = "getOrderProducts";
      return RUN("GET", func, id, null, null);
    }

    factoryObject.getProductsByType = function (typeid)
    {
      var func = "getProductsByType";
      return RUN("GET", func, typeid, null, null);
    }

    factoryObject.getUserNames = function ()
    {
      var func = "getUserNames";
      return RUN("GET", func, null, null, null);
    }

    //EDIT
    factoryObject.EDIT = function (objectName, object) {
        var func = "EDIT";
        var data = {columns: object};
        return RUN("POST", func, objectName, object.id, data);
    };

    factoryObject.setShipped = function (orderId) {
        var func = "setShipped";
        return RUN("POST", func, orderId, null, null);
    };

    //REMOVE
    factoryObject.REMOVE = function (objectName, id) {
        var func = "REMOVE";
        return RUN("POST", func, objectName, id, null);
    };

    factoryObject.removeProduct = function (orderId, productId) {
        var func = "removeProduct";
        return RUN("POST", func, orderId, productId, null);
    };

    //SPECIAL
    factoryObject.shoppingCart = function (action, id, object)
    {
      var func = "shoppingCart";
      var data = {"object": object}
      return RUN("POST", func, action, id, data);
    }



    return factoryObject;    // return factory object

});
