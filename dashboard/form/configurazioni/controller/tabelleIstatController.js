ngApp.controller("tabelleIstatController", ["$scope", "$http", "$filter", function ($scope, $http, $filter) {

    var url = window.location.href;
    var params = decodeUrl(url, 'id');
    stampalog('URL');
    stampalog(params);

    $scope.mesi_istat = [
        {id: '1', descrizione: 'GENNAIO'},
        {id: '2', descrizione: 'FEBBRAIO'},
        {id: '3', descrizione: 'MARZO'},
        {id: '4', descrizione: 'APRILE'},
        {id: '5', descrizione: 'MAGGIO'},
        {id: '6', descrizione: 'GIUGNO'},
        {id: '7', descrizione: 'LUGLIO'},
        {id: '8', descrizione: 'AGOSTO'},
        {id: '9', descrizione: 'SETTEMBRE'},
        {id: '10', descrizione: 'OTTOBRE'},
        {id: '11', descrizione: 'NOVEMBRE'},
        {id: '12', descrizione: 'DICEMBRE'}
    ];


    $scope.init = function () {
        $scope.caricaDati();
    };

    /*================================================================================================================*/

    $scope.caricaDati = function () {
        $http.post(params['form'] + '/configurazioni/controller/tabelleIstatHandler.php',
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
            $scope.istat = data.data.istat;
            $scope.emptyIstat = data.data.istatNew;
            $scope.istatNew = angular.copy($scope.emptyIstat);
            stampalog($scope.istat);
            stampalog($scope.istatNew);

            var d = new Date();
            $scope.istatNew.mese = '' + d.getMonth();

            $scope.caricamentoCompletato = true;

        });
    };


    $scope.salvaDati = function () {
        $http.post(params['form'] + '/configurazioni/controller/tabelleIstatHandler.php',
            {
                'function': 'salvaDati',
                'obj': $scope.istatNew
            }
        ).then(function (data, status, headers, config) {
            stampalog(data);

            if (data.data == "ok") {
                swal({
                        title: "ISTAT aggiornato",
                        text: "",
                        type: "success"
                    },
                    function () {
                        $scope.istatNew = angular.copy($scope.emptyIstat);
                        location.reload();
                    });
            } else {
                swal("Errore", "Salvataggio non riuscito", "error");
            }

        });
    };


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
                $http.post(params['form'] + '/configurazioni/controller/tabelleIstatHandler.php',
                    {'function': 'eliminaDati', 'id': id}
                ).then(function (data, status, headers, config) {
                    location.reload();
                });
            });

    };


    $scope.stampaMese = function (mese) {
        return $filter('filter')($scope.mesi_istat, {id: mese})[0].descrizione;
    };

}]);
