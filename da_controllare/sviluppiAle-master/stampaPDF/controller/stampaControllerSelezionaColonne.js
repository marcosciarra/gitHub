/**
 * Created by clickale on 04/04/17.
 */

var app = angular.module('myApp', []);

app.controller('stampaControllerSelezionaColonne', function($scope) {

    $scope.visualizzaNome = true;
    $scope.visualizzaCognome = true;
    $scope.visualizzaEta = true;
    $scope.visualizzaCitta = true;
    $scope.visualizzaSesso = true;
    $scope.visualizzaNote = true;
    $scope.visualizzaMillesimi = true;

    //---------------------------------------------------- DATI ----------------------------------------------------


    $scope.data = [{"nome":"Alessandro","cognome":"Pericolo","eta":26,"citta":"Legnano","sesso":"M","millesimi":1000,"note":"Alessandro Pericolo note di prova testo lungo per stampa."},
                    {"nome":"Sara","cognome":"Neri","eta":39,"citta":"Venezia","sesso":"F","millesimi":29472,"note":"Sara Neri note di prova"},
                    {"nome":"Luca","cognome":"Rossi","eta":10,"citta":"Milano","sesso":"M","millesimi":892,"note":"Luca Rossi note di prova"},
                    {"nome":"Mario","cognome":"Bianchi","eta":62,"citta":"Roma","sesso":"M","millesimi":45235,"note":"Testo lungo di prova note Mario Bianchi nato a Roma 62 anni fa con 45235 millesimi. "},
                    {"nome":"Andrea","cognome":"Verdi","eta":82,"citta":"Torino","sesso":"M","millesimi":1345,"note":"Testo prova note"},
                    {"nome":"Martina","cognome":"Bruni","eta":16,"citta":"Firenza","sesso":"F","millesimi":523,"note":"Andrea Verdi testo prova note"},
                    {"nome":"Alessandro","cognome":"Pericolo","eta":26,"citta":"Legnano","sesso":"M","millesimi":1000,"note":"Alessandro Pericolo note di prova testo lungo per stampa. "}
                  ];

    // ------------------------------------------------- PANNELLO ----------------------------------------------------

    $scope.mostraImpostazioniStampa = function () {
        $scope.showImpostazioniStampa = !$scope.showImpostazioniStampa;
    };

    // ------------------------------------------------- DEFAULT ----------------------------------------------------

    $scope.stampa = {
                        //generali
                        'nomeFile' : 'dati',
                        'foglio' : 'A3',
                        'orientamento' : 'L',
                        'tema': 'striped',
                        //margini
                        'margineSinistro' : 12,
                        'margineAlto' : 25,
                        'margineDestro' : 12,
                        'margineBasso' : 25,
                        //font
                        'dimensioneFont' : 10,
                        'tipoFont': 'helvetica',
                        'stileFont' : 'normal',
                        //celle
                        'paddingCella' : 1,
                        'testoCella' : 'linebreak',
                        'larghezzaCella' : 'auto',
                            //'larghezzaCella' : 40
                        //header&footer
                        'dimensioneFontHeader' : 8,
                        'dimensioneFontFooter' : 8

                    };

    //---------------------------------------------------HEADER FOOTER------------------------------------------------

    $scope.hf = false;

    $scope.showSetHeaderFooter = function(headerfooter){
        $scope.hf = headerfooter;
    };

    $scope.getHeaderTable = function () {

        header = new Array;

        if($scope.visualizzaNome){
            header.push("NOME");
        }
        if($scope.visualizzaCognome){
            header.push("COGNOME");
        }
        if($scope.visualizzaEta){
            header.push("ETA");
        }
        if($scope.visualizzaCitta){
            header.push("CITTA");
        }
        if($scope.visualizzaSesso){
            header.push("SESSO");
        }
        if($scope.visualizzaNote){
            header.push("NOTE");
        }
        if($scope.visualizzaMillesimi){
            header.push("MILLESIMI");
        }

        return header;
    };


    // ------------------------------------------------- DOWNLOAD ----------------------------------------------------

    $scope.creaFileDaScaricare = function(){

        $scope.fileExport = new Array();

        for(i=0; i<$scope.data.length; i++){

            app = new Array();
            if($scope.visualizzaNome){
                app.push($scope.data[i].nome);
            }
            if($scope.visualizzaCognome){
                app.push($scope.data[i].cognome);
            }
            if($scope.visualizzaEta){
                app.push($scope.data[i].eta);
            }
            if($scope.visualizzaCitta){
                app.push($scope.data[i].citta);
            }
            if($scope.visualizzaSesso){
                app.push($scope.data[i].sesso);
            }
            if($scope.visualizzaNote){
                app.push($scope.data[i].note);
            }
            if($scope.visualizzaMillesimi){
                app.push($scope.data[i].millesimi);
            }

            $scope.fileExport.push(app);
        }

        console.log("File da scaricare:");
        console.log($scope.fileExport);

        return $scope.fileExport;
    };

    $scope.scaricaPDF = function(){

        console.log($scope.stampa);

        //CREO IL FILE DEFINENDO ORIENTAMENTO E DIMENSIONI FOGLIO
        if($scope.stampa.orientamento === 'L'){
            if($scope.stampa.foglio === 'A3'){
                var doc = new jsPDF($scope.stampa.orientamento, 'mm', [420, 297]);
                $scope.stampa.larghezzaContenitoreDati = 420;
            }
            if($scope.stampa.foglio === 'A4'){
                var doc = new jsPDF($scope.stampa.orientamento, 'mm', [297, 210]);
                $scope.stampa.larghezzaContenitoreDati = 297;
            }
        }
        if($scope.stampa.orientamento === 'P'){
            if($scope.stampa.foglio === 'A3'){
                var doc = new jsPDF($scope.stampa.orientamento, 'mm', [297, 420]);
                $scope.stampa.larghezzaContenitoreDati = 297;
            }
            if($scope.stampa.foglio === 'A4'){
                var doc = new jsPDF($scope.stampa.orientamento, 'mm', [210, 297]);
                $scope.stampa.larghezzaContenitoreDati = 210;
            }
        }


        if($scope.hf){
            var pageContent = function (data) {
                doc.setFontSize($scope.stampa.dimensioneFontHeader);
                doc.text('Ragione Sociale Amministratore', data.settings.margin.left + 5, 10);
                doc.text('via Roma 100, 20025 - Legnano (MI)', data.settings.margin.left + 5, 15);
                doc.text('Telefono: 123456 Fax: 111222333', data.settings.margin.left + 100, 10);
                doc.text('Email: amministratore@gmail.com', data.settings.margin.left + 100, 15);
                doc.text('Professione esercitata ai sensi della legge 14/01/2013, n.4 (G.U. n 22 del 26/01/2013)', data.settings.margin.right + 5, 20);

                // FOOTER
                doc.setFontSize($scope.stampa.dimensioneFontFooter);
                var foot = "Pagina " + data.pageCount + "/";
                doc.text(foot, data.settings.margin.left, doc.internal.pageSize.height - 5);
            };
        }


        var columns = $scope.getHeaderTable();
        var rows = $scope.creaFileDaScaricare();

        doc.autoTable(columns, rows, {

            //aggiungo header e footer
            addPageContent: pageContent,

            theme: $scope.stampa.tema,
            pageBreak: 'avoid', // 'auto', 'avoid'
            tableWidth: $scope.stampa.larghezzaContenitoreDati - ($scope.stampa.margineSinistro + $scope.stampa.margineDestro), // 'auto', 'wrap' or a number, A4:3508*2480 A3:4134*2923

            margin:{
                left: $scope.stampa.margineSinistro,
                top: $scope.stampa.margineAlto,
                right: $scope.stampa.margineDestro,
                bottom: $scope.stampa.margineBasso
            },

            styles: {
                cellPadding: $scope.stampa.paddingCella,
                font: $scope.stampa.tipoFont,
                fontSize: $scope.stampa.dimensioneFont,
                fontStyle: $scope.stampa.stileFont,
                overflow: $scope.stampa.testoCella,
                columnWidth: 'auto'
            },

            columnStyles: {
                0: {
                    columnWidth: 'auto'
                },
                1: {
                    columnWidth: 'auto'
                },
                2: {
                    columnWidth: 'auto'
                },
                3: {
                    columnWidth: 'auto'
                },
                4: {
                    columnWidth: 'auto'
                },
                5: {
                    columnWidth: 'auto'
                },
                6: {
                    columnWidth: 'auto'
                }

                //columnWidth: 150 specificare larghezza in int per fissarla
            }



        });

        doc.save($scope.stampa.nomeFile+'.pdf');
    };



});