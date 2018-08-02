App.controller("systemOrdersController", function ($scope, $http, REST) {
  $scope.Title = "Hallinta: Tilaukset";

  $scope.types = [];
  $scope.users = [];

  var columns = {
    "Tilattu":    new Column("created", "read", "dateTime"),
    "Asiakas":    new Column("user_id", "select", null, $scope.users),
    "Toimitettu": new Column("shipped", "datetime-local", "dateTime", null, {cal: "()", pair: "shipping", format: "euro"}),
    "Hinta":      new Column("price", "euro", "euro"),
    "Yhteensä":   new Column("price", "read", "euro", null, {cal: "+", pair: "shipping"})
  };

  //$scope.columns.Tilattu.click = $scope.lol;
  var cols = new Columns(columns, false);

  var setExtra = function (extra){
    $scope.users = extra;
    $scope.Columns.columns.Asiakas.data = extra;
  };

  var UPDATE = function()
  {
    REST.GET($scope.objectName).then(
      function (data) //SUCCESS
      {
        $scope.setData(data, {}, null, null);
      }, function (errorColumns)//FAILURE
      {}
    );
    REST.getUserNames().then(
      function (data) //SUCCESS
      {
        setExtra(data);
      }, function (errorColumns)//FAILURE
      {}
    );
  }

  $scope.SHIPPED = function(object)
  {
    var promiseOfsetShipped = REST.setShipped(object.id);

    promiseOfsetShipped.then(
      function (data) //SUCCESS
      {
        $scope.UPDATE();
      }, function (errorColumns)//FAILURE
      {}
    );
  }

  $scope.SHOW = function(object)
  {
    $scope.data.show = angular.copy(object);
    REST.getOrderProducts($scope.data.show.id).then(
      function (data) //SUCCESS
      {
        $scope.data.show.products = data;
        $("#orderProducts").modal();
      }, function (errorColumns)//FAILURE
      {}
    );
  }

  var extraButtons = [
    {title: "TUOTTEET", click: $scope.SHOW},
    {title: "LÄHETETTY", click: $scope.SHIPPED, test:{func: $scope.EmptyDate, val:"shipped"}},
  ];
  $scope.INIT("Order", UPDATE, extraButtons, cols );

});
