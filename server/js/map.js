/**
*  Acts as class representing a JSON-encoded
*  HTTP POST request.
*/
function JSONRequest(URL, Data) {

    // member functions
    this.Send       = Send;

    // prepare request
    var m_request = new XMLHttpRequest();
    InitialiseRequest();

    /**
    * Initialise the basic POST request.
    */
    function InitialiseRequest () {
        m_request.open ("POST", URL, false);
        m_request.setRequestHeader ("Content-Type", "application/x-www-form-urlencoded");
    }

    /**
    * Sends the request and returns
    * its result.
    */
    function Send () {
        m_request.send(Data);
        return JSON.parse(m_request.responseText);

    }

}

/**Requests farm data from database and stores in JSON object Response **/




function getFarms(){
    var Request = new JSONRequest('../martin/api.php',
    "operation=listCroftsTest");

    var Response = Request.Send();

    console.log(Response);
    return Response;
}

function new_marker(position,map,title,icon_color){

    var color = ' ';
    switch(icon_color){

        case 'red' :  color = 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'; break;

        case 'yellow' : color = 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png'; break;

        default  : color = 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'; break;

    }

    var marker = new google.maps.Marker({

        position: position,
        map: map,
        title: title,
        icon: color

    });

    return marker;

}

function dynamicMap(){
    var mapOptions = {
        zoom: 3,
        center: myCenter
    }
    var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
    var infowindowArray = [];



    var markers = [];
    colorArray = ['red','green','blue','yellow'];
    var myCenter = new google.maps.LatLng(34,-40);

    var arrayLng = [];
    var arrayLat = [];
    var titleArray = [];
    var contentString = [];
    var listeners = [];
    var counter = 0;
    infowindow = new google.maps.InfoWindow();

    /*********************** Function definition *************************/

    function clearMarkers(){

        for(i = 0; i < markers.length; i++){

            markers[i].setMap(null);
            google.maps.event.removeListener(listeners[i]);
        }

        arrayLng = [];
        arrayLat = [];
        titleArray = [];
        contentString = [];
        listeners = [];
        markers = [];

    }

    function updateMarkers(){

        clearMarkers();
        Response = getFarms();

        for(i = 0; i < Response.length; i++){
            arrayLng.push(Response[i].longitude);
            arrayLat.push(Response[i].latitude);
            titleArray.push(Response[i].farmer_id);
            contentString.push(Response[i].name);
        }

        console.log('Refresh number: ' + counter);
        counter++;

        for (i = 0; i < arrayLng.length; i++){
            myLatlng = new google.maps.LatLng(arrayLat[i],arrayLng[i]);
            if (Response[i].state === "normal") {
                markers.push(new_marker(myLatlng,map,titleArray[i], "green"));
            } else if (Response[i].state === "endangered") {
                markers.push(new_marker(myLatlng,map,titleArray[i], "yellow"));
            } else if (Response[i].state === "infected") {
                markers.push(new_marker(myLatlng,map,titleArray[i], "red"));
            }


        }


        var content, i;
        for(i = 0; i < markers.length; i++){

            listeners.push(google.maps.event.addListener(markers[i],'click', function(content) {

                return function() {
                    infowindow.setContent(content);
                    infowindow.open(map,this);
                }
            }(contentString[i])));
        }

    }

    function AutoCenter(){
        var bounds = new google.maps.LatLngBounds();

        $.each(markers, function (index, marker) {
            bounds.extend(marker.position);
        });

        map.fitBounds(bounds);
    }

    function refresh(){
        window.setInterval(updateMarkers, 5000);
    }

    /*********************** Function calls *************************/
    updateMarkers();
    AutoCenter();
    refresh();
}


/**initialises maps with markers **/
function initialize() {
    var dynMap = new dynamicMap();
}

google.maps.event.addDomListener(window, 'load', initialize);
