/*  Alessandro Pericolo
 *  Training development
 *  Copyright @2017 Superperil
 *  "Programmers will conquer the Hello World"
*/

var app = angular.module('aleApp', []);

app.controller('registrationController', function($scope, $http) {
    
    var url = window.location.href;
    var percorso = url.split("/anagrafica");
    
    $scope.registraUser = function() {
        
        console.log($scope.user);
        
        $http.post(percorso[0] + '/src/handler/RegistrationHandler.php',
            {'function': 'insertUser', 'user': $scope.user}
        ).success(function (data, status, headers, config) {
            
            console.log('UTENTE INSERITO');
        });
    };
    
});

