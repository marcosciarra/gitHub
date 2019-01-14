ngApp.controller("configurazioniController", ["$scope", "$http", function($scope, $http) {

    var url = window.location.href;
    var params = decodeUrl(url);
    stampalog(params);

    $scope.caricamentoCompletato = false;

    /******************
     *   CARICADATI   * ================================================================================================
     ******************/

    $scope.init=function () {
        $scope.caricaDati();
    };

    $scope.caricaDati = function(){
        /*
        $http.post(params['percorso'] + '/form/contratto/controller/anagraficaHandler.php',
            {'function': 'caricaDati'}
        ).then(function (data, status, headers, config) {
            $scope.tipoContratto=data.data.tipoContratto;
            stampalog(data.data);
        });
        */
    };
}]);