var demoApp = angular.module('demoApp', ['angular-clipboard']);

demoApp.controller('DemoCtrl', ['$scope', function ($scope) {

    $scope.textToCopy = 'I can copy by clicking!\nAnd also new lines!';
    $scope.success = function () {
        console.log('Copied!');
    };
    $scope.fail = function (err) {
        console.error('Error!', err);
    };

    $scope.contatti = [{'nome': 'ale', 'email': 'ale@gmail.com'},
                       {'nome': 'gigi', 'email': 'gigi@gmail.com'},
                       {'nome': 'test', 'email': 'test@gmail.com'},
                       {'nome': 'suka', 'email': 'suka@gmail.com'},
                       {'nome': 'xxx', 'email': 'xxx@gmail.com'}]
}]);