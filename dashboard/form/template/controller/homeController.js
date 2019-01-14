var ngApp = angular.module('affittiApp', ["ngAnimate", "angularjs-dropdown-multiselect", "ngSanitize", "ngCsv", "angularjs-gauge", "angularFileUpload", "ngToast", "ngCookies"]);

ngApp.controller('homeController', ["$scope", "$http", function ($scope, $http) {

    $scope.params = decodeUrl(window.location.href);
    // stampalog($scope.params);
    $scope.mostraAvvisoCambioPwd = false;

    // BOTTONI indietro/refresh
    $scope.showRefresh = false;
    $scope.aggiornaPagina = function () {
        swal({
                title: "Sei sicuro di voler aggiornare?",
                text: "Se non hai salvato perderai le modifiche apportate!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Si!",
                cancelButtonText: "No!",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    location.reload();
                }
            });
    };
    $scope.showIndietro = false;
    $scope.indietro = function (sezione) {
        window.location.href = $scope.params['home'] + encodeUrl(sezione, sezione);
    };

    //di default 1 pagina
    $scope.import = "cruscotto/cruscotto.html";

    onload = function () {
        //chiamo la funzione per capire che pagina devo includere
        $scope.getPageIncluded();

        $http.post($scope.params['form'] + "/template/controller/homeHandler.php",
            {'function': 'getUsernameUserLogged'}
        ).then(function (data, status, headers, config) {
            if (data.data == 'logout') {
                location.href = $scope.params['baseurl'];
            }
            else {
                localStorage.setItem("tipoUtente", data.data.tipoUtente);
                $scope.username = data.data.username;
            }
        });

        $scope.livelloUtente();
        // $scope.controlliLogin();
        $scope.caricaAvvisi();
    };

    //recupero dall'url il parametro che specifica quale pagina devo includere e lo salvo nella variabile che passo all' ng-include ($scope.pagina)
    $scope.getPageIncluded = function () {

        var newUrl = new URL(window.location.href);
        var pagina = newUrl.searchParams.get("pagina");
        var sezione = newUrl.searchParams.get("sezione");
        if (sezione != null && pagina != null) {
            //importo l'html
            $scope.import = sezione + '/' + pagina + ".html";
        }
    };

    /**
     * - dalla pagina passo la stringa che identifica la pagina in cui voglio navigare
     * - passo questa variabile all'ng-include
     * - eseguo il redirect al nuovo url per richiamare l'onload e rieseguire il giro di import
     */
    $scope.includePage = function (sezione, pagina) {
        $scope.import = sezione + '/' + pagina + ".html";
        window.location.href = $scope.params['home'] + encodeUrl(sezione, pagina);
    };

    /*===== GESTIONE LIVELLO UTENTI =====*/

    $scope.livelloUtente = function () {
        $http.post($scope.params['form'] + "/template/controller/homeHandler.php",
            {'function': 'getLevelUserLogged'}
        ).then(function (data, status, headers, config) {
            $scope.livello = data.data;
        });
    };

    /*===== CONTROLLI VARI (scadenza pwd ...) =====*/

    $scope.controlliLogin = function () {
        $http.post($scope.params['form'] + "/template/controller/homeHandler.php",
            {'function': 'controlliLogin'}
        ).then(function (data, status, headers, config) {
            $scope.passwordValStatus = data.data;
            if ($scope.passwordValStatus < 15) {
                $scope.mostraAvvisoCambioPwd = true;
            }
            if ($scope.passwordValStatus <= 0) {
                $http.post($scope.params['form'] + "/template/controller/homeHandler.php",
                    {'function': 'getIdUserLogged'}
                ).then(function (data, status, headers, config) {
                    //stampalog(data.data);
                    var userID = data.data;
                    //window.location.href = $scope.params['home'] + encodeUrl("configurazioni", "gestioneUtente", userID);
                });
            }
        });
    };

    /*===== GESTIONE VISUALIZZAZIONE MENU LATERALE =====*/

    //$scope.visible = true;
    if (localStorage.getItem("menu") == true || localStorage.getItem("menu") == 'true') {
        $scope.visible = true;
    }
    else {
        $scope.visible = false;
    }
    $scope.mostraMenu = function () {
        if (localStorage.getItem("menu") == true || localStorage.getItem("menu") == 'true') {
            $scope.visible = false;
            localStorage.setItem("menu", false);
        }
        else {
            $scope.visible = true;
            localStorage.setItem("menu", true);
        }
    };

    /*===== LOGOUT =====*/

    $scope.logout = function () {

        $http.post($scope.params['form'] + "/template/controller/homeHandler.php",
            {'function': 'effettuaLogout'}
        ).then(function (data, status, headers, config) {
            if (data.data == '1') {
                stampalog('Sessione pulita');
                location.href = $scope.params['baseurl'];
            } else {
                stampalog('Errore session destroy');
            }

        });
    };

    $scope.cambiaPassword = function () {
        $http.post($scope.params['form'] + "/template/controller/homeHandler.php",
            {'function': 'getIdUserLogged'}
        ).then(function (data, status, headers, config) {
            //stampalog(data.data);
            var userID = data.data;
            window.location.href = $scope.params['home'] + encodeUrl("configurazioni", "gestioneUtente", userID);
        });
    };

    $scope.contatti = function () {
        swal({
            title: "",
            text:
                "www.clicksrl.eu\n\n" +
                "Assistenza:\naffitti@clicksrl.eu\n0331 18 16 589\n\n" +
                "Commerciale:\ninfo@clicksrl.eu\n0331 18 16 581\n\n" +
                "Orari: \n9:30 - 12:30\n14:30 - 17:30",
            imageUrl: '../grafica/img/Logo-Click.png'
        });
    }

    /*===== GESTIONE LIVELLO UTENTI =====*/

    $scope.caricaAvvisi = function () {
        $http.post($scope.params['form'] + "/template/controller/homeHandler.php",
            {'function': 'caricaAvvisi'}
        ).then(function (data, status, headers, config) {
            $scope.avvisi = data.data.avvisi;
        });
    };

}]);
