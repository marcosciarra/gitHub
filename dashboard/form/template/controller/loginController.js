/**
 * Created by alessandro on 19/06/17.
 */

var app = angular.module("affittiApp", ["ngAnimate"]);

app.controller("loginController", ["$scope", "$http", function($scope, $http) {

    var url = window.location.href;
    var params = decodeUrl(url);
    var handler=url.split("login.php");

    onload = function () {

        stampalog(params);
    };


    $scope.effettuaLogin = function () {

        $scope.login = {
            'username': $scope.username,
            'password': $scope.password
        };

        stampalog($scope.login);

        $http.post(handler[0] + "template/controller/loginHandler.php",
            {'function': 'effettuaLogin', 'login': $scope.login}
        ).then(function (data, status, headers, config) {

            if(data.data.status =='pwd'){
                window.location.href = "cambioPassword.php?p1=" + btoa(data.data.idutente);
            }else if (data.data.status =='ok'){
                window.location.href = "home.php";
            }else if (data.data.status =='ko'){
                stampalog(data.data);
                if(data.data.error) {
                    swal(data.data.error.message, data.data.error.title, "error");
                }
                else{
                    swal("Login Fallito", "Controllare Nome utente e Password", "error");
                }
            }else{
                stampalog(data.data);
                swal("Contattare Help Desk", "errore grave", "error");
            }
        });
    };

}]);

