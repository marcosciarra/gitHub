var app = angular.module('app', ['angularjs-gauge']);


app.controller('AppController', ['$scope', function($scope) {

    $scope.statoAvanzamento1 = 15;
    $scope.statoAvanzamento2 = 55;
    $scope.statoAvanzamento3 = 95;


    $scope.thresholdOptions = {
        '0': { color: 'red' },
        '33': {color: 'orange' },
        '66': {color: 'green'}
    };
}]);
