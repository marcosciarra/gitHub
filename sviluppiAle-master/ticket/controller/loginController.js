/**
 * Created by alessandro on 19/06/17.
 */

var app = angular.module('dscApp', []);

app.controller('loginController', function($scope) {

    $scope.login = function() {
        alert('Username: '+ $scope.username + ' Password: ' + $scope.password);
        location.href = '../anagrafica/elencoTicket.html?username='+$scope.username;
    };

});