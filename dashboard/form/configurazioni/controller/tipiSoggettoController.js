ngApp.controller("tipiSoggettoController", ["$scope", "$http", function($scope, $http) {

    var url = window.location.href;
    var params = decodeUrl(url);
    stampalog(params);

    $scope.caricamentoCompletato = false;
    $scope.showRefresh = true;
    $scope.showIndietro = true;

    /******************
     *   CARICADATI   * ================================================================================================
     ******************/

    $scope.init=function () {
        $scope.caricaDati();
    };

    $scope.caricaDati = function(){
        $http.post(params['form'] + '/configurazioni/controller/tipiSoggettoHandler.php',
            {'function': 'caricaDati'}
        ).then(function (data, status, headers, config) {
            stampalog(data.data);
            $scope.elencoTipiSoggetto = data.data.elencoTipiSoggetto;
            data.data.newTipoSoggetto.gruppo = 0;
            $scope.newTipoSoggetto = angular.copy(data.data.newTipoSoggetto);
            $scope.newTipoSoggettoApp = angular.copy(data.data.newTipoSoggetto);
            $scope.caricamentoCompletato = true;
        });
    };

    /******************
     *   SALVADATI   * ================================================================================================
     ******************/

    $scope.controllaCampiTipoSoggetto = function(tipoSoggetto) {
        if(
            tipoSoggetto != undefined &&
            tipoSoggetto.descrizione != null &&
            tipoSoggetto.descrizione.length > 0 &&
            tipoSoggetto.descrizione.length <= 45 &&
            tipoSoggetto.gruppo != 0
        ){
            return true;
        }
        return false;
    };

    $scope.salvaDati = function (obj, salva){
        $http.post(params['form'] + '/configurazioni/controller/tipiSoggettoHandler.php',
            {'function': 'salvaDati', 'object': obj}
        ).then(function (data, status, headers, config) {
            stampalog(data);

            if(data.data.status == "ok"){
                if(salva) {
                    $scope.elencoTipiSoggetto.push(obj);
                    $scope.newTipoSoggetto = angular.copy($scope.newTipoSoggettoApp);
                }
                swal("Salvataggio eseguito", "" , "success");
            }else {
                swal("Errore", "Salvataggio non riuscito", "error");
            }

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
                $http.post(params['form'] + '/configurazioni/controller/tipiSoggettoHandler.php',
                    {'function': 'eliminaDati', 'id': id}
                ).then(function (data, status, headers, config) {
                    stampalog(data);
                    location.reload();
                });
            });
    }
}]);