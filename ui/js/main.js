'use strict';
angular.module('rss')
.controller('MainCtrl', ['$scope', '$http', function ($scope, $http) {

        var $uri ='js/sample.json';

        $scope.renewRSS = function() {

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