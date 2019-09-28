var app = angular.module('dscApp', ["ngAnimate"]);

app.controller('anagraficaController',['$scope', '$http', function($scope, $http) {

    var url = decodeUrlTest(window.location.href);
    console.log(url);
    
    onload = function () {

        $scope.getStrutturaAnagrafica();

        $http.post( url['percorso']+ '/form/anagrafica/controller/anagraficaHandler.php',
            {'function': 'getElenchiPerSelect'}
        ).then(function (data, status, headers, config) {
            console.log(data);

            $scope.elencoModalitaPagamento = data.data.modalitaPagamento;
            $scope.elencoListino = data.data.listino;
            $scope.elencoCategoriaSconto = data.data.categoriaSconto;
            $scope.elencoAgente = data.data.agente;
            $scope.elencoZona = data.data.zona;
            $scope.elencoSettoreMerciologico = data.data.settoreMerciologico;
        });
    };

    /* =========================================== STRUTTURA NUOVO CLIENTE ========================================== */

    $scope.getStrutturaAnagrafica = function () {

        $scope.nuovoCliente = importaStrutturaAnagrafica();
    };

    /* ============================================ CAMBIO TIPOLOGIA CLIENTE ======================================== */

    //popolo select tipologia cliente
    $scope.tipologiaCliente = [
                               {'tipo': ' ', 'descrizione': "Azienda"},
                               {'tipo': 'X', 'descrizione': "Ditta individuale"},
                               {'tipo': 'C', 'descrizione': "Privato"}
                              ];

    $scope.cambiaTipologiaCliente = function () {

        //pulisco i campi
        $scope.pulisciCampiCliente();
        $scope.pulisciCampiAltriDati();
        $scope.pulisciCampiConducente();

        //setto variabile riporta dati da cliente a conducente a false
        $scope.riportaDati = false;

        //setto le variabili di controllo validazione a false
        $scope.codiceFiscalePrivato = false;
        $scope.codiceFiscaleDittaIndividuale = false;
        $scope.codiceFiscaleAzienda = false;
        $scope.codiceFiscaleConducente = false;
        $scope.partitaIvaDittaIndividuale = false;
        $scope.partitaIvaAzienda = false;

        //setto la variabile di validazione generale
        $scope.datiValidi = false;

        console.log('RIPORTA DATI: '+ $scope.riportaDati);
    };


    /* ====================================== PASSAGGIO DATI CLIENTE => CONDUCENTE ================================== */

    $scope.riportaDati = false;

    $scope.riportaDatiDaCliente = function(){

        $scope.riportaDati = !$scope.riportaDati;
        //console.log($scope.riportaDati);

        if($scope.riportaDati){
            if($scope.nuovoCliente.tipologia == 'C'){
                $scope.nuovoCliente.datiConducente.intestazione.nome = $scope.nuovoCliente.intestazione.nomeRagioneSociale;
                $scope.nuovoCliente.datiConducente.intestazione.cognome = $scope.nuovoCliente.intestazione.cognomeRagioneSocialeEst;
            }else{
                $scope.nuovoCliente.datiConducente.intestazione.nome = $scope.nuovoCliente.datiPrivati.nome;
                $scope.nuovoCliente.datiConducente.intestazione.cognome = $scope.nuovoCliente.datiPrivati.cognome;
            }
            //riportando il codice fiscale controllo che sia corretto (se si riportoi e valido)
            if(controllaCF($scope.nuovoCliente.datiFiscali.codiceFiscale)){
                $scope.nuovoCliente.datiConducente.intestazione.codiceFiscale = $scope.nuovoCliente.datiFiscali.codiceFiscale;
                $scope.codiceFiscaleConducente = true;
            }
            $scope.nuovoCliente.datiConducente.indirizzo.indirizzo = $scope.nuovoCliente.indirizzo.indirizzo;
            $scope.nuovoCliente.datiConducente.indirizzo.cap = $scope.nuovoCliente.indirizzo.cap;
            $scope.nuovoCliente.datiConducente.indirizzo.citta = $scope.nuovoCliente.indirizzo.citta;
            $scope.nuovoCliente.datiConducente.indirizzo.provincia = $scope.nuovoCliente.indirizzo.provincia;
            $scope.nuovoCliente.datiConducente.indirizzo.stato = $scope.nuovoCliente.indirizzo.stato;
            $scope.nuovoCliente.datiConducente.contatti.telefono = $scope.nuovoCliente.contatti.telefono;
            $scope.nuovoCliente.datiConducente.contatti.cellulare = $scope.nuovoCliente.contatti.cellulare;
            $scope.nuovoCliente.datiConducente.contatti.email = $scope.nuovoCliente.contatti.email;
            $scope.nuovoCliente.datiConducente.contatti.fax = $scope.nuovoCliente.contatti.fax;
            $scope.nuovoCliente.datiConducente.datiPrivati.luogoNascita = $scope.nuovoCliente.datiPrivati.luogoNascita;
            $scope.nuovoCliente.datiConducente.datiPrivati.dataNascita = $scope.nuovoCliente.datiPrivati.dataNascita;
            $scope.nuovoCliente.datiConducente.datiPrivati.provinciaNascita = $scope.nuovoCliente.datiPrivati.provinciaNascita;
            $scope.nuovoCliente.datiConducente.datiPrivati.statoNascita = $scope.nuovoCliente.datiPrivati.statoNascita;
            $scope.nuovoCliente.datiConducente.datiPrivati.sesso = $scope.nuovoCliente.datiPrivati.sesso;

        }else{

            $scope.pulisciCampiConducente();

            $scope.codiceFiscaleConducente = false;

        }

        //dopo che ho riportato i dati controllo quelli required
        $scope.verificaDatiObbligatori();
    };

    $scope.copyPIVAinCF = function () {
        $scope.nuovoCliente.datiFiscali.codiceFiscale = $scope.nuovoCliente.datiFiscali.partitaIva;
        $scope.codiceFiscaleAzienda = controllaPIVA($scope.nuovoCliente.datiFiscali.codiceFiscale);
    };


    /* ====================================== VERIFICA CODICE FISCALE - PARTITA IVA ================================= */

    // ----- CODICE FISCALE

    $scope.codiceFiscalePrivato = false;
    $scope.codiceFiscaleDittaIndividuale = false;
    $scope.codiceFiscaleAzienda = false;
    $scope.codiceFiscaleConducente = false;

    $scope.verificaCodiceFiscalePrivato = function (cfField) {

        $scope.codiceFiscalePrivato = controllaCF(cfField);
    };

    $scope.verificaCodiceFiscaleDittaIndividuale = function (cdField) {

        $scope.codiceFiscaleDittaIndividuale = controllaCF(cdField);
    };

    $scope.verificaCodiceFiscaleAzienda = function (cfField) {

        $scope.codiceFiscaleAzienda = controllaPIVA(cfField);
    };

    $scope.verificaCodiceFiscaleConducente = function (cfField){

        $scope.codiceFiscaleConducente = controllaCF(cfField);
    };

    // ----- PARTITA IVA

    $scope.partitaIvaDittaIndividuale = false;
    $scope.partitaIvaAzienda = false;

    $scope.verificaPartitaIvaDittaIndividuale = function (pivaField) {

        $scope.partitaIvaDittaIndividuale = controllaPIVA(pivaField);
    };

    $scope.verificaPartitaIvaAzienda = function (pivaField) {

        $scope.partitaIvaAzienda = controllaPIVA(pivaField);
    };

    // ----- EMAIL


    $scope.emailValida = false;

    $scope.verificaEmail = function(emailField){

        $scope.emailValida = controllaEmail(emailField);
    };

    /* ========================================= VERIFICA DATI REQUIRED ============================================= */

    $scope.datiValidi = false;

    $scope.verificaDatiObbligatori = function(){

        //PRIVATO
        if($scope.nuovoCliente.tipologia == 'C'){

            /*
            console.log($scope.nuovoCliente.intestazione.nomeRagioneSociale);
            console.log($scope.nuovoCliente.intestazione.cognomeRagioneSocialeEst);
            console.log($scope.codiceFiscalePrivato);
            console.log($scope.codiceFiscaleConducente);
            */

            if($scope.nuovoCliente.intestazione.nomeRagioneSociale != '' &&
               $scope.nuovoCliente.intestazione.cognomeRagioneSocialeEst != '' &&
               $scope.codiceFiscalePrivato &&
               $scope.codiceFiscaleConducente){

                $scope.datiValidi = true;

            }else{

                $scope.datiValidi = false;
            }
        }
        //DITTA INDIVIDUALE
        else if($scope.nuovoCliente.tipologia == 'X'){

            /*
            console.log($scope.nuovoCliente.intestazione.nomeRagioneSociale);
            console.log($scope.codiceFiscaleDittaIndividuale);
            console.log($scope.partitaIvaDittaIndividuale);
            console.log($scope.nuovoCliente.datiPrivati.nome);
            console.log($scope.nuovoCliente.datiPrivati.cognome);
            console.log($scope.nuovoCliente.datiPrivati.dataNascita);
            console.log($scope.nuovoCliente.datiPrivati.luogoNascita);
            console.log($scope.nuovoCliente.datiPrivati.provinciaNascita);
            console.log($scope.nuovoCliente.datiPrivati.statoNascita);
            console.log($scope.codiceFiscaleConducente);
            */

            if($scope.nuovoCliente.intestazione.nomeRagioneSociale != '' &&
               $scope.codiceFiscaleDittaIndividuale &&
               $scope.partitaIvaDittaIndividuale &&
               $scope.nuovoCliente.datiPrivati.nome != '' &&
               $scope.nuovoCliente.datiPrivati.cognome != '' &&
               $scope.nuovoCliente.datiPrivati.dataNascita != '' && $scope.nuovoCliente.datiPrivati.dataNascita != null &&
               $scope.nuovoCliente.datiPrivati.luogoNascita != '' &&
               $scope.nuovoCliente.datiPrivati.provinciaNascita != '' &&
               $scope.nuovoCliente.datiPrivati.statoNascita != '' &&
               $scope.codiceFiscaleConducente){

                $scope.datiValidi = true;

            }else{

                $scope.datiValidi = false;
            }

        }
        //AZIENDA
        else if($scope.nuovoCliente.tipologia == ' '){

            /*
            console.log($scope.nuovoCliente.intestazione.nomeRagioneSociale);
            console.log($scope.codiceFiscaleAzienda);
            console.log($scope.partitaIvaAzienda);
            console.log($scope.codiceFiscaleConducente);
            */

            if($scope.nuovoCliente.intestazione.nomeRagioneSociale != '' &&
                $scope.codiceFiscaleAzienda &&
                $scope.partitaIvaAzienda &&
                $scope.codiceFiscaleConducente){

                $scope.datiValidi = true;

            }else{

                $scope.datiValidi = false;
            }

        }
        else{
            $scope.datiValidi = false;
        }
        
    };

    /* ============================================== PULIZIA CAMPI ================================================= */

    $scope.pulisciCampiCliente = function () {

        $scope.nuovoCliente.intestazione.nomeRagioneSociale = "";
        $scope.nuovoCliente.intestazione.cognomeRagioneSocialeEst = "";
        $scope.nuovoCliente.datiFiscali.codiceFiscale = "";
        $scope.nuovoCliente.datiFiscali.partitaIva = "";
        $scope.nuovoCliente.indirizzo.indirizzo = "";
        $scope.nuovoCliente.indirizzo.indirizzoEst = "";
        $scope.nuovoCliente.indirizzo.cap = "";
        $scope.nuovoCliente.indirizzo.citta = "";
        $scope.nuovoCliente.indirizzo.provincia = "";
        $scope.nuovoCliente.indirizzo.stato = "";
        $scope.nuovoCliente.contatti.telefono = "";
        $scope.nuovoCliente.contatti.cellulare = "";
        $scope.nuovoCliente.contatti.email = "";
        $scope.nuovoCliente.contatti.fax = "";
        $scope.nuovoCliente.contatti.referenteAziendale = "";
        $scope.nuovoCliente.contatti.pec = "";
        $scope.nuovoCliente.contatti.sito = "";
        $scope.nuovoCliente.datiPrivati.nome = "";
        $scope.nuovoCliente.datiPrivati.cognome = "";
        $scope.nuovoCliente.datiPrivati.cognome = "";
        $scope.nuovoCliente.datiPrivati.luogoNascita = "";
        $scope.nuovoCliente.datiPrivati.provinciaNascita = "";
        $scope.nuovoCliente.datiPrivati.statoNascita = "";
        $scope.nuovoCliente.datiPrivati.dataNascita = "";
        $scope.nuovoCliente.datiPrivati.sesso = "";

    };

    $scope.pulisciCampiAltriDati = function () {

        $scope.nuovoCliente.altriDati.modalitaPagamento = "";
        $scope.nuovoCliente.altriDati.listino = "";
        $scope.nuovoCliente.altriDati.categoriaSconto = "";
        $scope.nuovoCliente.altriDati.agente = "";
        $scope.nuovoCliente.altriDati.areaGeografica = "";
        $scope.nuovoCliente.altriDati.settoreMerciologico = "";
    };

    $scope.pulisciCampiConducente = function () {

        $scope.nuovoCliente.datiConducente.intestazione.nome = "";
        $scope.nuovoCliente.datiConducente.intestazione.cognome = "";
        $scope.nuovoCliente.datiConducente.intestazione.codiceFiscale = "";
        $scope.nuovoCliente.datiConducente.indirizzo.indirizzo = "";
        $scope.nuovoCliente.datiConducente.indirizzo.cap = "";
        $scope.nuovoCliente.datiConducente.indirizzo.citta = "";
        $scope.nuovoCliente.datiConducente.indirizzo.provincia = "";
        $scope.nuovoCliente.datiConducente.indirizzo.stato = "";
        $scope.nuovoCliente.datiConducente.contatti.telefono = "";
        $scope.nuovoCliente.datiConducente.contatti.cellulare = "";
        $scope.nuovoCliente.datiConducente.contatti.email = "";
        $scope.nuovoCliente.datiConducente.contatti.fax = "";
        $scope.nuovoCliente.datiConducente.datiPrivati.luogoNascita = "";
        $scope.nuovoCliente.datiConducente.datiPrivati.dataNascita = "";
        $scope.nuovoCliente.datiConducente.datiPrivati.provinciaNascita = "";
        $scope.nuovoCliente.datiConducente.datiPrivati.statoNascita = "";
        $scope.nuovoCliente.datiConducente.datiPrivati.sesso = "";
    };
    
    /* ================================================ SALVATAGGIO ================================================= */

    $scope.salva = function () {

        console.log($scope.nuovoCliente);

        //converto le date da formato js a string YYYYMMDD
        $scope.nuovoCliente.datiPrivati.dataNascita = $scope.nuovoCliente.datiPrivati.dataNascita.yyyymmdd();
        $scope.nuovoCliente.datiConducente.datiPrivati.dataNascita = $scope.nuovoCliente.datiConducente.datiPrivati.dataNascita.yyyymmdd();
        $scope.nuovoCliente.datiConducente.patente.dataEmissione = $scope.nuovoCliente.datiConducente.patente.dataEmissione.yyyymmdd();
        $scope.nuovoCliente.datiConducente.patente.dataScadenza = $scope.nuovoCliente.datiConducente.patente.dataScadenza.yyyymmdd();

        $http.post( url['percorso']+ '/form/anagrafica/controller/anagraficaHandler.php',
            {'function': 'salvaNuovoCliente', 'nuovoCliente': $scope.nuovoCliente}
        ).then(function (data, status, headers, config) {
            console.log(data);
        });
    };

}]);