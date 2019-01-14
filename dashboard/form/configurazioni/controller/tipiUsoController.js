ngApp.controller("tipiUsoController", ["$scope", "$http", function ($scope, $http) {

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
        $http.post(params['form'] + '/configurazioni/controller/tipiUsoHandler.php',
            {'function': 'caricaDati'}
        ).then(function (data, status, headers, config) {
            stampalog(data.data);
            $scope.elencoTipiUso = data.data.elencoTipiUso;

            $scope.newTipoUso = angular.copy(data.data.newTipoUso);
            $scope.newTipoUsoApp = angular.copy(data.data.newTipoUso);
            $scope.caricamentoCompletato = true;
        });
    };

    /******************
     *   SALVADATI   * ================================================================================================
     ******************/

    $scope.controllaCampiTipoUso = function(tipoUso) {
        if(
            tipoUso != undefined &&
            tipoUso.descrizione != null &&
            tipoUso.descrizione.length > 0 &&
            tipoUso.descrizione.length <= 45
        ){
            return true;
        }
        return false;
    };

    $scope.salvaDati = function (obj, salva) {

        $http.post(params['form'] + '/configurazioni/controller/tipiUsoHandler.php',
            {'function': 'salvaDati', 'object': obj}
        ).then(function (data, status, headers, config) {
            stampalog(data);

            if (data.data.status == "ok") {
                if (salva) {
                    $scope.elencoTipiUso.push(obj);
                    $scope.newTipoUso = angular.copy($scope.newTipoUsoApp);
                }
                swal("Salvataggio eseguito", "", "success");
            } else {
                swal("Errore", "Salvataggio non riuscito", "error");
            }

        });
    };

    /****************
     *   ELIMINA   * ================================================================================================
     ****************/

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
                $http.post(params['form'] + '/configurazioni/controller/tipiUsoHandler.php',
                    {'function': 'eliminaDati', 'id': id}
                ).then(function (data, status, headers, config) {
                    stampalog(data);
                    location.reload();
                });
            });
    }
}]);