var app = angular.module("app", []);

app.controller("pageController", ["$scope", "$http", function($scope, $http) {
    $scope.visible = true;

    $scope.mostraMenu = function() {
        $scope.visible = !$scope.visible;
    }
}
]);