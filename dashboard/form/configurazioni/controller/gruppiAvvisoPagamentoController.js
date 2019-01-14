ngApp.controller("gruppiAvvisoPagamentoController", ["$scope", "$http", "$filter", "ngToast", function ($scope, $http, $filter, $ngToast) {

    var url = window.location.href;
    var params = decodeUrl(url);
    stampalog(params);

    $scope.caricamentoCompletato = true;
    $scope.showRefresh = true;
    $scope.showIndietro = false;
    //$scope.preset = {id: -1, descrizione: "scegli..."};

    $scope.init = function () {
        $scope.caricaDati();
    };

    $scope.stampaNomeAnagrafica = function (id) {
        return $filter('filter')($scope.elencoAnagrafichePiva, {id: id})[0].descrizione;
    };

    /******************
     *   CARICADATI   * ================================================================================================
     ******************/

    $scope.caricaDati = function () {

        $http.post(params['form'] + '/configurazioni/controller/gruppiAvvisoPagamentoHandler.php',
            {'function': 'caricaDati'}
        ).then(function (data, status, headers, config) {

            if (data.data.status == 'ko') {
                swal(data.data.error.title, data.data.error.message, 'error');
                return;
            }
            $scope.nuovoGruppoAvvisoPagamento = data.data.newGruppoFatturazione;
            $scope.nuovoGruppoAvvisoPagamento.ultimo_numero = 0;
            var d = new Date();
            $scope.nuovoGruppoAvvisoPagamento.anno = d.getFullYear();
            $scope.nuovoGruppoAvvisoPagamentoApp = angular.copy($scope.nuovoGruppoAvvisoPagamento);

            $scope.elencoAnagrafichePiva = data.data.elencoAnagrafichePiva;

            $scope.elencoGruppiAvvisoPagamento = data.data.elencoGruppiFatturazione;
            stampalog($scope.elencoGruppiAvvisoPagamento);
            $scope.caricamentoCompletato = true;
        });
    };

    /******************
     *   SALVADATI   * ================================================================================================
     ******************/

    $scope.salvaDati = function () {
        stampalog('dati da salvare');
        stampalog($scope.nuovoGruppoAvvisoPagamento);
        $http.post(params['form'] + '/configurazioni/controller/gruppiAvvisoPagamentoHandler.php',
            {'function': 'salvaDati', 'object': $scope.nuovoGruppoAvvisoPagamento}
        ).then(function (data, status, headers, config) {

            if (data.data.status == "ok") {
                stampalog(data.data);
                swal("Salvataggio eseguito", "", "success");
                $scope.elencoGruppiAvvisoPagamento.push($scope.nuovoGruppoAvvisoPagamento);
                $scope.nuovoGruppoAvvisoPagamento = angular.copy($scope.nuovoGruppoAvvisoPagamentoApp);
                $scope.elencoAnagrafichePivaModel = {};

            } else {
                swal("Errore", "Salvataggio non riuscito", "error");

            }

        });
    };


    /*===============================================ELIMINA GRUPPO FATTURAZIONE======================================*/

    $scope.eliminaGruppo = function (id) {
        $http.post(params['form'] + '/configurazioni/controller/gruppiAvvisoPagamentoHandler.php',
            {'function': 'eliminaGruppo', 'id': id}
        ).then(function (data, status, headers, config) {

            if (data.data.status == "ok") {
                stampalog(data.data);
                swal({
                    title: "Eliminazione effettuata",
                    text: '',
                    type: "success"
                }, function () {
                    location.reload();
                });
            } else {
                swal("Errore", "Eliminazione non riuscita", "error");
            }

        });
    };


    /*===============================================MODIFICA GRUPPO FATTURAZIONE======================================*/

    $scope.modificaGruppo = function (id, flagFattura) {
        $http.post(params['form'] + '/configurazioni/controller/gruppiAvvisoPagamentoHandler.php',
            {
                'function': 'modificaGruppo',
                'id': id,
                'flagFattura': flagFattura
            }
        ).then(function (data, status, headers, config) {
            if (data.data.status == "ok") {
                $ngToast.create({
                    className: 'info',
                    content: 'Modificato tipo di gruppo di fatturazione',
                    dismissButton: true,
                    timeout: 1500
                });
            } else {
                swal("Errore", "Modificato non riuscita", "error");
            }

        });
    };


    $scope.modificaNumerazioneZero = function (id, flagNumeroZero) {
        $http.post(params['form'] + '/configurazioni/controller/gruppiAvvisoPagamentoHandler.php',
            {
                'function': 'modificaNumerazioneZero',
                'id': id,
                'flagNumeroZero': flagNumeroZero
            }
        ).then(function (data, status, headers, config) {
            if (data.data.status == "ok") {
                for (var i = 0; i < $scope.elencoGruppiAvvisoPagamento.length; i++) {
                    if($scope.elencoGruppiAvvisoPagamento[i].id==id){
                        $scope.elencoGruppiAvvisoPagamento[i].ultimo_numero=0;
                    }
                }
                $ngToast.create({
                    className: 'info',
                    content: 'Modificato numerazione gruppo di fatturazione',
                    dismissButton: true,
                    timeout: 1500
                });
            } else {
                swal("Errore", "Modificato non riuscita", "error");
            }

        });
    };


    $scope.elencoAnagrafichePivaModel = {};
    $scope.elencoAnagrafichePivaSettings = {
        enableSearch: true,
        selectionLimit: 1,
        externalIdProp: '',
        idProp: 'id',
        displayProp: 'descrizione',
        scrollable: true,
        showCheckAll: false,
        showUncheckAll: true,
        closeOnSelect: true,
        closeOnBlur: true,
        scrollableHeight: '300px',
        smartButtonMaxItems: 1,
        smartButtonTextConverter: function (itemText, originalItem) {
            if (itemText.length > 40) {
                return itemText.substring(0, 40) + "...";
            } else {
                return itemText;
            }
        },
        buttonClasses: 'btn btn-default'
    };
    $scope.customTextMultiselectPIva = {
        buttonDefaultText: 'Seleziona società/ditta individuale',
        uncheckAll: 'Deseleziona',
        searchPlaceholder: 'Cerca società/ditta individuale'
    };
    $scope.elencoAnagrafichePivaEvents = {
        onItemSelect: function (anagrafica) {
            $scope.nuovoGruppoAvvisoPagamento.id_anagrafica = anagrafica.id;
            $scope.descrAnagraficaSelezionata = anagrafica.descrizione;
        }
    };
}]);