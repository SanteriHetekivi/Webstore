App.controller("systemProductTypesController", function ($scope, $http, REST) {
  $scope.Title = "Hallinta: Tuotetyypit";

  var columns = {
    "Nimi":   new Column("title", "text")
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

  $scope.INIT("ProductType", UPDATE,  null, cols );
});
