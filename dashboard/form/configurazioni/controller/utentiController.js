ngApp.controller("utentiController", ["$scope", "$http", function($scope, $http) {

    var url = window.location.href;
    var params = decodeUrl(url);
    stampalog(params);

    $scope.caricamentoCompletato = false;
    $scope.showRefresh = true;
    $scope.showIndietro = true;

    $scope.mostraTastoModifica = function(id){
        $scope.idPulsante = id;

    };

    /************************
     *   CONTROLLA FORM NUOVO  * ================================================================================================
     ************************/

    $scope.datiValidi = false;
    $scope.controllaCampi = function(){
        stampalog($scope.utente.password.match((/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/)));
        if(
            $scope.utente.username.length > 0 &&
            $scope.utente.password.length > 7 &&
            $scope.utente.password.match((/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/)) &&
            controllaEmail($scope.utente.email)
        ){
            $scope.datiValidi = true;
        }else{
            $scope.datiValidi = false;
        }
    };

    /************************
     *   BLOCCA UTENTE  * ================================================================================================
     ************************/

    $scope.bloccaUtente = function(id){
        $http.post(params['form'] + '/configurazioni/controller/utentiHandler.php',
            {'function': 'gestisciStatoUtente','id':id}
        ).then(function (data, status, headers, config) {
            window.location.reload();
        });
    };

    /***************
     *   MODIFICA  * ================================================================================================
     ***************/

    $scope.modificaUtente = function (id) {
        window.location.href = $scope.params['home'] + encodeUrl("configurazioni", "gestioneUtente", id);
    };

    /******************
     *   CARICADATI   * ================================================================================================
     ******************/

    $scope.init=function () {
        $scope.caricaDati();
    };

    $scope.caricaDati = function(){
        $http.post(params['form'] + '/configurazioni/controller/utentiHandler.php',
            {'function': 'caricaDati'}
        ).then(function (data, status, headers, config) {
            stampalog(data.data);
            $scope.elencoUtenti = data.data.elencoUtenti;
            $scope.idUtenteFromSession = data.data.idUtenteLoggato;
            $scope.caricamentoCompletato = true;
        });
    };

    /******************
     *   ELIMINA   * ================================================================================================
     ******************/

    $scope.eliminaDati = function (id){
        swal({
                title: "",
                text: "Confermare la cancellazione",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Si",
                cancelButtonText: "No",
                closeOnConfirm: false
            },
            function(){
                $http.post(params['form'] + '/configurazioni/controller/utentiHandler.php',
                    {'function': 'eliminaDati', 'id': id}
                ).then(function (data, status, headers, config) {
                    stampalog(data);
                    location.reload();
                });
            });

    }
}]);