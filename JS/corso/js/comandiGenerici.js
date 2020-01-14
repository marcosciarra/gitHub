
//CICLI
let programmi = ['php', 'JavaScript', 'python', 'nodeJs', 'c#'];
//FOR
for (let programmi of prog) {
    console.log(prog);
}
//FOR => ARRAY
for (let i; i < programmi.length; i++) {
    console.log(programmi[i]);
}

//DESTRUTTURARE UN ARRAY
let obj = { name: 'Marco', lastName: 'Sciarra' };
//In questo modo assegno alla variabile varNome e varCognome i valori dell'oggetto
let { name: varNome, lastName: varCognome } = obj;

