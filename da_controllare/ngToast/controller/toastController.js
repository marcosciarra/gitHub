// var app = angular.module("myApp",['ngSanitize','floatDirective','dndLists']);
// app.controller("CorrispondenzaEstrattiContoController",function ($scope, $http,$window){

var app = angular.module("myApp", ['ngAnimate','ngToast']);

// angular.module('myApp')
//     .config(['ngToastProvider', function(ngToast) {
//         ngToast.configure({
//             verticalPosition: 'bottom',
//             horizontalPosition: 'right',
//             animation: 'slide',
//             newestOnTop: true
//         });
//     }]);



app.controller("toastController", ['$scope','ngToast', function ($scope,$ngToast) {

    $scope.test=function(){
        $ngToast.create({
            className: 'info',
            content: 'Questo è un messaggio abbastanza lungo',
            dismissButton: true,
            timeout: 3000
        });
    };

}]);