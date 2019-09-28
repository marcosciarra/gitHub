/**[
  {
    "nome": "Alessandro",
    "cognome": "Pericolo",
    "eta": 26
  },
  {
    "nome": "Luca",
    "cognome": "Rossi",
    "eta": 10
  },
  {
    "nome": "Mario",
    "cognome": "Bianchi",
    "eta": 62
  },
  {
    "nome": "Andrea",
    "cognome": "Verdi",
    "eta": 82
  }
]
 * Created by clickale on 06/10/16.
 */

var app = angular.module('myApp', []);

app.controller('loginController', function($scope) {
    
    $scope.login = function() {
        alert('Username: '+ $scope.username + ' Password: ' + $scope.password);
        location.href = '../pagine/elencoTicket.html?username='+$scope.username;
    };

});

