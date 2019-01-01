var app = angular.module("engine", ['ngAnimate']);

app.controller("indexController", ['$scope', "$http", function ($scope, $http) {

    /*---------------------------------CARICAMENTO TABELLE------------------------------------------------------------*/

    $scope.init = function () {
        $http.post('./controller/indexHandler.php',
            {'function': 'caricaDati'}
        ).then(function (data, status, headers, config) {
            $scope.tabelle = data.data.tabelle;
            console.log('Elenco tabelle');
            console.log('--------------');
            console.log($scope.tabelle);
        });
    };

    /*---------------------------------ENGINE TABELLE-----------------------------------------------------------------*/

    $scope.engineTabelle = function (nomeTabella) {
        $http.post('./controller/indexHandler.php',
            {'function': 'engineTabelle', 'nomeTabella': nomeTabella}
        ).then(function (data, status, headers, config) {
            if (data.data.status == 'ok') {
                $scope.tabelle = data.data.tabelle;
                alert('Modellazione ' + nomeTabella + ' fatta!');
                $scope.init();
            }
        });
    };


}]);