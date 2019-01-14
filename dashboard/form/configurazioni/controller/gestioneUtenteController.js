ngApp.controller("gestioneUtenteController", ["$scope", "$http", function($scope, $http) {

    var url = window.location.href;
    var params = decodeUrl(url, 'id');
    $scope.aaa = params['id'];

    stampalog(params);

    $scope.caricamentoCompletato = false;
    $scope.showRefresh = true;
    $scope.showIndietro = true;
    $scope.oggi = new Date();
    $scope.modificaPassword = false;
    $scope.livelloPassword = false;
    $scope.passwordGenerata = '';
    $scope.campiValidi = false;

    /******************
     *   CARICADATI   * ================================================================================================
     ******************/

    $scope.init = function () {
        $scope.caricaDati();

        // flag nuovoUtente:
        params['id'] == 0 ? $scope.nuovoUtente=true : $scope.nuovoUtente=false;
    };

    $scope.caricaDati = function () {
        $http.post(params['form'] + '/configurazioni/controller/gestioneUtenteHandler.php',
            {
                'function': 'caricaDati',
                'id': params['id']
            }
        ).then(function (data, status, headers, config) {
            stampalog(data.data);
            if (data.data.status == 'ko') {
                swal(data.data.error.title, data.data.error.message, 'error');
                return;
            }
            $scope.utente = data.data.utente;
            $scope.utente.confermaPassword = $scope.utente.password;

            $scope.caricamentoCompletato = true;
        });
    };

    /******************
     *   SALVA DATI   * ================================================================================================
     ******************/

    $scope.salvaDati = function () {
        stampalog($scope.utente);

        // flag per salvare password
        var salvaPassword = false;
        if($scope.nuovoUtente || $scope.modificaPassword){
            salvaPassword = true;
        }

        // CONTROLLO ROTAZIONE PASSWORD
        $http.post(params['form'] + '/configurazioni/controller/gestioneUtenteHandler.php',
            {
                'function': 'controllaRotazionePwd',
                'object': $scope.utente
            }
        ).then(function (data, status, headers, config) {
            stampalog(data.data);
            if(data.data.status == 'ok') {
                $http.post(params['form'] + '/configurazioni/controller/gestioneUtenteHandler.php',
                    {
                        'function': 'salvaDati',
                        'object': $scope.utente,
                        'salvaPwd': salvaPassword
                    }
                ).then(function (data, status, headers, config) {
                    stampalog(data.data);
                    if (data.data.status == 'ok') {
                        var messaggioPrimario = 'Dati utente aggiornati';
                        var messaggioSecondario = '';
                        if(!$scope.nuovoUtente && data.data.eseguiLogout == true){
                            messaggioSecondario = 'Verrà effettuato il logout';
                        }
                        if($scope.nuovoUtente){
                            messaggioSecondario = 'ATTENZIONE:\nla password provvisoria coincide con lo username';
                        }
                        swal({title: messaggioPrimario, text: messaggioSecondario, type: "success"},
                            function () {
                                window.location.href = $scope.params['home'] + encodeUrl("configurazioni", "utenti");
                                stampalog(data.data);
                            }
                        );
                    }else{
                        swal(data.data.error.title, data.data.error.message, "error");
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

    $scope.showModificaPassword = function() {
        $scope.modificaPassword = !$scope.modificaPassword;
        $scope.livelloPassword = false;
        $scope.passwordGenerata = '';
    }

    $scope.generaPassword = function(){
        $http.post(params['form'] + '/configurazioni/controller/gestioneUtenteHandler.php',
            {'function': 'generaPassword'}
        ).then(function (data, status, headers, config) {
            $scope.passwordGenerata = data.data.result;
            $scope.utente.password = data.data.result;
            $scope.utente.confermaPassword = data.data.result;
            $scope.livelloPassword=5;
        });
    };

    $scope.$watchGroup(["utente.nome","utente.cognome","utente.username",
            "utente.email"],function(){
        // FIXME senza webservice claudio
        $scope.livelloPassword = true;

        if($scope.utente) {
            if(
                $scope.utente.cognome != null &&
                $scope.utente.cognome.length > 0 &&
                $scope.utente.nome != null &&
                $scope.utente.nome.length > 0 &&
                $scope.utente.username != null &&
                $scope.utente.username.length > 0 &&
                controllaEmail($scope.utente.email)
                /*
                $scope.utente.password != null &&
                $scope.utente.password.length > 0 &&
                ($scope.utente.password == $scope.utente.confermaPassword) &&
                $scope.livelloPassword
                */
            ){
                $scope.campiValidi = true;
            }else{
                $scope.campiValidi = false;
            }
        }
    });

    //controllo sicurezza password
    $scope.$watch("utente.password",function() {
        if($scope.utente) {
// FIXME senza webservice claudio
/*
            $http.post(params['form'] + '/configurazioni/controller/gestioneUtenteHandler.php',
                {'function': 'verificaPassword', 'password': $scope.utente.password}
            ).then(function (data, status, headers, config) {
                stampalog(data.data);
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

    // button indietro
    $scope.indietro = function () {
        window.location.href = $scope.params['home'] + encodeUrl("configurazioni", "utenti");
    };

}]);