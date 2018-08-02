var App = angular.module("App", ["ui.router","angularUtils.directives.dirPagination", "ngStorage", "ngSanitize"]);

App.config(function($stateProvider, $urlRouterProvider, USER_ROLES)
{
  $urlRouterProvider.otherwise("/store");

  $stateProvider.state('store', {
    url: "/store",
    templateUrl: "pages/store/page.html",
    controller: "storeController",
    data: {
      authorizedRoles: USER_ROLES.all
    }
  })
  .state('user', {
    url: "/user",
    templateUrl: "pages/user/page.html",
    controller: "userController",
    data: {
      authorizedRoles: [USER_ROLES.admin, USER_ROLES.manager, USER_ROLES.worker, USER_ROLES.customer]
    }
  })
  .state('cart', {
    url: "/cart",
    templateUrl: "pages/cart/page.html",
    controller: "cartController",
    data: {
      authorizedRoles: USER_ROLES.all
    }
  })
  .state('systemProducts', {
    url: "/system/products",
    templateUrl: "pages/system/products/page.html",
    controller: "systemProductsController",
    data: {
      authorizedRoles: [USER_ROLES.admin, USER_ROLES.manager, USER_ROLES.worker]
    }
  })
  .state('systemProductTypes', {
    url: "/system/productTypes",
    templateUrl: "pages/system/productTypes/page.html",
    controller: "systemProductTypesController",
    data: {
      authorizedRoles: [USER_ROLES.admin, USER_ROLES.manager, USER_ROLES.worker]
    }
  })
  .state('systemShipping', {
    url: "/system/shipping",
    templateUrl: "pages/system/shipping/page.html",
    controller: "systemShippingController",
    data: {
      authorizedRoles: [USER_ROLES.admin, USER_ROLES.manager, USER_ROLES.worker]
    }
  })
  .state('systemOrders', {
    url: "/system/orders",
    templateUrl: "pages/system/orders/page.html",
    controller: "systemOrdersController",
    data: {
      authorizedRoles: [USER_ROLES.admin, USER_ROLES.manager, USER_ROLES.worker]
    }
  })
  .state('systemUsers', {
    url: "/system/users",
    templateUrl: "pages/system/users/page.html",
    controller: "systemUsersController",
    data: {
      authorizedRoles: [USER_ROLES.admin]
    }
  });
});
