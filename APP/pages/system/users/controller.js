App.controller("systemUsersController", function ($scope, $http, REST, USER_ROLES) {
  $scope.Title = "Hallinta: Käyttäjät";

  var columns = {
    "Käyttäjänimi": new Column("username", "text"),
    "Ryhmä":        new Column("userGroup", "select", null,     USER_ROLES.allNamed),
    "Etunimi":      new Column("firstName", "text"),
    "Sukunimi":     new Column("lastName", "text"),
    "Osoite":       new Column("address", "text"),
    "Postinumero":  new Column("postcode", "text"),
    "Kaupunki":     new Column("city", "text"),
    "Maa":          new Column("country", "text"),
    "Email":        new Column("email", "text")
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

  $scope.INIT("User", UPDATE,  null, cols );

});
