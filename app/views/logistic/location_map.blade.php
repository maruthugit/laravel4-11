<!DOCTYPE html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<title>GPS signal Tracking</title>
{{ HTML::style('css/bootstrap.min.css') }}
<!-- Scripts are placed here -->
        {{ HTML::script('js/jquery.js') }}
        {{ HTML::script('js/bootstrap.min.js') }}
 <!-- Latest compiled and minified JavaScript -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>

        <!-- bootbox code -->
        {{ HTML::script('js/bootbox.js') }}

<!-- Google Maps version -->
<!-- <script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyDcTN4TPOfZRUmCF_7S_4w3sFlxaGEr3f4&sensor=false"></script>
 -->
 <!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDUFmbwJMBHU_paeMfVO7oqPC1IJEtbJUU&sensor=false&&allback=createGMap" async defer></script>-->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDcTN4TPOfZRUmCF_7S_4w3sFlxaGEr3f4&sensor=false&&allback=createGMap" async defer></script>

<!-- OpenStreetMap + Leaflet.js version -->
<!-- <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.css" />
<script src="http://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.js"></script> -->
{{ HTML::style('css/leaflet.css') }}
{{ HTML::script('js/leaflet.js') }}

</head>
<body>

<h1>Date : <span id="date">…</span></h1>
<!-- <p>(last known position where I had a GPS signal, a network connection, and some battery power)</p> -->

<div id="googlemap" style="width: 100%; height: 900px">
    <div id="interlude" style="text-align: center; line-height: 600px; font-weight: bold; border: 1px dotted grey; background-color: #eee;">
        Map currently unavailable.
    </div>
</div>

<!-- <h2>OpenStreetMap + Leaflet.js version</h2>
<div id="openstreetmap" style="width: 800px; height: 600px">
    <div id="interlude" style="text-align: center; line-height: 600px; font-weight: bold; border: 1px dotted grey; background-color: #eee;">
        Map currently unavailable.
    </div>
</div> -->

<script>
var gmap, newmarker;
var osmap, osmarker;
var dte, lat, lon, utc, drivername;

var styles = [
    {
        "featureType": "all",
        "stylers": [
            {
                "saturation": 0
            },
            {
                // "hue": "#e7ecf0"
            }
        ]
    },
    {
        "featureType": "road",
        "stylers": [
            {
                "saturation": -70
            }
        ]
    },
    {
        "featureType": "transit",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "poi",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "water",
        "stylers": [
            {
                "visibility": "simplified"
            },
            {
                "saturation": -60
            }
        ]
    }
] 

$(document).ready(function() {

function createGMap(pbook) {
    // var styledMap = new google.maps.StyledMapType(styles,
    //   {name: "Simple Map"});

    var latlng = new google.maps.LatLng(pbook[0].latitude, pbook[0].longitude);
    


    var myOptions = {
        zoom: 8,
         center: latlng,
        mapTypeControl: false,
        navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    gmap = new google.maps.Map(document.getElementById("googlemap"), myOptions);
    // gmap.mapTypes.set('map_style', styledMap);
    // gmap.setMapTypeId('map_style');

     var styledMap = new google.maps.StyledMapType(styles,
      {name: "Simple Map"});
    gmap.mapTypes.set('map_style', styledMap);
    gmap.setMapTypeId('map_style');
    var infowindow = new google.maps.InfoWindow({})

    $.each(pbook, function (index, value) {
              
                    dte = value.created_date;
                    lat = value.latitude;
                    lon = value.longitude;
                    utc = '';
                    drivername = value.driver_name;
                    add = value.address;
                    // alert(lat);

     var newmarker = new google.maps.Marker({
            position: new google.maps.LatLng(lat, lon),
            icon:'/media/lorry_icon.png',
            map: gmap,
            title: drivername, 
            // id:     locations[i][2],
        });

     newmarker.setMap(gmap);
        newmarker['infowindow'] = new google.maps.InfoWindow({
            content: '<div style="font-weight:bold;">Driver Name: '+drivername+'</div><div><span style="font-weight:bold;">Last GPS Coordinates:</span> <br>Latitude: '+lat+' | Longitude: '+lon+' <br> <span style="font-weight:bold;">Address</span> : '+add+'<br> <span style="font-weight:bold;">Last Tracking Date/Time</span> : '+dte+'</div>'
        });

       
    
    var infowindow = new google.maps.InfoWindow({
          content: '<div style="font-weight:bold;">Driver Name: '+drivername+'</div><div><span style="font-weight:bold;">Last GPS Coordinates:</span> <br>Latitude: '+lat+' | Longitude: '+lon+' <br> <span style="font-weight:bold;">Address</span> : '+add+'<br> <span style="font-weight:bold;">Last Tracking Date/Time</span> : '+dte+'</div>'
          // getPosition() 

            
        });

    // newmarker.setMap(gmap);

    infowindow.open(gmap, newmarker);
    

    google.maps.event.addListener(newmarker, 'click'  , function(e) {
        // alert(this);
         this['infowindow'].open(gmap, this);
        // alert("Driver Name: "+newmarker.title+"\nGPS coordinates:\nLatitude: " + newmarker.getPosition().lat() + "\nLongitude: " + newmarker.getPosition().lng());
    });
});
}
 
function createOSMap() {
    osmap = L.map('openstreetmap').setView([lat, lon], 12);

    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(osmap);

    osmarker = L.marker([lat, lon]);

    osmarker
        .addTo(osmap)
        .bindPopup("<p>GPS coordinates :</p><p>" + lat + ", " + lon + "</p>");
}

function removeMarkers(){
    for(i=0; i<newmarker.length; i++){
        newmarker[i].setMap(null);
    }
}

function updateGMap(pbook) {
    var latlng = new google.maps.LatLng(pbook[0].latitude, pbook[0].longitude);
    //var latlng = new google.maps.LatLng(lat, lon);
    // gmarker.setPosition(latlng);
    // gmap.panTo(latlng);
    // alert('call');

    var newmarker = [];
    // removeMarkers();

    $.each(pbook, function (index, value) {
              
                    dte = value.created_date;
                    lat = value.latitude;
                    lon = value.longitude;
                    utc = '';

                    

    // for (var i = 0; i < lat.length; i++) { 
        var newmarker = new google.maps.Marker({
            position: new google.maps.LatLng(lat, lon),
            icon:'/media/lorry_icon.png',
             map: gmap,
            // title: "'"+locations[i][2]+"'", 
            // id:     locations[i][2],
        });
        newmarker.setMap(gmap);

        // newmarker['infowindow'] = new google.maps.InfoWindow({
        //     content: '<div style="font-weight:bold;">Transaction ID: '+locations[i][2]+'</div>'
        // });
        

        // google.maps.event.addListener(newmarker, 'click', function() {
        //     this['infowindow'].open(map, this);

            
        // });

        google.maps.event.addListener(newmarker, "click", function(e) {
        alert("GPS coordinates:\nLatitude: " + newmarker.getPosition().lat() + "\nLongitude: " + newmarker.getPosition().lng());
    });

        // alert('ok');
        // marker.push(newmarker);
    // }

    });

}

function updateOSMap() {
    osmarker.setLatLng([lat, lon]);
    osmarker.bindPopup("<p>GPS coordinates :</p><p>" + lat + ", " + lon + "</p>");
    osmap.panTo([lat, lon]);
}

function formatAMPM() {
    var date = new Date();
    var hours = date.getHours();
    var days = date.getDay(); 
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'pm' : 'am';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0'+minutes : minutes;
    var strTime = date + ' ' + hours + ':' + minutes + ' ' + ampm;
    return strTime;
}

function doRefresh() {
   //alert('asa');
    var xhr;
    try {
        xhr = new XMLHttpRequest();
    } catch (e) {
        xhr = false;
    }

    xhr.onreadystatechange  = function() {
        if (xhr.readyState  == 4) {
            if (xhr.status  == 200) {
                // alert(xhr.responseText);
                var param = xhr.responseText.split(',');

                

               //var  pbook  =  new  Array (); 

               var  pbook = JSON.parse(xhr.responseText);
            //   console.log(pbook);
                // alert(param[1]);
                // for (var i = 0; i < param.length; i++) { 
                //     alert(param['driverid']);
                // }
                

                $.each(pbook, function (index, value) {
              
                    dte = value.created_date;
                    lat = value.latitude;
                    lon = value.longitude;
                    utc = '';
                    add = value.address;

                    });

                // alert(dte+'\n'+lat+'\n'+lon+'\n'+utc);
                if (dte && lat && lon) {
                    if (!gmap) {
                        createGMap(pbook);
                    } else {
                        createGMap(pbook);
                    }
                    // if (!osmap) {
                    //     createOSMap();
                    // } else {
                    //     updateOSMap();
                    // }

                       utc_dte = new Date(dte);
                        document.querySelector("#date").innerHTML = formatAMPM();

                    //alert(utc_dte.toLocaleString());

                    // if (utc) {
                    //     utc_dte = new Date(parseInt(utc));
                    //     document.querySelector("#date").innerHTML = utc_dte.toLocaleString();
                    // } else {
                    //     document.querySelector("#date").innerHTML = dte + " (server time)";
                    // }
                }

            
            }
        }
    };
    // xhr.open("GET", "/jlogistic/trackingvalues?" + Math.random(),  true);
    xhr.open("GET", "/jlogistic/trackingvalues",  true);
    xhr.send(null);
    setTimeout(function() {doRefresh() }, 60000 );
    
}

doRefresh();

});
</script>