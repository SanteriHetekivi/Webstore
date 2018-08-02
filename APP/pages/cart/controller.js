App.controller("cartController", function ($scope, $http, REST, Session)
{
  $scope.Title = "Ostoskori";
  $scope.cart = {};
  $scope.shipping = [];
  $scope.user = Session.user;
  $scope.errorColumns = [];
  $scope.Ship = {};
  $scope.extraQuestions =
  [
    {question: "Etunimi", column: "firstName" },
    {question: "Sukunimi", column: "lastName" },
    {question: "Osoite", column: "address" },
    {question: "Maa", column: "country" },
    {question: "Kaupunki", column: "city" },
    {question: "Postinumero", column: "postcode" },
    {question: "Email", column: "email" }
  ];


  var clearErrorColumns = function()
  {
    toggleErrors($scope.errorColumns, false);
    errorColumns = [];
  }

  $scope.UPDATE = function()
  {
    REST.shoppingCart("GET", "").then(
      function (data) //SUCCESS
      {
        clearErrorColumns();
        $scope.cart = data;
        $scope.updateShoppingCartCounter();
        if(isset(Session.user) && isset($scope.cart.order))
        {
          var u = Session.user;
          if(isEmpty($scope.cart.order.firstName))  $scope.cart.order.firstName = u.firstName;
          if(isEmpty($scope.cart.order.lastName))   $scope.cart.order.lastName = u.lastName;
          if(isEmpty($scope.cart.order.address))    $scope.cart.order.address = u.address;
          if(isEmpty($scope.cart.order.country))    $scope.cart.order.country = u.country;
          if(isEmpty($scope.cart.order.city))       $scope.cart.order.city = u.city;
          if(isEmpty($scope.cart.order.postcode))   $scope.cart.order.postcode = u.postcode;
          if(isEmpty($scope.cart.order.email))      $scope.cart.order.email = u.email;
        }
      }, function (errorColumns)//FAILURE
      {

      }
    );
  }

  $scope.REMOVE = function(id)
  {
    REST.shoppingCart("REMOVE", id).then(
      function (data) //SUCCESS
      {
        $scope.UPDATE();
      }, function (errorColumns)//FAILURE
      {}
    );

  }

  $scope.PAY = function()
  {
    clearErrorColumns();
    REST.shoppingCart("PAY", null ,$scope.cart.order).then(
      function (data) //SUCCESS
      {
        $scope.UPDATE();
      }, function (errorColumns) //FAILURE
      {
        $scope.errorColumns = errorColumns;
        toggleErrors($scope.errorColumns, true);
      }
    );
  }

  $scope.UPDATE();
});
