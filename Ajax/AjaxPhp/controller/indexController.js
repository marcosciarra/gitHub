var XMLHTTP;


function Richiesta(Stringa)
{
    if (Stringa.length > 0)
    {
        var url = "./controller/indexHandler.php?nome=" + Stringa.toUpperCase();
        XMLHTTP = RicavaBrowser(CambioStato);
        XMLHTTP.open("GET", url, true);
        XMLHTTP.send(null);
    }
    else
    {
        document.getElementById("risultati").innerHTML = "";
    } 
}


function CambioStato()
{
    if (XMLHTTP.readyState == 4)
    {
        var R = document.getElementById("risultati");
        R.innerHTML = XMLHTTP.responseText;
    }
}


function RicavaBrowser(QualeBrowser)
{
    if (navigator.userAgent.indexOf("MSIE") != (-1))
    {
        var Classe = "Msxml2.XMLHTTP";
        if (navigator.appVersion.indexOf("MSIE 5.5") != (-1));
        {
            Classe = "Microsoft.XMLHTTP";
        } 
        try
        {
            OggettoXMLHTTP = new ActiveXObject(Classe);
            OggettoXMLHTTP.onreadystatechange = QualeBrowser;
            return OggettoXMLHTTP;
        }
        catch(e)
        {
            alert("Errore: l'ActiveX non verrà eseguito!");
        }
    }
    else if (navigator.userAgent.indexOf("Mozilla") != (-1))
    {
        OggettoXMLHTTP = new XMLHttpRequest();
        OggettoXMLHTTP.onload = QualeBrowser;
        OggettoXMLHTTP.onerror = QualeBrowser;
        return OggettoXMLHTTP;
    }
    else
    {
        alert("L'esempio non funziona con altri browser!");
    }
}