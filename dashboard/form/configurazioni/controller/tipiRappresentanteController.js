ngApp.controller("tipiRappresentanteController", ["$scope", "$http", function($scope, $http) {

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
        $http.post(params['form'] + '/configurazioni/controller/tipiRappresentanteHandler.php',
            {'function': 'caricaDati'}
        ).then(function (data, status, headers, config) {
            stampalog(data.data);
            $scope.elencoTipiRappresentante = data.data.elencoTipiRappresentante;
            $scope.newTipoRappresentante = angular.copy(data.data.newTipoRappresentante);
            $scope.newTipoRappresentanteApp = angular.copy(data.data.newTipoRappresentante);
            $scope.caricamentoCompletato = true;
        });
    };

    /******************
     *   SALVADATI   * ================================================================================================
     ******************/

    $scope.controllaCampiTipoRappresentante = function(tipoRappresentante) {
        if(
            tipoRappresentante != undefined &&
            tipoRappresentante.descrizione != null &&
            tipoRappresentante.descrizione.length > 0 &&
            tipoRappresentante.descrizione.length <= 45
        ){
            return true;
        }
        return false;
    };

    $scope.salvaDati = function (obj, salva){
        $http.post(params['form'] + '/configurazioni/controller/tipiRappresentanteHandler.php',
            {'function': 'salvaDati', 'object': obj}
        ).then(function (data, status, headers, config) {
            stampalog(data);

            if(data.data.status == "ok"){
                if(salva) {
                    $scope.elencoTipiRappresentante.push(obj);
                    $scope.newTipoRappresentante = angular.copy($scope.newTipoRappresentanteApp);
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
                $http.post(params['form'] + '/configurazioni/controller/tipiRappresentanteHandler.php',
                    {'function': 'eliminaDati', 'id': id}
                ).then(function (data, status, headers, config) {
                    stampalog(data);
                    location.reload();
                });
            });
    }
}]);