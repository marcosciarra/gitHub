var app = angular.module("engine", ['ngAnimate']);

app.controller("engineController", ['$scope', "$http", function ($scope, $http) {
    $scope.init = function () {
        $http.post(params['form'] + '/controller/indexHandler.php',
            {'function': 'caricaDati'}
        ).then(function (data, status, headers, config) {
            $scope.tabelle = data.data.tabelle;
            console.log(data.data.tabelle);
            console.log($scope.tabelle);
        });
    };
}]);