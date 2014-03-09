'use strict';

angular.module('user', ['ngResource', 'ngCookies']).
  
  config(['$routeProvider', function($routeProvider) {
                 
    $routeProvider.when('/login', {
      templateUrl: 'partials/user/login.html', 
      controller: 'login'
    });
    
    $routeProvider.when('/register', {
      templateUrl: 'partials/user/register.html', 
      controller: 'register'
    });
    
    $routeProvider.when('/logout', {
      templateUrl: 'partials/user/login.html', 
      controller: 'logout'
    });
    
    $routeProvider.otherwise({redirectTo: '/login'});
    
  }]).

  factory('User', function($resource){
    return $resource('/user/:login', {}, {
      login: { method:'GET', params: { login: '@login' } },
      create: { method:'PUT' },
    });
  }).
  
  controller('login', ['$scope', '$http', '$location', 'Auth', 'User', 'currentUser', function($scope, $http, $location, Auth, User, currentUser) {
    $scope.currentUser = currentUser;
    
    $scope.submit = function() {
      var user = new User();
      Auth.setCredentials($scope.user.login, CryptoJS.SHA1($scope.user.password).toString());
      user.$login({ login: $scope.user.login }, 
        function(data) {
          $scope.error = false;
          $scope.currentUser.name = data.name;
          $scope.currentUser.login = data.login;
          
          $location.path('/task');
        }, 
        function(data) {
          $scope.error = true;
        }
      );
      
    }
    
  }]).

  controller('register', ['$scope', 'Auth', 'User', 'currentUser', function($scope, Auth, User, currentUser) {
    var user;
    
    $scope.submit = function() {
      user = new User();
      
      angular.extend(user, $scope.user);
      user.password = CryptoJS.SHA1(user.password).toString();

      user.$create(function() {
        $scope.error = false;
        $scope.success = true;
        
        Auth.setCredentials($scope.user.login, CryptoJS.SHA1($scope.user.password).toString());
        currentUser.name = $scope.user.name;
        currentUser.login = $scope.user.login;
          
      }, function() {
        $scope.error = true;
        
      });
      
    }
    
  }]).
  
  controller('logout', ['$scope', '$http', '$location', 'Auth', 'User', 'currentUser', function($scope, $http, $location, Auth, User, currentUser) {
    Auth.clearCredentials();
    currentUser.name = null;
    currentUser.login = null;
    $location.path('/login');
      
  }]).

  controller('header', ['$scope', 'currentUser', function($scope, currentUser) {
    $scope.currentUser = currentUser;
  }]).

  factory('currentUser', function(){
    return { };
  }).

  factory('Auth', ['Base64', '$cookieStore', '$http', function (Base64, $cookieStore, $http) {
    $http.defaults.headers.common['Authorization'] = 'Basic ' + $cookieStore.get('authdata');
 
    return {
      setCredentials: function (username, password) {
        var encoded = Base64.encode(username + ':' + password);
        $http.defaults.headers.common.Authorization = 'Basic ' + encoded;
        $cookieStore.put('authdata', encoded);
      },
      clearCredentials: function () {
        document.execCommand("ClearAuthenticationCache");
        $cookieStore.remove('authdata');
        $http.defaults.headers.common.Authorization = 'Basic ';
      }
    };
  }]).
  
  factory('Base64', function() {
      var keyStr = 'ABCDEFGHIJKLMNOP' +
      'QRSTUVWXYZabcdef' +
      'ghijklmnopqrstuv' +
      'wxyz0123456789+/' +
      '=';
      return {
        encode: function (input) {
          var output = "";
          var chr1, chr2, chr3 = "";
          var enc1, enc2, enc3, enc4 = "";
          var i = 0;

          do {
            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
              enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
              enc4 = 64;
            }

            output = output +
            keyStr.charAt(enc1) +
            keyStr.charAt(enc2) +
            keyStr.charAt(enc3) +
            keyStr.charAt(enc4);
            chr1 = chr2 = chr3 = "";
            enc1 = enc2 = enc3 = enc4 = "";
          } while (i < input.length);

          return output;
        },

        decode: function (input) {
          var output = "";
          var chr1, chr2, chr3 = "";
          var enc1, enc2, enc3, enc4 = "";
          var i = 0;

          // remove all characters that are not A-Z, a-z, 0-9, +, /, or =
          var base64test = /[^A-Za-z0-9\+\/\=]/g;
          if (base64test.exec(input)) {
            alert("There were invalid base64 characters in the input text.\n" +
              "Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
              "Expect errors in decoding.");
          }
          input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

          do {
            enc1 = keyStr.indexOf(input.charAt(i++));
            enc2 = keyStr.indexOf(input.charAt(i++));
            enc3 = keyStr.indexOf(input.charAt(i++));
            enc4 = keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
              output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
              output = output + String.fromCharCode(chr3);
            }

            chr1 = chr2 = chr3 = "";
            enc1 = enc2 = enc3 = enc4 = "";

          } while (i < input.length);

          return output;
        }
      };
  })
;