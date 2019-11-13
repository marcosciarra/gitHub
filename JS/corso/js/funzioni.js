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
