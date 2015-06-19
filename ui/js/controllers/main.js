'use strict';
angular.module("myApp", [])
.controller('mainController', ['$scope', '$http', function ($scope, $http) {

        var $uri ='/api/api.php';

        $scope.getNewAPI = function() {

            $http({
                method : 'GET',
                url : $uri
            }).success(function(data, status, headers, config) {
                $scope.list = data.data;
                console.log(status);
                console.log(data);
            }).error(function(data, status, headers, config) {
                console.log(status);
            });
        };

    }]);