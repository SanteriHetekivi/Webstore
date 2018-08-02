App.controller("systemProductsController", function ($scope, $http, REST) {
  $scope.Title = "Hallinta: Tuotteet";
  //$scope.data = {data: [], selected:{}, new:{}};
  $scope.types = [];

  var columns = {
    "Kuva":   new Column("image",           "url",    "image",  null),
    "Ryhmä":  new Column("product_type_id", "select", null,     $scope.types),
    "Tuote":  new Column("title",           "text",   null,     null,   null),
    "Hinta":  new Column("price",           "euro",   "euro"),
  };
  var cols = new Columns(columns);

  var setExtra = function (extra){
    $scope.types = extra;
    $scope.Columns.columns.Ryhmä.data = extra;
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
    REST.getColumn("ProductType", "title").then(
      function (data) //SUCCESS
      {
        setExtra(data);
      }, function (errorColumns)//FAILURE
      {}
    );
  }

  $scope.INIT("Product", UPDATE, null, cols );
});
