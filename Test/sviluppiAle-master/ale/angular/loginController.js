/*  Alessandro Pericolo
 *  Training development
 *  Copyright @2017 Superperil
 *  "Programmers will conquer the Hello World"
*/

var app = angular.module('aleApp', []);

app.controller('loginController', function($scope, $http) {
    
    var url = window.location.href;
    var percorso = url.split("/anagrafica");
    
    $scope.login = function() {
        
        console.log($scope.userLogin);
        
        /*$http.post(percorso[0] + '/src/handler/LoginHandler.php',
            {'function': 'checkLogin', 'username': $scope.username}
        ).success(function (data, status, headers, config) {
            
            console.log(data);
        });*/ 
    };
    
});

