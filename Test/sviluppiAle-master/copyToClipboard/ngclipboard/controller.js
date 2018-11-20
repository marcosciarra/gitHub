var app = angular.module('myApp', ['ngclipboard']);

app.controller('testController', ['$scope', function ($scope) {

    $scope.elencoNomePiva = [
                                {'ID':1, 'CONDOMINIO':'CICCIO', 'PIVA': 12345678910},
                                {'ID':2, 'CONDOMINIO':'SUKA', 'PIVA': 26489236948},
                                {'ID':3, 'CONDOMINIO':'TEST', 'PIVA': 58302730271},
                                {'ID':4, 'CONDOMINIO':'PROVA', 'PIVA': 24729084908},
                                {'ID':5, 'CONDOMINIO':'ALE', 'PIVA': 849281238311}

                            ];

    // You can still access the clipboard.js event
    $scope.onSuccess = function(e) {
        console.info('Action:', e.action);
        console.info('Text:', e.text);
        console.info('Trigger:', e.trigger);

        e.clearSelection();
    };

    $scope.onError = function(e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
    }

}]);