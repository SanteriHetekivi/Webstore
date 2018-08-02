App.constant('AUTH_EVENTS', {
  loginSuccess: 'auth-login-success',
  loginFailed: 'auth-login-failed',
  logoutSuccess: 'auth-logout-success',
  sessionTimeout: 'auth-session-timeout',
  authenticated: 'auth-authenticated',
  notAuthenticated: 'auth-not-authenticated',
  notAuthorized: 'auth-not-authorized'
})
App.constant('USER_ROLES', {
  admin: 'admin',
  manager: 'manager',
  worker: 'worker',
  customer: 'customer',
  guest: 'guest',
  all: ['admin', 'manager', 'worker',
    'customer', 'guest'],
  allNamed: {'admin':"Admin", 'manager':"Manageri", 'worker':"Työntekijä",
    'customer':"Asiakas"}
})
