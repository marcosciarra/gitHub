ngApp.controller("testiLettereController", ["$scope", "$http", function ($scope, $http) {

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
        $http.post(params['form'] + '/configurazioni/controller/testiLettereHandler.php',
            {'function': 'caricaDati'}
        ).then(function (data, status, headers, config) {
            stampalog(data.data);
            $scope.elencoTipiTestiLettere = data.data.elencoTipiTestiLettere;
            $scope.elencoTestiLettere = data.data.elencoTestiLettere;

            $scope.tipiTestiLettere=0;

            $scope.caricamentoCompletato = true;
        });
    };

    /******************
     *   SALVADATI   * ================================================================================================
     ******************/

    $scope.salvaDati = function (id, oggetto, testo) {
        $http.post(params['form'] + '/configurazioni/controller/testiLettereHandler.php',
            {
                'function': 'salvaDati',
                'id': id,
                'oggetto': oggetto,
                'testo': testo
            }
        ).then(function (data, status, headers, config) {
            stampalog(data);

            if (data.data.status == "ok") {
                swal("Salvataggio eseguito", "", "success");
            } else {
                swal("Errore", "Salvataggio non riuscito", "error");
            }

        });
    };

}]);