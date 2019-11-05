// Trasformare in intero
let numero = parseInt('1') + 1;
let numero = (+'1') + 1;

let variabileConVirgola = 0.10;
variabileConVirgola = variabileConVirgola.toFixed(2);      //Converte numero a 2 cifre decimali ma lo trasforma in stringa

//NaN
Number.isNaN('12');     //Restituisce true per vedere se ciò che c'è nella parentesi è NaN
//Attenzione, non sono NaN => '', ' ', true, false e []

//Cast dei numeri
num = '12';
num = parseInt(num);   //Pars a Intero
numF = '12.43';
numF = parseFloat(numF);   //Pars a Intero

//In JS non esiste Division by Zero, perchè se faccio 2/0 = Infinity