ngApp.controller("tipiIvaController", ["$scope", "$http", function($scope, $http) {

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
        $http.post(params['form'] + '/configurazioni/controller/tipiIvaHandler.php',
            {'function': 'caricaDati'}
        ).then(function (data, status, headers, config) {
            stampalog(data.data);
            $scope.elencoTipiIva = data.data.elencoTipiIva;
            $scope.newTipoIva = angular.copy(data.data.newTipoIva);
            $scope.newTipoIvaApp = angular.copy(data.data.newTipoIva);
            $scope.caricamentoCompletato = true;
        });
    };

    $scope.controllaDati = function () {

    }

    /******************
     *   SALVADATI   * ================================================================================================
     ******************/

    $scope.controllaCampiTipoIva = function(tipoIva) {
        if(
            tipoIva != undefined &&
            tipoIva.descrizione != null &&
            tipoIva.descrizione.length > 0 &&
            tipoIva.descrizione.length <= 10 &&
            (tipoIva.normativa_pf == null || tipoIva.normativa_pf.length < 1000) &&
            (tipoIva.normativa_pnf == null || tipoIva.normativa_pnf.length < 1000) &&
            tipoIva.aliquota >= 0 &&
            tipoIva.aliquota < 100
        ){
            return true;
        }
        return false;
    };

    $scope.salvaDati = function (obj, salva){
        if( (obj.aliquota < 0 || obj.aliquota >= 100) || obj.descrizione.length <= 0) {
            swal("Errore", "Valori non corretti", "error");
        }else{
            $http.post(params['form'] + '/configurazioni/controller/tipiIvaHandler.php',
                {'function': 'salvaDati', 'object': obj}
            ).then(function (data, status, headers, config) {
                stampalog(data);

                if (data.data.status == "ok") {
                    if (salva) {
                        $scope.elencoTipiIva.push(obj);
                        $scope.newTipoIva = angular.copy($scope.newTipoIvaApp);
                    }
                    swal("Salvataggio eseguito", "", "success");
                } else {
                    swal("Errore", "Salvataggio non riuscito", "error");
                }

            });
        }
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
                $http.post(params['form'] + '/configurazioni/controller/tipiIvaHandler.php',
                    {'function': 'eliminaDati', 'id': id}
                ).then(function (data, status, headers, config) {
                    stampalog(data);
                    location.reload();
                });
            });

    }
}]);