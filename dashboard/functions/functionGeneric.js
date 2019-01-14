
function jsonParse (value) {
    if(value == ''){
        return [];
    }else{
        return JSON.parse(value);
    }
}

/**
 * Ordinamento di JSON
 *
 * @param data  --> JSON
 * @param key   -->chiave su cui fare l'ordinamento
 * @param way   --> up o down per indicare ordinamento crescente o decrescente
 * @returns {*}
 */
function sortJSON(data, key, way) {
    return data.sort(function(a, b) {
        var x = a[key]; var y = b[key];
        if (way === 'up' ) { return ((x < y) ? -1 : ((x > y) ? 1 : 0)); }
        if (way === 'down') { return ((x > y) ? -1 : ((x < y) ? 1 : 0)); }
    });
}

function controllaIbanIT (value) {
    var reg = /([a-zA-Z]{2}[0-9]{2}[a-zA-Z][0-9]{5}[0-9]{5}[0-9a-zA-Z]{12})/;
   /* var reg = /([a-zA-Z]{2}[0-9]{2}[a-zA-Z]{1}[0-9]{5}[0-9]{5}[0-9]{12})/;*/
    return reg.test(value);
}

function stampalog(value){
    if (localStorage.getItem("tipoUtente")=='SU'){
        console.log(value);
    }
};