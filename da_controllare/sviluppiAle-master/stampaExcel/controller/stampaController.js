/**
 * Created by clickale on 04/04/17.
 */

var app = angular.module('myApp', []);

app.controller('stampaController', function ($scope, $http) {

    var url = window.location.href;
    var percorso = url.split("/stampaExcel");


    //---------------------------------------------------- DATI ----------------------------------------------------

    $scope.data = [{"nome":"Alessandro","cognome":"Pericolo","eta":26,"citta":"Legnano","sesso":"M","millesimi":1000,"note":"Alessandro Pericolo note di prova testo lungo per stampa. esempio container cell in table"},
                    {"nome":"Sara","cognome":"Neri","eta":39,"citta":"Venezia","sesso":"F","millesimi":29472,"note":"Sara Neri note di prova"},
                    {"nome":"Luca","cognome":"Rossi","eta":10,"citta":"Milano","sesso":"M","millesimi":892,"note":"Luca Rossi note di prova"},
                    {"nome":"Mario","cognome":"Bianchi","eta":62,"citta":"Roma","sesso":"M","millesimi":45235,"note":"Testo lungo di prova note Mario Bianchi nato a Roma 62 anni fa con 45235 millesimi. Testo di esempio per le note. Prova dimensione e contenitore cella"},
                    {"nome":"Andrea","cognome":"Verdi","eta":82,"citta":"Torino","sesso":"M","millesimi":1345,"note":"Testo prova note"},
                    {"nome":"Martina","cognome":"Bruni","eta":16,"citta":"Firenza","sesso":"F","millesimi":523,"note":"Andrea Verdi testo prova note"}
                  ];

    $scope.getHeaderTable = function () {
        return ["NOME","COGNOME","ETA","CITTA","SESSO","NOTE","MILLESIMI"];
    };

    $scope.creaFileDaScaricare = function(){

        $scope.fileExport = new Array();

        for(i=0; i<$scope.data.length; i++){

            app = new Array();
            app.push($scope.data[i].nome);
            app.push($scope.data[i].cognome);
            app.push($scope.data[i].eta);
            app.push($scope.data[i].citta);
            app.push($scope.data[i].sesso);
            app.push($scope.data[i].note);
            app.push($scope.data[i].millesimi);


            $scope.fileExport.push(app);
        }

        console.log("File da scaricare:");
        console.log($scope.fileExport);
        return $scope.fileExport;
    };

    //-------------------------------------------------- PASSAGGIO DATI PER STAMPA -----------------------------------

    $scope.scarica = function(){

        window.open(percorso[0] + '/stampaExcel/handler/StampaHandler.php?name=ALE');

        /*
        $http.post(percorso[0] + '/stampaExcel/handler/StampaHandler.php',
            //{'function': 'stampaExcel', 'Header': $scope.getHeaderTable(), 'Dati': $scope.creaFileDaScaricare()}
            {'function': 'stampaExcel'}
        ).success(function (data, status, headers, config) {
            console.log(data);
        });*/
    };


});