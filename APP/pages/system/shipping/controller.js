App.controller("systemShippingController", function ($scope, $http, REST) {
  $scope.Title = "Hallinta: Toimitus";

  var columns = {
    "Nimi":   new Column("title", "text"),
    "Hinta":  new Column("price", "euro", "euro")
  };
  var cols = new Columns(columns);

  var UPDATE = function()
  {
    REST.GET($scope.objectName).then(
      function (data) //SUCCESS
      {
        $scope.setData(data, {}, null, null);
      }, function (errorColumns)//FAILURE
      {}
    );
  }

  $scope.INIT("Shipping", UPDATE,  null, cols );
});
