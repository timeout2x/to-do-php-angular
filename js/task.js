'use strict';

angular.module('task', ['ngResource']).
  
  config(['$routeProvider', function($routeProvider) {
                 
    $routeProvider.when('/task', {
      templateUrl: 'partials/task/list.html', 
      controller: 'list',      
    });
    
    $routeProvider.when('/task/:id', {
      templateUrl: 'partials/task/edit.html', 
      controller: 'edit'
    });
    
  }]).

  factory('Task', function($resource){
    return $resource('/task/:id', {}, {
      list: { method:'GET', params: { id: '@id' }, isArray: true },
      update: { method:'POST', params: { id: '@id' } },
      create: { method:'PUT', params: { id: '@id' } },
      remove: { method:'DELETE', params: { id: '@id' } }
    });
  }).
  
  directive('datepicker', function () {
    return {
      restrict: 'A',
      link: function (scope, elem, attrs, ctrl) {
        $(elem).datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true,
        });
        
        $(elem).bind('change', function () {
          scope.task.due_date = $(elem).children('input').val();
        });
      }
    }
  }).
  
  filter('completed', function() {
    return function(input) {
      return (input == '1')?'check':'';
    };
  }).

  filter('priority', function() {
    return function(input) {
      switch(parseInt(input)) {

        case 1: return 'info'; break;
        case 2: return 'warning'; break;
        case 3: return 'important'; break;

        default: return ''; break;
      }
    };
  }).

  filter('priority_title', function() {
    return function(input) {
      switch(parseInt(input)) {

        case 1: return 'Average'; break;
        case 2: return 'High'; break;
        case 3: return 'Urgent'; break;

        default: return 'Low'; break;
      }
    };
  }).

  controller('list', ['$scope', '$http', 'Task', function($scope, $http, Task) {
    $scope.tasks = Task.list(null, null, null, function(responce) {
      if (responce.status == 401) {
        $scope.error = 'auth';
      }
    });
    
    $scope.orderProp = 'due_date';
  }]).

  controller('edit', ['$scope', '$route', '$location', 'Task', '$routeParams', function($scope, $route, $location, Task, $routeParams) {
    var task;
     
    if ($routeParams.id && $routeParams.id > 0) {
      task = Task.get({ id: $routeParams.id }, null, null, function(responce) {
        if (responce.status == 401) {
          $scope.error = 'auth';
        }
      });
      $scope.task = task;
    }
    
    $scope.returnMarker = 0;
    $scope.setReturnMarker = function() {
      $scope.returnMarker = 1;  
    }
    
    $scope.submit = function() {
      if (!task) {
        task = new Task();
      }
      
      angular.extend(task, $scope.task);

      if (task.id) {
        task.$update(completeRequestHandler);
      }
      else {
        task.$create(completeRequestHandler);
      }
      
    }
    
    $scope.remove = function() {
      task.$remove({ id: task.id }, function() {
        $scope.returnMarker = 1;
        completeRequestHandler();
      });
    }
    
    function completeRequestHandler() {
      if ($scope.returnMarker == 1) {
        $location.path('/task');
      }
      else {
        $route.reload();
      }
    }
    
  }])
  
;