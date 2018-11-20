/** approccio sincrono (classico) **/
var dato = ottieniDatoDaRemoto(url);
alert(dato);

/** approccio ad eventi (asincrono) **/
ottieniDatoDaRemoto(url, function(dato) {
  alert(dato);
}); //la funzione ritorna subito