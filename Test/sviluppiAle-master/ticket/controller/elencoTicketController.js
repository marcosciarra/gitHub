/**
 * Created by alessandro on 19/06/17.
 */

var app = angular.module('dscApp', []);

app.controller('elencoTicketController', function($scope) {

    var url = window.location.href;

    if(url.indexOf("username") > -1) {
        $scope.username = /username=([^&]+)/.exec(url)[1];
    }

    $scope.goToNewTicket = function() {
        location.href = '../anagrafica/nuovoTicket.html?username='+$scope.username;
    };

});