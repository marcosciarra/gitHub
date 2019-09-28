var app = angular.module("myList", []); 

app.controller("elencoController", function($scope) {
    
    $scope.prodotti = [];
    $scope.nuovoElemento = {};


    console.log(JSON.stringify($scope.prodotti));
    
    $scope.aggiungiElemento = function () {

        $scope.errore = "";

        if($scope.nuovoElemento.nome != null && $scope.nuovoElemento.nome != ''){
            if(!$scope.nuovoElemento.quantita){
                $scope.nuovoElemento.quantita = 1;
            }
            $scope.inserisco = $scope.nuovoElemento
            $scope.prodotti.push($scope.inserisco);
            $scope.nuovoElemento = {};
        }

    }
    
    $scope.togliElemento = function (x) {
        $scope.errore = "";    
        $scope.prodotti.splice(x, 1);
    }
});