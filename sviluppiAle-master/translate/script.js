var translationsEN = {
    BUTTON_LANG_IT: 'Italian',
    BUTTON_LANG_EN: 'English',
    labelNome: 'Name'
};

var translationsIT= {
    BUTTON_LANG_IT: 'Italiano',
    BUTTON_LANG_EN: 'Inglese',
    labelNome: 'Nome'
};

var app = angular.module('myApp', ['pascalprecht.translate']);

app.config(['$translateProvider', function ($translateProvider) {
    // add translation tables
    $translateProvider.translations('en', translationsEN);
    $translateProvider.translations('it', translationsIT);
    $translateProvider.fallbackLanguage('it');
    $translateProvider.preferredLanguage('it');
}]);

app.controller('Ctrl', ['$translate', '$scope', function ($translate, $scope) {

    $scope.changeLanguage = function (langKey) {
        $translate.use(langKey);
    };
}]);