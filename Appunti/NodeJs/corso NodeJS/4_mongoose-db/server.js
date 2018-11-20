const mongoose = require('mongoose');

const URL='mongodb://localhost:27017/biblioteca';

//Promis JS
mongoose.Promise=global.Promise;
//Connessione al DB mongo, tabella Biilioteca
mongoose.connect(
    URL,{
        useNewUrlParser:true
    }
);

//Struttura dello schema
const libriSchema = mongoose.Schema({
    titolo: String,
    autore: String,
    prezzo: Number,
    disponibile: Boolean
});

//Costruzione del modello assegnandogli lo schema da usare
const Libro = mongoose.model('libro', libriSchema);

//AGGIUNGO UN LIBRO
/*
//Preparo lo schema del libro
const aggiungiLibro = new Libro({
    autore: 'Tolkien',
    titolo: 'Il Signore degli anelli',
    prezzo: 30,
    disponibile: true
});

//Inserimento di un modello nel DB
aggiungiLibro.save(function(err,doc){
    if(err){
        console.log(err);
    }
    console.log(doc);
});
*/

//CERCO UN LIBRO
/*
//Riceca di tutti i risultati
Libro.find({autore:"Tolkien"},function(err,doc){
    if (err){
        return console.log(err);
    }
    console.log(doc);
});

//Riceca del primo risultato
Libro.findOne({autore:"Tolkien"},function(err,doc){
    if (err){
        return console.log(err);
    }
    console.log(doc);
});

//Riceca per id
Libro.findById("5b7c7c0bd2667245835b6ce3",function(err,doc){
    if (err){
        return console.log(err);
    }
    console.log(doc);
});
*/

//ELIMINARE UN LIBRO
/*
//Riceca di tutti i risultati
Libro.findOneAndDelete({autore:"Tolkien"},function(err,doc){
    if (err){
        return console.log(err);
    }
    console.log(doc);
});

Libro.findByIdAndRemove("5b7c804d319f2b47b848712f",function(err,doc){
    if (err){
        return console.log(err);
    }
    console.log(doc);
});
*/

//AGGIORNAMENTO DI UN LIBRO
/*
//findByIdAndUpdate
//findOneAndUpdate
Libro.findOneAndUpdate(
    {titolo:'Il Signore degli anelli'},
    {$set:{titolo:'Il Signore degli anelli 2'}},
    function(err,doc){
    if (err){
        return console.log(err);
    }
    console.log(doc);
});

//Altro modo per aggiornare
Libro.findById(
    "5b7c80b95ee16c4801323835",
    function(err,libro){
        if (err) return console.log(err);

        libro.set({prezzo:18});

        libro.save(function(err,doc){
            if (err) return console.log(err);
            console.log(doc);
        })
});
*/

