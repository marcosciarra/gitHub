ngApp.controller("tipiContrattoController", ["$scope", "$http", function ($scope, $http) {

    var url = window.location.href;
    var params = decodeUrl(url);
    stampalog(params);

    $scope.caricamentoCompletato = false;
    $scope.showRefresh = true;
    $scope.showIndietro = true;

    /******************
     *   CARICADATI   * ================================================================================================
     ******************/

    $scope.init = function () {
        $scope.caricaDati();
    };

    $scope.caricaDati = function () {
        $http.post(params['form'] + '/configurazioni/controller/tipiContrattoHandler.php',
            {'function': 'caricaDati'}
        ).then(function (data, status, headers, config) {
            stampalog(data.data);
            $scope.elencoTipiContratto = data.data.elencoTipiContratto;
            for (var i = 0; i < $scope.elencoTipiContratto.length; i++) {
                $scope.elencoTipiContratto[i].istat = $scope.elencoTipiContratto[i].tipo_uso + '-' + $scope.elencoTipiContratto[i].percentuale_istat;
            }
            $scope.newTipoContratto = angular.copy(data.data.newTipoContratto);
            $scope.newTipoContrattoApp = angular.copy(data.data.newTipoContratto);
            $scope.caricamentoCompletato = true;
        });
    };

    /******************
     *   SALVADATI   * ================================================================================================
     ******************/

    $scope.controllaCampiTipoContratto = function(tipoContratto) {
        if(
            tipoContratto != undefined &&
            tipoContratto.descrizione != null &&
            tipoContratto.descrizione.length > 0 &&
            tipoContratto.primo_rinnovo != null && tipoContratto.primo_rinnovo > 0 &&
            tipoContratto.secondo_rinnovo != null && tipoContratto.secondo_rinnovo >= 0 &&
            tipoContratto.rinnovo_successivo != null && tipoContratto.rinnovo_successivo >= 0 &&
            tipoContratto.preavviso_locatore != null && tipoContratto.preavviso_locatore > 0 &&
            tipoContratto.preavviso_conduttore != null && tipoContratto.preavviso_conduttore > 0 &&
            tipoContratto.istat != undefined
        ){
            return true;
        }
        return false;
    };

    $scope.salvaDati = function (obj, salva) {
        var tmp = obj.istat.split('-');
        obj.tipo_uso = tmp[0];
        obj.percentuale_istat = tmp[1];

        $http.post(params['form'] + '/configurazioni/controller/tipiContrattoHandler.php',
            {'function': 'salvaDati', 'object': obj}
        ).then(function (data, status, headers, config) {
            stampalog(data);

            if(data.data.status == "ok"){
                if (salva) {
                    $scope.elencoTipiContratto.push(obj);
                    $scope.newTipoContratto = angular.copy($scope.newTipoContrattoApp);
                }
                swal("Salvataggio eseguito", "", "success");
            } else {
                swal("Errore", "Salvataggio non riuscito", "error");
            }

        });
    };

    /******************
     *   ELIMINA   * ================================================================================================
     ******************/

    $scope.eliminaDati = function (id) {

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
            function () {
                $http.post(params['form'] + '/configurazioni/controller/tipiContrattoHandler.php',
                    {'function': 'eliminaDati', 'id': id}
                ).then(function (data, status, headers, config) {
                    stampalog(data);
                    location.reload();
                });
            });
    };


    /*===============================================MODIFICA TIPO MESI/ANNI==========================================*/
    $scope.modificaTempo=function (riga) {
        if(riga.tipo_periodo=='M'){
            riga.secondo_rinnovo=0;
            riga.rinnovo_successivo=0;
        }
    };
}]);