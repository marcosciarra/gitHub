ngApp.controller("opzioniController", ["$scope", "$http", "ngToast", function ($scope, $http, $ngToast) {

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
        $http.post(params['form'] + '/configurazioni/controller/opzioniHandler.php',
            {'function': 'caricaDati'}
        ).then(function (data, status, headers, config) {
            stampalog(data.data);
            $scope.elencoOpzioni = data.data.elencoOpzioni;
            $scope.caricamentoCompletato = true;
        });
    };

    /******************
     *   SALVADATI   * ================================================================================================
     ******************/

    $scope.salvaDati = function (id, valore) {
        $http.post(params['form'] + '/configurazioni/controller/opzioniHandler.php',
            {'function': 'salvaDati', 'id': id, 'valore': valore}
        ).then(function (data, status, headers, config) {
            stampalog(data);

            if (data.data.status == "ok") {
                $ngToast.create({
                    className: 'info',
                    content: 'Opzione modificata',
                    dismissButton: true,
                    timeout: 1500
                });

            } else {
                swal("Errore", "Salvataggio non riuscito", "error");
            }

        });
    };

}]);