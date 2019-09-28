/**
 * Created by clickale on 06/10/16.
 */

var app = angular.module('myApp', ['ui.bootstrap']);

app.controller('elencoTicketController', function($scope) {
    
    var url = window.location.href;

    if(url.indexOf("username") > -1) {
        $scope.username = /username=([^&]+)/.exec(url)[1];
    }

    $scope.goToNewTicket = function() {
        location.href = '../pagine/nuovoTicket.html?username='+$scope.username;;
    };

    $scope.sortType     = 'titolo'; // set the default sort type
    $scope.sortReverse  = false;  // set the default sort order
    $scope.searchField   = '';     // set the default search/filter term

    $scope.data = [{"titolo":"Bell","id":"K0H 2V5", "data":"2012-04-23T18:25:43.511Z", "descrizione":"descrizione utente Bell", "stato":"aperto", "priorita":1},
                    {"titolo":"Octavius","id":"X1E 6J0", "data":"2013-03-23T18:25:43.511Z", "descrizione":"descrizione utente Octavius", "stato":"aperto", "priorita":5},
                    {"titolo":"Alexis","id":"N6E 1L6", "data":"2016-12-23T18:25:43.511Z", "descrizione":"descrizione utente Alexis", "stato":"chiuso", "priorita":3},
                    {"titolo":"Colton","id":"U4O 1H4", "data":"2016-10-23T18:25:43.511Z", "descrizione":"descrizione utente Colton", "stato":"chiuso", "priorita":1},
                    {"titolo":"Abdul","id":"O9Z 2Q8", "data":"2015-04-23T18:25:43.511Z", "descrizione":"descrizione utente Abdul", "stato":"pending", "priorita":2},
                    {"titolo":"Eden","id":"H8X 5E0", "data":"2011-09-23T18:25:43.511Z", "descrizione":"descrizione utente Eden", "stato":"pending", "priorita":3},
                    {"titolo":"Britanney","id":"I1Q 1O1", "data":"2009-05-23T18:25:43.511Z", "descrizione":"descrizione utente Brittaney", "stato":"aperto", "priorita":5},
                    {"titolo":"Ulric","id":"K5J 1T0", "data":"2013-03-23T18:25:43.511Z", "descrizione":"descrizione utente Ulric", "stato":"chiuso", "priorita":3},
                    {"titolo":"Geraldine","id":"O9K 2M3", "data":"2000-11-23T18:25:43.511Z", "descrizione":"descrizione utente Geraldine", "stato":"pending", "priorita":1},
                    {"titolo":"Hamilton","id":"S1D 3O0", "data":"2012-12-23T18:25:43.511Z", "descrizione":"descrizione utente Hamilton", "stato":"aperto", "priorita":2},
                    {"titolo":"Melissa","id":"H9L 1B7", "data":"2006-07-23T18:25:43.511Z", "descrizione":"descrizione utente Melissa", "stato":"pending", "priorita":3},
                    {"titolo":"Remedios","id":"Z3C 8P4", "data":"2011-08-23T18:25:43.511Z", "descrizione":"descrizione utente Remedios", "stato":"aperto", "priorita":4},                    {"titolo":"Ignacia","id":"K3B 1Q4", "data":"2012-04-23T18:25:43.511Z", "descrizione":"descrizione utente Ignacia", "stato":"chiuso", "priorita":5},
                    {"titolo":"Jaime","id":"V6O 7C9", "data":"2000-01-23T18:25:43.511Z", "descrizione":"descrizione utente Jaime", "stato":"aperto", "priorita":5},
                    {"titolo":"Savannah","id":"L8B 8T1", "data":"2011-02-23T18:25:43.511Z", "descrizione":"descrizione utente Savannah", "stato":"chiuso", "priorita":4},
                    {"titolo":"Declan","id":"D5Q 3I9", "data":"2010-02-23T18:25:43.511Z", "descrizione":"descrizione utente Declan", "stato":"pending", "priorita":3},
                    {"titolo":"Skyler","id":"I0O 4O8", "data":"2014-06-23T18:25:43.511Z", "descrizione":"descrizione utente Skyler", "stato":"chiuso", "priorita":2},
                    {"titolo":"Lawrence","id":"V4K 0L2", "data":"2016-06-23T18:25:43.511Z", "descrizione":"descrizione utente Lawrance", "stato":"aperto", "priorita":1},
                    {"titolo":"Yael","id":"R5E 9D9", "data":"2010-03-23T18:25:43.511Z", "descrizione":"descrizione utente Yael", "stato":"pending", "priorita":2},
                    {"titolo":"Herrod","id":"V5W 6L3", "data":"2011-03-23T18:25:43.511Z", "descrizione":"descrizione utente Herrod", "stato":"aperto", "priorita":3},
                    {"titolo":"Lydia","id":"G0E 2K3", "data":"2011-09-23T18:25:43.511Z", "descrizione":"descrizione utente Lydia", "stato":"pending", "priorita":4},
                    {"titolo":"Tobias","id":"N9P 2V5", "data":"2010-08-23T18:25:43.511Z", "descrizione":"descrizione utente Tobias", "stato":"aperto", "priorita":5},
                    {"titolo":"Wing","id":"T5M 0E2", "data":"2002-04-23T18:25:43.511Z", "descrizione":"descrizione utente Wing", "stato":"pending", "priorita":5},
                    {"titolo":"Callum","id":"L9P 3W5", "data":"2001-11-23T18:25:43.511Z", "descrizione":"descrizione utente Callum", "stato":"chiuso", "priorita":3},
                    {"titolo":"Tiger","id":"R9A 4E4", "data":"2011-11-23T18:25:43.511Z", "descrizione":"descrizione utente Tiger", "stato":"aperto", "priorita":2},
                    {"titolo":"Summer","id":"R4B 4Q4", "data":"2013-12-23T18:25:43.511Z", "descrizione":"descrizione utente Summer", "stato":"chiuso", "priorita":1},
                    {"titolo":"Beverly","id":"M5E 4V4", "data":"2009-10-23T18:25:43.511Z", "descrizione":"descrizione utente Beverly", "stato":"pending", "priorita":5},
                    {"titolo":"Xena","id":"I8G 6O1", "data":"2007-04-23T18:25:43.511Z", "descrizione":"descrizione utente Xena", "stato":"chiuso", "priorita":4},
                    {"titolo":"Ale","id":"APD 140", "data":"1990-12-23T18:25:43.511Z", "descrizione":"descrizione utente Ale", "stato":"aperto", "priorita":1},
                    {"titolo":"Aperto","id":"APD 140", "data":"1990-12-23T18:25:43.511Z", "descrizione":"descrizione utente aperto", "stato":"chiuso", "priorita":1}
    ];

    $scope.viewby = 10;
    $scope.totalItems = $scope.data.length;
    $scope.currentPage = 1;
    $scope.itemsPerPage = $scope.viewby;

    $scope.setPage = function (pageNo) {
        $scope.currentPage = pageNo;
    };

    $scope.setItemsPerPage = function(num) {
        if(num === 'All'){
            $scope.itemsPerPage = $scope.data.length;
        }else{
            $scope.itemsPerPage = num;
            $scope.currentPage = 1; //reset to first paghe
        }
    }

    $scope.clearSearch = function () {
        $scope.search.titolo = '';
        $scope.search.data = '';
        $scope.search.descrizione = '';
        $scope.search.stato = '';
        $scope.search.priorita = '';
    }

    $scope.show = function(item){
        $scope.singleItem = item;
    };

    $scope.clear = function(){
        $scope.singleItem = null;
    };

});