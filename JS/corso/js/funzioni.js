//TIPI DI FUNZIONI

//Funzione creata in modo dichiarativo
function myFunc(argomento) {
    return argomento;
}

//Funzione creata come espressione
var myFunc = function MyFunc(argomento) {
    return argomento;
};


//Dichiarazione di una funzione
function test() {
    console.log('codice della funzione');
    return 'risultato';
}
//Chiamata della funzione
test();
//Assegnazione a variabile risultato di una funzione
var output = test();

//FUNZIONE CON ARGOMENTI
function calcola(operazione, parametro1, parametro2) {
    switch (operazione) {
        case 'somma':
            return parametro1 + parametro2;
            break;
        case 'sottrazione':
            return parametro1 - parametro2;
            break;
        case 'moltiplicazione':
            return parametro1 * parametro2;
            break;
        case 'divisione':
            return parametro1 / parametro2;
            break;
    }
}

//FUNZIONE CON ARGOMENTI VARIABILI
function calcola(operazione, parametro1, parametro2) {
    console.log(arguments);     //Oggetto standard di una funzione ed è ciclabile
    console.log(arguments[0]);  //Stampa ciò che c'è in operazione

    for (let i = 0; i < arguments.length; i++) {
        console.log(arguments[i]);
    }

    switch (operazione) {
        case 'somma':
            return parametro1 + parametro2;
            break;
        case 'sottrazione':
            return parametro1 - parametro2;
            break;
        case 'moltiplicazione':
            return parametro1 * parametro2;
            break;
        case 'divisione':
            return parametro1 / parametro2;
            break;
    }
}

//FUNZIONI CON ARGUMENTS
function calcola() {
    let operazione = arguments[0];                        //Variabile 0 =>    operazione
    let parametri = [];                                   //Array dei parametri
    let result = 0;

    for (let i = 1; i < arguments.length; i++) {
        switch (operazione) {
            case '+': result += arguments[i]; break;
            case '-': result -= arguments[i]; break;
            case '*':
                if (result === 0)
                    result = 1;
                result *= arguments[i]; break;
            case '/':
                if (result === 0)
                    result = 1;
                result /= arguments[i]; break;
            default: result = 0; break;
        }
    }
    return result;

}


//PASSAGGIO DI FUNZIONE AD ALTRA FUNZIONE
//Cre funzione che cicla gli elementi dell'oggetto e li stampa
function outPutObject(obj) {
    for (var i in obj) {
        console.logobj([i]);
    }
}
//Funzione che dato un oggetto esegue la funzione passata come variabile
function processObject(myOject, callBack) {
    callBack(myObject);
    //Creo un metodo all'interno della funzione
    callBack.testFunction = function () {
        console.log('Chiamata a testFunction!');
    }
}
//Oggetto che dovrò ciclare
var objTest = { name: 'Test', lastName: 'Test2', age: 33 };
//Lancio la funzione passando l'oggetto objTest ed eseguo la funzione outPutObject
processObject(objTest, outPutObject);
//Eseguo il metodo che ho dichiarato nella funzione al quale ho passato la funzione
outPutObject.testFunction();

//FUNZIONI COME ESPRESSIONI
var funcName = function () {
    console.log('funzione')
}
//Chiamo la funzione chiamando la variabile
funcName();

//FUNZIONI INVOCATE IMMEDIATAMENTE
//avvolgendo la funzione tra parentesi ed aggiungendo le tonde finali
//la funzione viene eseguita una sola volta all'avvio del file 
(function iife() {
    console.log('Teest');
})();

//FAT ARROW FUNCTION
var test = function (arg1, arg2) {
    return arg1 + arg2;
}
//Posso scrivere la funzione sopra in questo modo
//Come in questo caso, se devo eseguire solo una istruzione posso omettere return
var test2 = (arg1, arg2) => {
    return arg1 + arg2;
};

//PARAMETRI DI DEFAULT
//Dichiaro funzione con valori predefiniti
function isMaggiore(param1 = 0, param2 = 0) {
    return param1 > param2;
}
//RES parameter
//In questo modo chiamo la funzione mettendo :
//operazione => operazione che voglio fare
//...numeri => il RESTo dei parametri e JS li assegna alla var numeri come un array
function stampaParametri(operazione, ...numeri) {
    console.log(numeri);
}

