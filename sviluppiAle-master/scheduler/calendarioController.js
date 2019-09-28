var ngApp = angular.module('aleApp', ['daypilot']);

ngApp.controller('calendarioController', ['$scope', '$http', function ($scope, $http) {

    /*----------------------------URL&PARAMS----------------------------*/
    var url = window.location.href;
    var percorso = url.split("/form");

    /*----------------------------------------------IMPOSTAZIONI NAVIGATOR------------------------------------------------*/


    $scope.navigatorConfig = {
        locale: "it-it",
        selectMode: "month",
        showMonths: 3,
        skipMonths: 3,

        //------------------------------AZIONI NAVIGATORE------------------------------------

        //seleziono il mese e modifico la data di inzio dello schedulatore
        onTimeRangeSelected: function(args) {

            //"scheduler" è l'id dell'oggetto schedulatore nella pagina html
            $scope.scheduler.startDate=args.start;
            $scope.scheduler.update();
        }
    };

    /*---------------------------------------------IMPOSTAZIONI SCHEDULER-------------------------------------------------*/

    $scope.schedulerConfig = {
        locale: "it-it",
        theme: "schedulercsscustom",
        scale: "Day",
        //setta quanti giorni visualizzare dallo startDate
        days: 365,
        //bubble: new DayPilot.Bubble(),
        startDate: new DayPilot.Date().firstDayOfMonth(),
        //eventi schedulatore
        onEventMoved: function(args) {
            $scope.dp.message("Event moved: " + args.e.text());
        },
        eventClickHandling: "Select",
        onEventSelected: function(args) {
            $scope.$apply(function() {
                $scope.selectedEvents = $scope.dp.multiselect.events();
            });
        },
        //gestione tasto dx sull'evento
        contextMenu: new DayPilot.Menu({items: [
            {text:"Show event ID", onclick: function() {alert("Event value: " + this.source.value());} },
            {text:"Show event text", onclick: function() {alert("Event text: " + this.source.text());} },
            {text:"Show event start", onclick: function() {alert("Event start: " + this.source.start().toStringSortable());} },
            {text:"Delete", onclick: function() { $scope.dp.events.remove(this.source); } },
            {text:"Disabled menu item", onclick: function() { alert("disabled")}, disabled: true }
        ]}),
        //divisione tempo schedulatore
        timeHeaders: [
            { groupBy: "Month" },
            { groupBy: "Day", format: "dd"}
        ],
        //definizione intestazioni
        rowHeaderColumns: [
            {title: "Mezzo", width: 80},
            {title: "Sede", width: 80},
            {title: "Avviso", width: 80}
        ],
        //le recuperiamo da db
        resources: [
            {"id":"1","name":"BMW","sede":"Milano","avviso":"libero"},
            {"id":"2","name":"Ducati","sede":"Venezia","avviso":"occupato"},
            {"id":"3","name":"Garelli","sede":"Firenze","avviso":"libero"},
            {"id":"4","name":"il CIAO","sede":"Roma","avviso":"occupato"},
            {"id":"5","name":"il SI","sede":"Rozzangeles","avviso":"rubbbato"}
        ],

        //------------------------------AZIONI SCHEDULATORE------------------------------------

        //match tra le intestazioni e i dettagli aggiuntivi delle risorse (parte in automatico)
        onBeforeResHeaderRender: function(args) {

            args.resource.columns[0].html = args.resource.sede;
            args.resource.columns[1].html = args.resource.avviso;
            switch (args.resource.status) {
                case "rubbbato":
                    args.resource.cssClass = "rubbbato";
                    break;
                case "libero":
                    args.resource.cssClass = "libero";
                    break;
                case "occupato":
                    args.resource.cssClass = "occupato";
                    break;
            }
        },
        //creo un evento selezionando un range di tempo sullo schedulatore
        onTimeRangeSelected: function(args) {

            var name = prompt("New event name:", "Event");
            if (!name) return;
            var e = new DayPilot.Event({
                start: args.start,
                end: args.end,
                id: DayPilot.guid(),
                resource: args.resource,
                text: "Event"
            });
            $scope.events.events.push(e);
            clearSelection();
            message("Created");
            /* sweetalert manca la libreria
            console.log(args);
            swal({
                    title:"Nuovo Contratto",
                    text:"Stai per effettuare una prenotazione per il periodo selezionato. Continuare la procedura di compilazione del contratto? ",
                    type:"success",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Si, continua!!",
                    cancelButtonText: "No!"
                },
                function(){
                    window.location.href=percorso[0] +  '/form/home.php?sezione=contratto&pagina=nuovoContratto' ;
                })
                */
        }

    };

    //eventi dello schedulatore (le prenotazioni) ==> recuperare da db
    $scope.events = [
        {
            start: new DayPilot.Date("2017-12-12T00:00:00"),
            end: new DayPilot.Date("2017-12-12T00:00:00"),
            id: DayPilot.guid(),
            resource: "1",
            text: "One-Day Event",
            bubbleHtml: "tua mamma"
        },
        {
            start: new DayPilot.Date("2017-11-05T00:00:00"),
            end: new DayPilot.Date("2017-12-06T00:00:00"),
            id: DayPilot.guid(),
            resource: "2",
            text: "One-Day Event",
            bubbleHtml: "suca"
        }
    ];

    //------------------------------FUNZIONI SCHEDULATORE------------------------------------

    $scope.add = function() {
        $scope.events.push(
            {
                start: new DayPilot.Date("2014-09-05T00:00:00"),
                end: new DayPilot.Date("2014-09-06T00:00:00"),
                id: DayPilot.guid(),
                resource: "B",
                text: "One-Day Event",
                bubbleHtml: "Details"
            }
        );
    };

    $scope.move = function() {
        var event = $scope.events[0];
        event.start = event.start.addDays(1);
        event.end = event.end.addDays(1);
    };

    $scope.rename = function() {
        $scope.events[0].text = "New name";
    };

    $scope.scrollTo = function(date) {
        $scope.dp.scrollTo(date);
    };

    $scope.scale = function(val) {
        $scope.config.scale = val;
    };


}]);//CLOSE APP



