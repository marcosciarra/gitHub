ngApp.controller("dashboardController", ["$scope", "$http", function ($scope, $http) {

    var url = window.location.href;
    var params = decodeUrl(url);
    stampalog(params);

    // $scope.caricamentoCompletato = false;
    $scope.caricamentoCompletato = true;

    /******************
     *   CARICADATI   * ================================================================================================
     ******************/

    $scope.init = function () {
        $scope.caricaDati();
    };


    $scope.caricaDati = function () {
        $http.post(params['form'] + '/dashboard/controller/dashboardHandler.php',
            {'function': 'caricaDati'}
        ).then(function (data, status, headers, config) {

            if (data.data.status == 'ko') {
                swal(data.data.error.title, data.data.error.message, 'error');
                return;
            }

            stampalog(data.data);


            $scope.caricamentoCompletato = true;
        });
    };

}])
;