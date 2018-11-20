//Instanzio oggetto MongoClient per connessione
const MongoClient = require('mongodb').MongoClient;
//Oggetto per il controllo degli errori
const assert = require('assert');

const URL='mongodb://localhost:27017/test';

MongoClient.connect(URL,function(err,client){
    assert.equal(null,err);
    console.log('connessione ok');

    //Apertura connessione
    const db = client.db('test');
    
    //Creo la collezione (Tabella)
    const collection=db.collection('corsi');
    var corso  = {
        titolo:'node', 
        ore:'4',
        livello:'medio'
    };
    var corso1 = {
        titolo:'php', 
        ore:'12',
        livello:'base'
    };
    var corso2 = {
        titolo:'bootstrap', 
        ore:'20',
        livello:'avanzato'
    };
    var corso3 = {
        titolo:'angular', 
        ore:'10',
        livello:'principianti'
    };
    //Modo alternativo per passare array di JSON da memorizzare
    var arrayCorso=[
        {titolo:'php', ore:'12',livello:'base'},
        {titolo:'bootstrap', ore:'20',livello:'avanzato'},
        {titolo:'angular', ore:'10',livello:'principianti'}
    ];


    //INSERIMENTO SINGOLO VALORE
    /*
    collection.insertOne(corso,function(err,result){
        if(err){
            console.log(err);
        }else{
            console.log('Documenti inseriti correttamente',result);
        }
    });
    */

    //INSERIMENTO MULTI VALORE
    /*
    collection.insertMany([corso1,corso2,corso3],function(err,result){
        if(err){
            console.log(err);
        }else{
            console.log('Documenti inseriti correttamente',result);
        }
    });
    */

    //RICERCA
    /*
    collection.find({livello:"base"}).toArray(function(err,result){
        if(err){
            console.log(err);
        }else{
            console.log(result,result);
        }
    });
    */

    //RICERCA DI UN SINGOLO VALORE
    /*
    collection.findOne({livello:"base"},{_id:0,titolo:0},function(err,result){
        if(err){
            console.log(err);
        }else{
            console.log(result,result);
        }
    });
    */

    //ELIMINAZIONE DI UN SINGOLO VALORE
    /*
    collection.deleteOne({livello:"principianti"},function(err,result){
        if(err){
            console.log(err);
        }else{
            console.log('collezione eliminata');
        }
    });
    */
   
    //UPDATE DI UN SINGOLO VALORE
    /*
    collection.findOneAndUpdate({livello:"avanzato"},{$set:({livello:"avanzatoPro"})},function(err,result){
        if(err){
            console.log(err);
        }else{
            console.log('collezione aggiornata');
        }
    });
    */
    

   //Chiusura connessione
   client.close();
   
});