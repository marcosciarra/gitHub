function loadText() {
    // Assegno ad xhttp l'oggetto per le richieste ad AJAX

    // per gestire la chiamata su browser "vecchi"
    if (window.XMLHttpRequest) {
        var xhttp = new XMLHttpRequest();
    }
    else {
        // Per IE5 e IE6
        var xhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("demo").innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", "./ajax/ajax_info.txt", true);
    xhttp.send();
}


function loadInput() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("demo2").innerHTML = this.responseText;
        }
    };
    // Open - Fa una chiamata al server indicando:
    //  - metodo (GET - POST)
    //  - url (percorso del file)
    //  - asincrono (true - false)
    xhttp.open("GET", "./ajax/input.html", true);
    // Send()           --> per GET
    // Send(stringa)    --> per POST
    xhttp.send();
}


function ajaxGet() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("demo3").innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", "./ajax/input.html", true);
    xhttp.send();
}


function ajaxPost() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("demo4").innerHTML = this.responseText;
        }
    };
    xhttp.open("POST", "./ajax/inputPost.html", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xhttp.send("var1=prova1&var2=prova2");
}