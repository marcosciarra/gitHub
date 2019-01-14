ngApp.controller("tipiCauzioniController", ["$scope", "$http", function($scope, $http) {

    var url = window.location.href;
    var params = decodeUrl(url);
    stampalog(params);

    $scope.caricamentoCompletato = false;
    $scope.showRefresh = true;
    $scope.showIndietro = true;

    /******************
     *   CARICADATI   * ================================================================================================
     ******************/

    $scope.init=function () {
        $scope.caricaDati();
    };

    $scope.caricaDati = function(){
        stampalog(params['form'] + '/configurazioni/controller/tipiCauzioneHandler.php');
        $http.post(params['form'] + '/configurazioni/controller/tipiCauzioneHandler.php',
            {'function': 'caricaDati'}
        ).then(function (data, status, headers, config) {
            stampalog(data.data);
            $scope.elencoTipiCauzione = data.data.elencoTipiCauzione;
            $scope.newTipoCauzione = angular.copy(data.data.newTipoCauzione);
            $scope.newTipoCauzioneApp = angular.copy(data.data.newTipoCauzione);
            $scope.caricamentoCompletato = true;
        });
    };

    /******************
     *   SALVADATI   * ================================================================================================
     ******************/

    $scope.controllaCampiTipoCauzione = function(tipoCauzione) {
        if(
            tipoCauzione != undefined &&
            tipoCauzione.descrizione != null &&
            tipoCauzione.descrizione.length > 0 &&
            tipoCauzione.descrizione.length <= 45
        ){
            return true;
        }
        return false;
    };

    $scope.salvaDati = function (obj, salva){
        $http.post(params['form'] + '/configurazioni/controller/tipiCauzioneHandler.php',
            {'function': 'salvaDati', 'object': obj}
        ).then(function (data, status, headers, config) {
            stampalog(data);

            if(data.data.status == "ok"){
                if(salva) {
                    $scope.elencoTipiCauzione.push(obj);
                    $scope.newTipoCauzione = angular.copy($scope.newTipoCauzioneApp);
                }
                swal("Salvataggio eseguito", "" , "success");
            }else {
                swal("Errore", "Salvataggio non riuscito", "error");
            }

        });
    };

    /******************
     *   ELIMINA   * ================================================================================================
     ******************/

    $scope.eliminaDati = function (id){

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
            function(){
                $http.post(params['form'] + '/configurazioni/controller/tipiCauzioneHandler.php',
                    {'function': 'eliminaDati', 'id': id}
                ).then(function (data, status, headers, config) {
                    stampalog(data);
                    location.reload();
                });
            });
    }
}]);