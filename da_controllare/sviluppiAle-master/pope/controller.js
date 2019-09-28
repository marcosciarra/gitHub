
var app = angular.module("myApp", ['ngAnimate']); /*definisco l'applicazione a cui passo il modulo ngAnimate*/

app.controller("pageController",['$scope', function($scope){ /*definisco il controller e la variabile di $scope*/

//ESEMPIO 1: definisco una variabile di $scope che sarà accessibile nella pagina html
$scope.hello = 'Ciao Pope';

//ESEMPIO 2: accedo/scrivo una variabile di $scope dalla pagina
$scope.firstname = "Silvia";
$scope.lastname = "";

//ESEMPIO 3: definisco una function che richiamo all' ng-click dalla pagina di front
$scope.mostraNomeCognome = function(){
	alert('Nome: ' + $scope.firstname + ' Cognome: '+ $scope.lastname);
	console.log('Nome: ' + $scope.firstname + ' Cognome: '+ $scope.lastname);
};

//ESEMPIO 4: json
$scope.persone=[
				{"nome": "Silvia", "cognome": "Biral", "age": 27},
				{"nome": "Elisa", "cognome": "Losito", "age": 26},
				{"nome": "Alessandro", "cognome": "Pericolo", "age": 26},
				{"nome": "Roberto", "cognome": "Pedone", "age": 26}
				];

$scope.reset = function(){

	$scope.search = {"nome": "", "cognome": "", "age": ""};
    $scope.personaSelezionata = null;
};

//creo un oggetto vuoto per un nuovo inserimento
$scope.new = {"nome": "", "cognome": "", "age": ""};

//inserisco in persone il nuovo oggetto che popolo i cui campi sono popolati dal tag ng-model nella pagina di front
//p.s. ovviamento è in cache, se non salvo il dato a db quando aggiorno la pagina lo perdo
$scope.addPerson = function(){
	$scope.persone.push($scope.new);
	//svuoto l'oggetto per un nuovo inserimento
    $scope.new = {"nome": "", "cognome": "", "age": ""};
};

$scope.personaSelezionata = null;

//questa function accetta un parametro
$scope.showInfo = function(p){
	console.log(p);
	//salvo la persona selezionata in una variabile di scope
	$scope.personaSelezionata = p;
}

$scope.insulti = ['merda', 'stronzo', 'schifo', 'JD', 'TF', 'pedo'];

$scope.insultaPersona = null;

$scope.insulta = function(i){

	var app = $scope.personaSelezionata.nome + ' ' + i
    if (confirm(app) == true) {
		$scope.insultaPersona = app;
    }
}

}]); //CLOSE APP