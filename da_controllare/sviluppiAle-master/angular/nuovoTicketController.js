/**
 * Created by clickale on 12/10/16.
 */

var app = angular.module('myApp', ['ui.bootstrap', 'ngFileUpload']);

/*
app.directive('fileModel', ['$parse', function ($parse) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;

            element.bind('change', function(){
                scope.$apply(function(){
                    modelSetter(scope, element[0].files[0]);
                });
            });
        }
    };
}]);

app.service('fileUpload', ['$http', function ($http) {
    this.uploadFileToUrl = function(file, uploadUrl){
        var fd = new FormData();
        fd.append('file', file);

        $http.post(uploadUrl, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
            .success(function(){
            })
            .error(function(){
            });
    }
}]);*/

app.directive('myDirective', function (httpPostFactory) {
    return {
        restrict: 'A',
        scope: true,
        link: function (scope, element, attr) {

            element.bind('change', function () {
                var formData = new FormData();
                formData.append('file', element[0].files[0]);
                httpPostFactory('../file-upload.php', formData, function (callback) {
                    // recieve image name to use in a ng-src
                    alert("file uploaded");
                    console.log(callback);
                });
            });

        }
    };
});

app.factory('httpPostFactory', function ($http) {
    return function (file, data, callback) {
        $http({
            url: file,
            method: "POST",
            data: data,
            headers: {'Content-Type': undefined}
        }).success(function (response) {
            callback(response);
        });
    };
});

//app.controller('nuovoTicketController', ['$scope', 'fileUpload', function ($scope, fileUpload) {
app.controller('nuovoTicketController', ['$scope', function ($scope) {

    var url = window.location.href;

    $scope.flgShow = false;
    
    if(url.indexOf("username") > -1) {
        $scope.username = /username=([^&]+)/.exec(url)[1];
    }

    $scope.inserisciTicket = function(){
        $scope.flgShow = true;
        console.log($scope.ticket);
    };

    $scope.goToElencoTicket = function() {
        location.href = '../pagine/elencoTicket.html?username='+$scope.username;;
    };

    $scope.nuovoTicket = function(){
        $scope.flgShow = false;
        $scope.ticket = null;
        console.log($scope.ticket);
    };

    /* DATEPICKER */

    $scope.today = function() {
        $scope.data = new Date();
    };

    $scope.today();

    $scope.clear = function() {
        $scope.data = null;
    };

    $scope.options = {
        minDate: new Date(),
        showWeeks: true
    };

    // Disable weekend selection
    function disabled(data) {
        var date = data.date,
            mode = data.mode;
        return mode === 'day' && (date.getDay() === 0 || date.getDay() === 6);
    }

    $scope.toggleMin = function() {
        $scope.options.minDate = $scope.options.minDate ? null : new Date();
    };

    $scope.toggleMin();

    $scope.setDate = function(year, month, day) {
        $scope.data = new Date(year, month, day);
    };


    /* ALLEGATO 
    $scope.uploadFile = function(){
        var file = $scope.myFile;

        console.dir(file);

        var uploadUrl = "../upload.php";
        fileUpload.uploadFileToUrl(file, uploadUrl);
    };
    */
    
}]);
