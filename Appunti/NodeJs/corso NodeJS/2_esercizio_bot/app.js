//console.log(process.argv);

//Libreria per ricevere argomenti nella chiamata della applicazione
const lineeComando=require('command-line-args');

//definisco i parametri che riceverò dalla chiamata della applicazione
const definizioni=[
    {name:'nome',type:String},
    {name:'corso',type:String},
    {name:'pagamento',type:Number},
    {name:'fine',type:Boolean}
];

const opzioni=lineeComando(definizioni);

//1.node app.js
//2.node app.js -- nome
//3.node app.js -- corso
//4.node app.js -- pagamento
//5.node app.js -- fine

//per fare le chiamate da console      
//node app.js --nome Marco
//node app.js --corso Node.js
//...

if(opzioni.nome){
    console.log(`Ciao ${opzioni.nome}, puoi comprare il corso`);
}else if(opzioni.corso){
    console.log(`Ciao ${opzioni.nome}, l'imposto è di 30€`);
}else if(opzioni.pagamento){
    console.log(`Ciao ${opzioni.nome}, grazie del pagmento di ${opzioni.pagamento - 30}`);
}else if(opzioni.fine){
    console.log(`Grazie a presto`);
}else{
    console.log(`Benvenuto, come ti chiami?`);
}