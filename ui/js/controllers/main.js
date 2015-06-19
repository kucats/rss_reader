'use strict';
angular.module("myApp", [])
.controller('mainController', ['$scope', '$http', function ($scope, $http) {

        var $uri ='/rss_reader/api/api.php';

		$scope.changeCategory = function(category){
			$scope.category = category;
		}

        $scope.getNewAPI = function() {

            $http({
                method : 'GET',
                url : $uri+'?category='+$scope.category
            }).success(function(data, status, headers, config) {
                $scope.list = data.data;
                console.log(status);
                console.log(data);
            }).error(function(data, status, headers, config) {
                console.log(status);
            });
        };

    }]);