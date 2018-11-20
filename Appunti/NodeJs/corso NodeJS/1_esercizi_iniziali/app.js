//caricamento modulo OS
const os=require('os');

//caricamento modulo FileSystem
const fs=require('fs');

//carico modulo creato da me
const infoStudente=require("./studenti.js");

console.log("Il nome dello studente è " + infoStudente.studenti.nome);

//Dichiarazione variabile
let utente=os.userInfo();
let piattaforma=os.platform();

let data=new Date();
let avviso="utente " + utente.username +  " ha inizializzato il giorno " + data + "il testo nel file";
/*
//stampa oggetto utente
console.log(utente);
//stampa attributo dell'oggetto utente
console.log(utente.username);
//stampa oggetto piattaforma
console.log(piattaforma);
*/

fs.appendFile('testo.txt',avviso,function(errore){
    if(errore){
        console.log('Si è verificato un errore');
    }
});