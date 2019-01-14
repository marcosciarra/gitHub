ngApp.controller("rilasciController", ["$scope", "$http", "$filter", "ngToast", function ($scope, $http, $filter, $ngToast) {

    var url = window.location.href;
    var params = decodeUrl(url, 'id');
    stampalog(params);

    $scope.caricamentoCompletato = false;

    /*--------------------------------------------------CARICA DATI---------------------------------------------------*/

    $scope.init = function () {

        $scope.selezionatiTutti = true;

        $scope.caricaDati();
    };

    $scope.caricaDati = function () {
        $http.post(params['form'] + '/rilasci/controller/rilasciHandler.php',
            {
                'function': 'caricaDati'
            }
        ).then(function (data, status, headers, config) {
            stampalog(data.data.status);
            if (data.data.status == 'ko') {
                swal(data.data.error.title, data.data.error.message, 'error');
                return;
            }
            stampalog('Carico Dati');
            stampalog(data.data);
            $scope.clienti= data.data.clienti;
            if ($scope.clienti.length > 0) {
                for (var i = 0; i < $scope.clienti.length; i++) {
                    $scope.clienti[i].selezionato = true;
                }
            }

            $scope.caricamentoCompletato = true;
        });
    };


    $scope.rilascia = function (ambiente) {
        $http.post(params['form'] + '/rilasci/controller/rilasciHandler.php',
            {
                'function': 'rilascia',
                'ambiente': ambiente
            }
        ).then(function (data, status, headers, config) {
            stampalog(data.data.status);
            if (data.data.status == 'ko') {
                swal(data.data.error.title, data.data.error.message, 'error');
                return;
            }

            $scope.caricamentoCompletato = true;
        });
    };

}])
;