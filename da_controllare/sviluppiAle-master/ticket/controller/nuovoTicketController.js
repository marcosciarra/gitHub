/**
 * Created by alessandro on 19/06/17.
 */

var app = angular.module('dscApp', []);

app.controller('nuovoTicketController', function($scope) {


    /* oggetto ticket vuoto*/
    $scope.ticket = {
                    'idCliente' : null,
                    'username' : null,
                    'titoloTicket' : null,
                    'descrizioneTicket': null,
                    'tipologiaTicket': null,
                    'prioritaTicket': null
                    };


    $scope.ticket.idCliente = 12345;

    var url = window.location.href;

    if(url.indexOf("username") > -1) {
        $scope.ticket.username = /username=([^&]+)/.exec(url)[1];
    }

    $scope.inserimentoEffettuato = false;

    $scope.tipologia = [
                            {
                                "ID":"SW",
                                "DESCRIZIONE":"SOFTWARE"
                            },
                            {
                                "ID":"HW",
                                "DESCRIZIONE":"HARDWARE"
                            }
                        ];

    $scope.priorita = [
                            {
                                "ID":1,
                                "DESCRIZIONE":"Molto bassa"
                            },
                            {
                                "ID":2,
                                "DESCRIZIONE":"Bassa"
                            },
                            {
                                "ID":3,
                                "DESCRIZIONE":"Media"
                            },
                            {
                                "ID":4,
                                "DESCRIZIONE":"Alta"
                            },
                            {
                                "ID":5,
                                "DESCRIZIONE":"Molto alta"
                            }
                    ];


    $scope.goToElencoTicket = function () {

        location.href = '../anagrafica/elencoTicket.html?username='+$scope.ticket.username;;
    };

    $scope.inserisciTicket = function(){

        console.log($scope.ticket);
        $scope.inserimentoEffettuato = true;

        //redirect to elencoTicket
        setTimeout(function(){
            window.location.href = '../anagrafica/elencoTicket.html?username='+$scope.ticket.username;;
        }, 1000);

    };

});//close controller


