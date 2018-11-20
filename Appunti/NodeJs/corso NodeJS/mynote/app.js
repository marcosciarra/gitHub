// 1) npm init
// 2) npm install express --save

const express = require('express');             //inclusione express
const exphbs  = require('express-handlebars');  //inclusione handlebars

const app = express();                  // Chiamo metodo Express
const port = 3000;                      // Porta di connessione

//MIDDLEWARE PER HANDLERBARS
app.engine('handlebars', exphbs({defaultLayout: 'main'}));
app.set('view engine', 'handlebars');


//ROUTE PER PAGINA INFO
// la / serve per indicare la root
app.get('/',(req,res)=>{
    const titolo='Benvenuto';
    //chiamata a index.handlebars passandogli un oggetto con la const titolo
    res.render('index',{titolo:titolo});
});

app.get('/info',(req,res)=>{
    const titolo='Info';
    res.render('info',{titolo:titolo});
});

app.listen(port,function(){
    console.log('Server attivato sulla porta: ' + port);
});



//USO DI BASE DI MIDDLEWARE
//req => richiesta
//res => risposta
//next => gestione funzioni successive
/*
app.use((req,res,next)=>{
    req.saluto = 'Ciao, sono una app';
    next();
});

app.get('/',(req,res)=>{
    res.send(req.saluto);
});
*/

//ROUTE PER PAGINA INDEX
// la / serve per indicare la root
/*
app.get('/',(req,res)=>{
    res.send('Io sono la pagina inizale');
});
*/

//ROUTE PER PAGINA INFO
/*
// la / serve per indicare la root
app.get('/info',(req,res)=>{
    res.send('Pagina info');
});

app.listen(port,function(){
    console.log('Server attivato sulla porta: ' + port);
});
*/