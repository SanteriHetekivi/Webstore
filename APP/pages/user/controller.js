App.controller("userController", function ($scope, $http,$state, AuthService, REST)
{
  $scope.Title = "Oma Sivu";
  $isShipped = "EI";
  $scope.orders = [];
  $scope.show = {};


  $scope.checkShipped = function(order)
  {
    if(emptyDate(order.shipped)) return "Lähettämätön";
    else return "Lähetetty ("+pDateTime(order.shipped)+")";

  }

  $scope.specialUPDATE = function()
  {
    $isShipped = "EI";

    REST.getUserOrders().then(
      function (data) //SUCCESS
      {
        $scope.orders = data;
      }, function (errorColumns)//FAILURE
      {}
    );
  }

  $scope.SHOW = function(object)
  {
    $scope.show = angular.copy(object);
    REST.getOrderProducts($scope.show.id).then(
      function (data) //SUCCESS
      {
        $scope.show.products = data;
        $("#orderProducts").modal();
      }, function (errorColumns)//FAILURE
      {}
    );
  }


  $scope.specialUPDATE();
  $scope.INIT(null, null,  null, null );

});
