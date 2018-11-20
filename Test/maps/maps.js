function showMaps(position)
{

// ricavo le coordinate
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;

//punto della mappa della posizione del dispositivo
    var punto = new google.maps.LatLng(latitude , longitude);
//definiamo le opzioni da assare alla mappa
    options={
        zoom: 10, //valore dello zoom
        center: punto, //centriamo la mappa in base alle coordinate

        /*indentifico il tipo di mappa, in questo caso la mappa stradale.*/
        mapTypeId:google.maps.MapTypeId.ROADMAP
    }
    map_div=document.getElementById("my_map");

//creo l'oggetto mappa
    map = new google.maps.Map(map_div,options);
//definico il marker con le relative opzioni
    marker= new google.maps.Marker(
        {position:punto,
            map:map,
            title:"Questa è la tua posizione"});
}