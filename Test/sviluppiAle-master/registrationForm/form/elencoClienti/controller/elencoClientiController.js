var app = angular.module('dscApp', ["ngAnimate", "ngSanitize", "ui.bootstrap"]);

app.controller('elencoClientiController',['$scope', '$http', function($scope, $http) {

    var url = decodeUrlTest(window.location.href);
    console.log(url);

    $scope.elencoClientiPagina = new Array();

    onload = function () {

        $scope.caricaDati();
    };

    $scope.caricaDati = function(){

        $http.post( url['percorso']+ '/form/elencoClienti/controller/elencoClientiHandler.php',
            {'function': 'getDatiPagina'}
        ).then(function (data, status, headers, config) {
            console.log(data);

            $scope.elencoClienti = data.data;
            var len = $scope.elencoClienti.length;

            $scope.definisciPaginatore(len);
        });
    };


    /*****************
     *   PAGINATOR   * ==================================================================================================
     ****************/

    $scope.currentPage = 1;
    $scope.itemsPerPage = 10;
    $scope.maxSize = 5; //Number of pager buttons to show

    $scope.definisciPaginatore = function(len){

        $scope.totalItems = len;

        $scope.selectElementiPerPagina = [
            {"valore": 10, "descrizione": "10"},
            {"valore": 25, "descrizione": "25"},
            {"valore": 50, "descrizione": "50"},
            {"valore": 100, "descrizione": "100"},
            {"valore": len, "descrizione": "Tutti"},
        ];

    };



    $scope.setPage = function (pageNo) {
        $scope.currentPage = pageNo;
    };

    $scope.setItemsPerPage = function() {

        $scope.currentPage = 1; //reset to first page
    }

}]);