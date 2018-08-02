App.controller("storeController", function ($scope, REST, $http) {
  $scope.products = [];
  $scope.allTypes =
  {
      id: 0,
      title: "Kaikki"
  };
  $scope.types = [$scope.allTypes];
  $scope.selectedType = $scope.allTypes;

  $scope.Title = "Kauppa: " +  $scope.selectedType.title;

  $scope.errorColumns = [];

  var clearErrorColumns = function()
  {
    toggleErrors($scope.errorColumns, false);
    errorColumns = [];
  }

  $scope.UPDATE = function()
  {
    REST.getProductsByType($scope.selectedType.id).then(
      function (data) //SUCCESS
      {
        $scope.products = data;
      }, function (errorColumns)//FAILURE
      {}
    );
    (function(response) {
    });
    REST.getColumn("ProductType", "title").then(
      function (data) //SUCCESS
      {
        $scope.types = data;
        $scope.types[0] = "Kaikki";
      }, function (errorColumns)//FAILURE
      {}
    );
  }

  $scope.addToCart = function(id)
  {
    REST.shoppingCart("ADD", id).then(
      function (data) //SUCCESS
      {
        $scope.updateShoppingCartCounter();
      }, function (errorColumns)//FAILURE
      {}
    );
  }

  $scope.changeType = function(id)
  {
    $scope.selectedType = {id: id, title: $scope.types[id]};
    $scope.Title = "Kauppa: " + $scope.selectedType.title;
    $scope.UPDATE();
  }

  $scope.UPDATE();
});
