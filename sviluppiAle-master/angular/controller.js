/**
 * Created by clickale on 04/10/16.
 */

var app = angular.module('myApp', []);

app.controller('myCtrl', function($scope) {

    $scope.color = "black";

    $scope.setColor = function(colore) {
        $scope.color = colore;
        scriviCookie('colore', $scope.color, 60)
    };

});

app.controller('myCtrl2', function($scope) {

    $scope.color = leggiCookie('colore');

    $scope.setColor = function(colore) {
        $scope.color = colore;
        scriviCookie('colore', $scope.color, 60)
    };
});

//ALTRE FUNZIONI
function scriviCookie(nomeCookie,valoreCookie,durataCookie)
{
    var scadenza = new Date();
    var adesso = new Date();
    scadenza.setTime(adesso.getTime() + (parseInt(durataCookie) * 60000));
    document.cookie = nomeCookie + '=' + escape(valoreCookie) + '; expires=' + scadenza.toGMTString() + '; path=/';
}

function leggiCookie(nomeCookie)
{
    if (document.cookie.length > 0)
    {
        var inizio = document.cookie.indexOf(nomeCookie + "=");
        if (inizio != -1)
        {
            inizio = inizio + nomeCookie.length + 1;
            var fine = document.cookie.indexOf(";",inizio);
            if (fine == -1) fine = document.cookie.length;
            return unescape(document.cookie.substring(inizio,fine));
        }else{
            return "";
        }
    }
    return "";
}