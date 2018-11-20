// var app = angular.module("myApp",['ngSanitize','floatDirective','dndLists']);
// app.controller("CorrispondenzaEstrattiContoController",function ($scope, $http,$window){

var app = angular.module("myApp", ['ngAnimate', 'ngWYSIWYG']);

// angular.module('myApp')
//     .config(['ngToastProvider', function(ngToast) {
//         ngToast.configure({
//             verticalPosition: 'bottom',
//             horizontalPosition: 'right',
//             animation: 'slide',
//             newestOnTop: true
//         });
//     }]);


// app.controller("wysiwygController", ['$scope', 'ngWYSIWYG', function ($scope, ngWYSIWYG) {
//
//
//     $scope.your_variable = 'testo HTML qui qui';
//
// }]);

// app.controller('wysiwygController', ['$scope', '$q', '$timeout', function ($scope, $q, $timeout) {
//     $scope.content = '<h1>Hello world!</h1>';
//     $scope.editorConfig = {
//         fontAwesome: true
//     };
//     $scope.api = {
//         scope: $scope
//     };
//     $scope.$watch('content', function(newValue) {
//         $log.info(newValue);
//     });
// }]);

app.controller('wysiwygController', ['$scope', '$q', '$timeout', function ($scope, $q, $timeout) {
    $scope.content = 'Questa è una prova di testo';
    $scope.api = {
        scope: $scope
    }
    $scope.editorConfig = {
        sanitize: false,
        toolbar: [
            {name: 'basicStyling',items: ['bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', '-', 'leftAlign', 'centerAlign', 'rightAlign', 'blockJustify', '-']},
            {name: 'paragraph', items: ['orderedList', 'unorderedList', 'outdent', 'indent', '-']},
            // {name: 'doers', items: ['removeFormatting', 'undo', 'redo', '-']},
            {name: 'colors', items: ['fontColor', 'backgroundColor', '-']},
            // {name: 'links', items: ['image', 'hr', 'symbols', 'link', 'unlink', '-']},
            {name: 'tools', items: ['print', '-']},
            // {name: 'styling', items: ['font', 'size', 'format']},
        ]
    };
    // console.log($scope);


    $scope.addTag = function (id) {
        $scope.content = $scope.content + id;
    }

    //FIXME::CREARE FUNZIONE PER CONTROLLARE SE DISABILITARE PULSANTE PERCHE' GIA' USATO

}])
;