/**
 * Created by alessandro on 19/06/17.
 */

var app = angular.module("affittiApp", ["ngAnimate"]);

app.controller("loginController", ["$scope", "$http", function($scope, $http) {

    var url = new URL(window.location.href);
    var id = atob(url.searchParams.get("p1"));
    var params = window.location.href.split("form");
    stampalog(params);
    var handler = params[0] + "form/template/controller/cambioPasswordHandler.php";
    $scope.vecchiaPassword = '';
    $scope.mostraFormNuovaPassword = false;

    onload = function () {
        $http.post(handler,
            {'function': 'caricaDati', 'id': id}
        ).then(function (data, status, headers, config) {
            $scope.utente = data.data.utente;

            stampalog($scope.utente);
        });
    };

    /******************
     *   SALVA DATI   * ================================================================================================
     ******************/

    $scope.salvaDati = function () {
        stampalog($scope.utente);
        // CONTROLLO ROTAZIONE PASSWORD
        $http.post(handler,
            {
                'function': 'controllaRotazionePwd',
                'object': $scope.utente
            }
        ).then(function (data, status, headers, config) {
            stampalog(data.data);
            if(data.data.status == 'ok') {
                $http.post(handler,
                    {
                        'function': 'salvaDati',
                        'object': $scope.utente
                    }
                ).then(function (data, status, headers, config) {
                    stampalog(data.data);
                    if (data.data.status == 'ok') {
                        swal({title: "La password è stata modificata", text: "", type: "success"},
                            function () {
                                window.location.href = params[0];
                            }
                        );
                    }
                });
            }else{
                swal({title: "Errore", text: "La password inserita risulta già utilizzata", type: "error"});
            }
        });
    };

    /****************
     *   PASSWORD   * ================================================================================================
     ****************/

    $scope.verificaVecchiaPassword = function(){
        $http.post(handler,
            {'function': 'verificaVecchiaPassword',
                'idUtente': id,
                'oldPassword' : $scope.vecchiaPassword}
        ).then(function (data, status, headers, config) {
            if(data.data.status == 'ok') {
                $scope.mostraFormNuovaPassword = true;
                $scope.utente.password = '';
                $scope.utente.confermaPassword = '';
            }else{
                swal({title: "Errore", text: "La password inserita non è corretta", type: "error"},
                    function(){
                        location.reload();
                    }
                );
            }
        });
    };

    $scope.generaPassword = function(){
        $http.post(handler,
            {'function': 'generaPassword'}
        ).then(function (data, status, headers, config) {
            stampalog(data.data);
            $scope.passwordGenerata = data.data.result;
            $scope.utente.password = data.data.result;
            $scope.utente.confermaPassword = data.data.result;
            $scope.livelloPassword=5;
            $scope.visualizzaPassword = true;
        });
    };

    $scope.$watchGroup(["utente.password","utente.confermaPassword"],function(){
        // FIXME senza webservice claudio
        $scope.livelloPassword = true;

        if($scope.utente) {
            if(
                $scope.utente.password != null &&
                $scope.utente.password.length > 0 &&
                ($scope.utente.password == $scope.utente.confermaPassword) &&
                $scope.livelloPassword
            ){
                $scope.campiValidi = true;
            }else{
                $scope.campiValidi = false;
            }
        }
    });

    //controllo sicurezza password
    $scope.$watch("utente.password",function() {
        if($scope.utente && $scope.utente.password.length>0) {
            // FIXME senza webservice claudio
            /*
            $http.post(handler,
                {'function': 'verificaPassword', 'password': $scope.utente.password}
            ).then(function (data, status, headers, config) {
                $scope.resultPwd = data.data.result;
                if ($scope.resultPwd.level > 2) {
                    $scope.livelloPassword = true;
                } else {
                    $scope.livelloPassword = false;
                }
            });
            */
            $scope.livelloPassword = true;
        }else{
            $scope.livelloPassword = false;
        }
    });



}]);

