<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDcTN4TPOfZRUmCF_7S_4w3sFlxaGEr3f4" async defer></script>

    {{ HTML::style('font-awesome/css/font-awesome.min.css') }}

    {{ HTML::style('css/circle/bootstrap.min.css') }}

    <title>Logistic Dashboard : GPS signal Tracking & Overall Statistics</title>

    <style type="text/css">

	     #rcorners4 {
	        border-radius: 1px;
	        /*background: #73AD21;*/
	       /* border-color: #42B3FC;
	        border-style:solid;*/
	       
	        vertical-align: middle;
	        padding: 0px; 
	        min-width: 12rem;
	        min-height: 12rem; 
	    }

	    /*.navbar{

	    	 background: rgba(0, 0, 0, .15);
	    	 background: #73AD21;
	    	 width: 100%;
	    }*/

	    .text-info1{

	    	 font-size: 2.4rem;
	    	  color: #fdd10b;
	    	 /*color: #81f1f9; */
	    	
	    }

	    .bg-info{
	    	background: #81f1f9;

	    }

	    .bg-secondary1{
	    	background: #272727;

	    }

	    .bg-dark1{
	    	background: #01192d;
	    }

	    .text-white {
	    	color : #FFFFFF;
	    	text-align: center;
	    }

	    .font-weight-bold1{
	    	font-weight: bold;
	    	font-size: 2.9rem;
	    }

	    .font-weight-bold2{
	    	font-weight: bold;
	    	font-size: 2.6rem;
	    }

	    .rounded {
		  border-radius: 10px;
		  /*background: #FFF;*/
		  border-left: 1px solid #42B3FC;
   		  border-top: 3px solid #42B3FC;
   		  border-bottom: 3px solid #42B3FC;
   		  border-right: 1px solid #42B3FC;
   		  min-width: 12rem;
	      min-height: 12rem;
		}

		.rounded_sub {
		  border-radius: 2px;
		  /*background: #FFF;*/
		  border-left: 1px solid #42B3FC;
   		  border-top: 2px solid #42B3FC;
   		  border-bottom: 2px solid #42B3FC;
   		  border-right: 1px solid #42B3FC;
   		  max-width: 5rem;
	      max-height: 5rem;
	      padding: 15px;
		}



		.bg_hd {
			background: #73AD21;
			
		}

		.textcenter {
			    margin: auto;
			    text-align: center;
			    width: 100%;
			    /*border: 3px solid;*/
			    padding: 10px;
			}

		/*Chart CSS Start*/
		.rounded-progress {
		  /*border-radius: 10px;*/
		  /*background: #FFF;*/
		  border-left: 1px solid #42B3FC;
   		  border-top: 3px solid #42B3FC;
   		  border-bottom: 3px solid #42B3FC;
   		  border-right: 1px solid #42B3FC;
   		  min-width: 20rem;
	      min-height: 20rem;


		}


.progress-bar {
  background-color: #01192d;
   height: 15px;
}
.progress-bar-primary {
  background-color: #4680ff;
}
.progress-bar-success {
  background-color: #26dad2;
}
.progress-bar-info {
  background-color: #62d1f3;
}
.progress-bar-danger {
  background-color: #fc6180;
}
.progress-bar-warning {
  background-color: #ffb64d;
}
.progress-bar-pink {
  background-color: #e6a1f2;
}
.progress {
  height: 6px;
}
.progress-bar.active,
.progress.active .progress-bar {
  animation: 2s linear 0s normal none infinite running progress-bar-stripes;
}
.progress-vertical {
  display: inline-block;
  height: 250px;
  margin-bottom: 0;
  margin-right: 20px;
  min-height: 250px;
  position: relative;
}
.progress-vertical-bottom {
  display: inline-block;
  height: 250px;
  margin-bottom: 0;
  margin-right: 20px;
  min-height: 250px;
  position: relative;
  transform: rotate(180deg);
}
.progress-animated {
  animation-duration: 5s;
  animation-name: myanimation;
  transition: all 5s ease 0s;

}
@keyframes myanimation {
  0% {
    width: 0;
  }
}
@keyframes myanimation {
  0% {
    width: 0;
  }
}
.browser .progress {
  height: 15px;
}

		@keyframes loading-1{
		    0%{
		        -webkit-transform: rotate(0deg);
		        transform: rotate(0deg);
		    }
		    100%{
		        -webkit-transform: rotate(180deg);
		        transform: rotate(180deg);
		    }
		}
		@keyframes loading-2{
		    0%{
		        -webkit-transform: rotate(0deg);
		        transform: rotate(0deg);
		    }
		    100%{
		        -webkit-transform: rotate(144deg);
		        transform: rotate(144deg);
		    }
		}
		@keyframes loading-3{
		    0%{
		        -webkit-transform: rotate(0deg);
		        transform: rotate(0deg);
		    }
		    100%{
		        -webkit-transform: rotate(90deg);
		        transform: rotate(90deg);
		    }
		}
		@keyframes loading-4{
		    0%{
		        -webkit-transform: rotate(0deg);
		        transform: rotate(0deg);
		    }
		    100%{
		        -webkit-transform: rotate(36deg);
		        transform: rotate(36deg);
		    }
		}
		@keyframes loading-5{
		    0%{
		        -webkit-transform: rotate(0deg);
		        transform: rotate(0deg);
		    }
		    100%{
		        -webkit-transform: rotate(126deg);
		        transform: rotate(126deg);
		    }
		}
		@media only screen and (max-width: 990px){
		    .progress{ margin-bottom: 20px; }
		}

		
.card {
  margin-bottom: 30px;
}
.card .card-subtitle {
  color: #99abb4;
  font-weight: 300;
  margin-bottom: 15px;
}
.card-inverse .card-bodyquote .blockquote-footer {
  color: rgba(255, 255, 255, 0.65);
}
.card-inverse .card-link {
  color: rgba(255, 255, 255, 0.65);
}
.card-inverse .card-subtitle {
  color: rgba(255, 255, 255, 0.65);
}
.card-inverse .card-text {
  color: rgba(255, 255, 255, 0.65);
}
.card-success {
  background: #26dad2 none repeat scroll 0 0;
  border-color: #26dad2;
}
.card-danger {
  background: #ef5350 none repeat scroll 0 0;
  border-color: #ef5350;
}
.card-warning {
  background: #ffb22b none repeat scroll 0 0;
  border-color: #ffb22b;
}
.card-info {
  background: #1976d2 none repeat scroll 0 0;
  border-color: #1976d2;
}
.card-primary {
  background: #5c4ac7 none repeat scroll 0 0;
  border-color: #5c4ac7;
}
.card-dark {
  background: #2f3d4a none repeat scroll 0 0;
  border-color: #2f3d4a;
}
.card-megna {
  background: #00897b none repeat scroll 0 0;
  border-color: #00897b;
}
.card-actions {
  float: right;
}
.card-actions a {
  color: #67757c;
  cursor: pointer;
  font-size: 13px;
  opacity: 0.7;
  padding-left: 7px;
}
.card-actions a:hover {
  opacity: 1;
}
.card-columns .card {
  margin-bottom: 20px;
}
.collapsing {
  transition: height 0.08s ease 0s;
}
.card-outline-info {
  border-color: #1976d2;
}
.card-outline-info .card-header {
  background: #1976d2 none repeat scroll 0 0;
  border-color: #1976d2;
}
.card-outline-inverse {
  border-color: #2f3d4a;
}
.card-outline-inverse .card-header {
  background: #2f3d4a none repeat scroll 0 0;
  border-color: #2f3d4a;
}
.card-outline-warning {
  border-color: #ffb22b;
}
.card-outline-warning .card-header {
  background: #ffb22b none repeat scroll 0 0;
  border-color: #ffb22b;
}
.card-outline-success {
  border-color: #26dad2;
}
.card-outline-success .card-header {
  background: #26dad2 none repeat scroll 0 0;
  border-color: #26dad2;
}
.card-outline-danger {
  border-color: #ef5350;
}
.card-outline-danger .card-header {
  background: #ef5350 none repeat scroll 0 0;
  border-color: #ef5350;
}
.card-outline-primary {
  border-color: #5c4ac7;
}
.card-outline-primary .card-header {
  background: #5c4ac7 none repeat scroll 0 0;
  border-color: #5c4ac7;
}
.card-body {
  padding: 0;
  width: 100%;
}
.card {
  background: #01192d none repeat scroll 0 0;
  margin: 15px 0;
  padding: 0px;
  border: 0 solid #e7e7e7;
  border-radius: 5px;
  box-shadow: 0 5px 20px 0 rgba(0,0,0,0.15);
  color : #fff;
}
.card-subtitle {
  font-size: 12px;
  margin: 10px 0;
}
.card-title {
  font-weight: 500;
  font-size: 18px;
  line-height: 22px;
}
.card-title h4 {
  display: inline-block;
  font-weight: 500;
  font-size: 18px;
  line-height: 22px;
}
.card-title p {
  font-family: 'Poppins', sans-serif;
  margin-bottom: 12px;
}


/*Panel Size*/

.pn {
	height: 250px;
	box-shadow: 0 2px 1px rgba(0, 0, 0, 0.2);
}

.pn:hover {
	box-shadow: 2px 3px 2px rgba(0, 0, 0, 0.3);
	
}

/*Grey Panel*/

.grey-panel {
	text-align: center;
	background: #dfdfe1;
}
.grey-panel .grey-header {
	background: #ccd1d9;
	padding: 3px;
	margin-bottom: 15px;
}
.grey-panel h5 {
	font-weight: 200;
	margin-top: 10px;
}
.grey-panel p {
	margin-left: 5px;
}
/* Specific Conf for Donut Charts*/
.donut-chart p {
	margin-top: 5px;
	font-weight: 700;
	margin-left: 15px;
}
.donut-chart h2 {
	font-weight: 900;
	color: #FF6B6B;
	font-size: 38px;
}

.relative {
  position: relative;
}

.absolute-center {
  position:absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.text-center{
  text-align: center;
}

.relative {
  position: relative;
}

.absolute-center {
  position:absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.icon-color {
	color: #fdd10b;
}

.piegray {
	color: #1EAA91;
}

.font-weight-bold3{
	    	font-weight: bold;
	    	font-size: 1.9rem;
	    }

.headsize{

	text-transform: uppercase;
}

		/*Chart CSS End*/

 	</style>
 	

</head>
<body class="bg-light">
<nav class="row navbar-expand navbar-dark bg-secondary1" style="padding-left:10px;">
	<form class="form-inline "   style="width:100%;">
	    <div class="input-group text-left"  style="width:50%;">
	    	<a class="navbar-brand font-weight-bold" href="javascript:void(0)"><span class="text-info1">Logistic Dashboard : </span><span>GPS signal Tracking & Overall Statistics</span></a> 
	    </div>
	    <div class="text-right"  style="width:50%; padding-right:30px;">
	    	<span id="date" class="text-right font-weight-bold2 text-white"></span>
	    </div>
    </form>
</nav>


<div class="d-flex" style="width: 100%;">
	<div class="content pp-3 pull-left" style="width: 80%; height: 100%">
	        <div lass="form-body">
	            <div id="googlemap" style="width: 100%; height: 1400px">
						    <div id="interlude" style="text-align: center; width:100%; line-height: 1150px; font-weight: bold; border: 0px dotted grey; background-color: #eee;">
						        Map currently unavailable.
						    </div>
				</div>

	        </div> 

    </div>
    <div class="col-md-4 sidebar bg-dark1 pull-right" style="width:20%; border: 1px; border-style:  none; border-color: #FF1111; padding-left: 40px;">
    	<div class="form-body">
    		<div class="row mb-4">
		    	<div class="row col-md col-lg-12 textcenter" style="border: 1px; border-style:  none; border-color: #FFF;  ">
		    		<h4><span class="font-weight-bold text-info1 headsize">Overall Batch Pending </span></h4>
		    	</div>
		    	<!-- <section class="wrapper"> -->
		    	<div class="row mb-4 col-lg-12 textcenter" style="border: 1px; border-style: none; border-color: #FFF;">
		    		<div class="col-md">
		    		 	<div class="d-flex rounded">
		    					<span id="rcorners4" class="text-white align-middle" style="vertical-align:middle; padding-top:1.2rem;">
		    						<span id="total-batchpending1" class="font-weight-bold1 icon-color"></span>
		    						<span><h4 class="font-weight-bold text-white">1 Day</h4></span>
		    					</span>
		    			</div>
		    		</div>

		    		<div class="col-md">
		    		 	<div class="d-flex rounded">
		    				<span id="rcorners4" class="text-white" style="vertical-align:middle; padding-top:1.2rem;">
								<span id="total-batchpending2" class="font-weight-bold1 icon-color"  style="vertical-align:middle; padding-top:1.2rem;"></span>
								<h4 class="font-weight-bold text-white">2 Days</h4>
							</span>
		    			</div>
		    		</div>
		    		<div class="col-md">
		    		 	<div class="d-flex rounded">
			    			<span id="rcorners4" class="text-white" style="vertical-align:middle; padding-top:1.2rem;">
								<span id="total-batchpending3" class="font-weight-bold1 icon-color"  style="vertical-align:middle; padding-top:1.2rem;"></span>
								<h4 class="font-weight-bold text-white">Over 3 Days</h4> 
							</span>
		    			</div>
		    		</div>
		    		    		
		    	</div>
		    	<!-- </section> -->
		    	<div class="row col-md col-lg-12 textcenter"  style="border: 1px; border-style: none;  adding:20px; ">
		    		<p align="center">
		    		<h4><span class="font-weight-bold text-info1 headsize">Daily Statistics</span></h4>
		    		</p>
		    	</div>
		    	<div class="row col-md col-lg-12 " style="border: 1px; border-style:  none; border-color: #FFF;">
		            <div class="col-md-12 mb-4 ol-md-8">
		                <div class="col-lg-12 " style="width:100%;">
	                        <div class="card bg-dark1">
	                            <div class="card-body browser">
	                            	<div class="row d-flex">
	                            		<div class="col-md-2 rounded_sub" tyle="width: 38rem;">
	                            			<span class="ounded-progress text-center "><i class="fa fa-exclamation fa-2x icon-color" aria-hidden="true"></i></span>
	                            		</div>
		                            	<div class="col-md-10" style="border: 1px; border-style:  none; border-color: #FFF;">
		                            		<p class="f-w-600"><span class="font-weight-bold3">Total Pending</span> <span class="pull-right font-weight-bold2" id='lblpending'></span></p>
			                                <div class="progress ">
			                                    <div role="progress-bar" id='divtotalpending' style="height:15px;" class="progress-bar bg-info wow animated progress-animated"> <span class="sr-only">60% Complete</span> </div>
			                                </div>	
		                            	</div>
	                            	</div>
	                            	<div class="row d-flex">
	                            		<div class="col-md-2 rounded_sub" tyle="width: 38rem;">
	                            			<span class="ounded-progress text-center "><i class="fa fa-check fa-2x icon-color" aria-hidden="true"></i></span>
	                            		</div>
		                            	<div class="col-md-10">
		                            		<p class="f-w-600"><span class="font-weight-bold3">Total Sending</span> <span class="pull-right font-weight-bold2" id='lblsending'></span></p>
			                                <div class="progress ">
			                                    <div role="progress-bar" id='divtotalsending' style="height:15px;" class="progress-bar bg-info wow animated progress-animated"> <span class="sr-only">60% Complete</span> </div>
			                                </div>	
		                            	</div>
	                            	</div>
	                            	<div class="row d-flex">
	                            		<div class="col-md-2 rounded_sub" tyle="width: 38rem;">
	                            			<span class="ounded-progress text-center "><i class="fa fa-truck fa-2x icon-color" aria-hidden="true"></i></span>
	                            		</div>
		                            	<div class="col-md-10">
		                            		<p class="f-w-600"><span class="font-weight-bold3">Out for Delivery</span> <span class="pull-right font-weight-bold2" id='lbloutdeliver'></span></p>
			                                <div class="progress ">
			                                    <div role="progress-bar" id='divoutfordelivery' style="height:15px;" class="progress-bar bg-info wow animated progress-animated"> <span class="sr-only">60% Complete</span> </div>
			                                </div>	
		                            	</div>
	                            	</div>
	                            	<div class="row d-flex">
	                            		<div class="col-md-2 rounded_sub" tyle="width: 38rem;">
	                            			<span class="ounded-progress text-center "><i class="fa fa-check-square fa-2x icon-color" aria-hidden="true"></i></span>
	                            		</div>
		                            	<div class="col-md-10">
		                            		<p class="f-w-600"><span class="font-weight-bold3">Shipment Complete</span><span class="pull-right font-weight-bold2" id='lblshipment'></span></p>
			                                <div class="progress ">
			                                    <div role="progress-bar" id='divshipmentcomplete' style="height:15px;" class="progress-bar bg-info wow animated progress-animated"> <span class="sr-only">60% Complete</span> </div>
			                                </div>	
		                            	</div>
	                            	</div>
	                            	<div class="row d-flex">
	                            		<div class="col-md-2 rounded_sub" tyle="width: 38rem;">
	                            			<span class="ounded-progress text-center "><i class="fa fa-undo fa-2x icon-color" aria-hidden="true"></i></span>
	                            		</div>
		                            	<div class="col-md-10">
		                            		<p class="f-w-600"><span class="font-weight-bold3">Undelivered</span><span class="pull-right font-weight-bold2" id='lblundeliver'></span></p>
			                                <div class="progress ">
			                                    <div role="progress-bar" id='divundelivered' style="height:15px;" class="progress-bar bg-info wow animated progress-animated"> <span class="sr-only">60% Complete</span> </div>
			                                </div>	
		                            	</div>
	                            	</div>
	                            	
	                            </div>
	                        </div>
	                    </div>


		            </div>
	       		 </div>
	       		<!-- </section> -->
	       		<div class="row col-md col-lg-12 textcenter"  style="border: 1px; border-style: none; border-color: #FF0000;  adding:20px; ">
		    		<p align="center">
		    		<h4><span class="font-weight-bold text-info1 text-center headsize">Sent Items</span></h4>
		    		</p>
		    	</div>
		    	<div class="row col-md col-lg-12 textcenter" id ="maincontents" >
				</div>
			

		    


			

       		<!-- </section> -->

    	   </div>
        </div>
        
    </div>
 
</div>


{{ HTML::script('js/jquery.js') }}
{{ HTML::style('css/bootstrap.min.css') }}
{{ HTML::script('js/Chart.js') }}


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
            content: '<div style="font-weight:bold;">Driver Name: '+drivername+'</div><div><span style="font-weight:bold;">Last GPS Coordinates:</span> <br><b>Latitude: '+lat+' | Longitude: '+lon+'</b> <br> <span style="font-weight:bold;">Address</span> : '+add+'<br> <span style="font-weight:bold;">Last Tracking Date/Time</span> : '+dte+'</div>'
        });

       
    
    var infowindow = new google.maps.InfoWindow({
          content: '<div style="font-weight:bold;">Driver Name: '+drivername+'</div><div><span style="font-weight:bold;">Last GPS Coordinates:</span> <br><b>Latitude: '+lat+' | Longitude: '+lon+' <b><br> <span style="font-weight:bold;">Address</span> : '+add+'<br> <span style="font-weight:bold;">Last Tracking Date/Time</span> : '+dte+'</div>'
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
    osmap = L.map('openstreetmap').setView([lat, lon], 8);

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
               // alert(xhr.responseText);
                // alert(param[1]);
                // for (var i = 0; i < param.length; i++) { 
                //     alert(param['driverid']);
                // }
                document.querySelector("#date").innerHTML = formatAMPM();

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
                    

                       utc_dte = new Date(dte);
                        

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
    setTimeout(function() {doRefresh() }, 300000 );
    
}

doRefresh();

fnDoughnut();


function fnDoughnut(){
	// alert('ok');

		$.ajax({
	            method: "POST",
	            url: "/jlogistic/activedrivers",
	            dataType:'json',
	            data: {},
	            beforeSend: function(){
	                
	            },
	            success: function(data) {
	                   // alert(data.response);
	                itern = 0; 
	                canvasitern = 0;
	                contentscmb = "";
	                chartcontents = "<div class='row col-md col-lg-12 textcenter'><div style='width: 100%; border: 1px; border-style: none;'>";
	                legendcontents = "<div class='row col-md col-lg-12 textcenter'><div style='width: 100%; padding-bottom:20px;'>";

	                	// console.log(data.data.driverdata.driverdetails.length);

	                   // console.log(data);
	                $.each(data.data.driverdata.driverdetails, function (index, value) {
	                	// alert(value.totalbatch);
	                	// alert(value.totalbatch);
	                	canvasitern = canvasitern + 1;
	                	chartcontents = chartcontents + "<div class='col-lg-4' style='position: relative'><canvas id=canvasitn"+canvasitern+" height='120' width='120'></canvas><div class='absolute-center text-center'><span class='text-white font-weight-bold2'>"+value.totalbatch+"</span></div></div>";

	                	legendcontents = legendcontents + "<div class='col-lg-4' style='position: relative;'><div class='absolute-center text-center font-weight-bold'><span class='text-white headsize'>"+value.name+"<br><i class='fa fa-check-square icon-color font-weight-bold3' aria-hidden='true'></i> <span class='font-weight-bold3'>"+value.totalsent+"&nbsp;</span><i class='fa fa-exclamation piegray font-weight-bold3' aria-hidden='true'></i><span class='font-weight-bold3'> "+value.totalpending+"</span></span></div></div>";

	                	itern = itern + 1; 
	                	if(itern == 3){
	                		itern = 0; 
	                		chartcontents = chartcontents + '</div></div>';
	                		legendcontents = legendcontents + '</div></div>';
	                		contentscmb = contentscmb +  chartcontents + legendcontents ;
	                		chartcontents = "<div class='row col-md col-lg-12 textcenter'><div style='width: 100%; border: 1px; border-style: none;'>";
	                		legendcontents = "<div class='row col-md col-lg-12 textcenter'><div style='width: 100%; padding-bottom:20px;'>";
	                	}	

	                });

	                if(data.data.driverdata.driverdetails.length == 0){

	                	contentscmb = chartcontents + "<span class='text-white'>No Active drivers today</span></div></div>";

	                }

	               
	                if(itern != 0){
	                	itern = 0; 
                		chartcontents = chartcontents + '</div></div>';
                		legendcontents = legendcontents + '</div></div>';
                		contentscmb = contentscmb +  chartcontents + legendcontents ;
                		canvasitern = 0;
	                }

	                $("#maincontents").html(contentscmb); 
                    canvasitern =0;
                    //console.log(canvasitern);
	                $.each(data.data.driverdata.driverdetails, function (index, value) {

	                		canvasitern = canvasitern + 1;

	                		doughnutid = "";
	                		totalbt = 0 
	                		sentitem = 0;
	                		pendingitem = 0;
	                	    doughnutid = "canvasitn"+canvasitern;
	                	    totalbt = value.totalbatch;
	                	    if(value.totalsent != 0){
	                	    	sentitem = (value.totalsent / totalbt) * 100 ;
	                	    }
	                	    pendingitem = (value.totalpending / totalbt) * 100 ;
                       // console.log(doughnutid);

		                var myDoughnut = new Chart(document.getElementById(doughnutid).getContext("2d")).Doughnut([
							{
								value: sentitem,
								color:"#fdd10b",
							},
							{
								value : pendingitem,
								color : "#1EAA91",
							}

						]
						);


	                });




	            }
	        });

		setTimeout(function() {fnDoughnut() }, 250000 );
}

fnProgressbar();

function fnProgressbar(){
	$.ajax({
            method: "POST",
            url: "/jlogistic/dashboardstatisticregion",
            dataType:'json',
            data: {

            },
            beforeSend: function(){
                
            },
            success: function(data) {

            	 totaltransaction = 0;
            	 totalpending = 0; 
            	 totalsending = 0;
            	 totalpendingpercentage = 0; 
            	 totalsendingpercentage = 0;

            	 totalbatchoutdeliver = 0; 
            	 totalbatchsent = 0;
            	 totalbatchundelivered = 0;
            	 totalbatch = 0;

            	 totalbatchoutdeliverpercentage = 0;
            	 totalbatchsentpercentage = 0;
            	 totalbatchundeliveredpercentage = 0;


            	 totalpending = data.data.TransactionLogistic.TotalPending; 
            	 totalsending = data.data.TransactionLogistic.TotalSending;
            	 totaltransaction = totalpending + totalsending;

            	 totalbatchoutdeliver = data.data.TransactionLogistic.TotalBatchPending;
            	 totalbatchsent = data.data.TransactionLogistic.TotalBatchSent;
            	 totalbatchundelivered = data.data.TransactionLogistic.TotalUndelivered;

            	 totalbatch = totalbatchsent + totalbatchundelivered;

            	 totalpendingpercentage = (totalpending / totaltransaction) * 100; 
            	 totalsendingpercentage = (totalsending / totaltransaction) * 100; 
            	 // console.log(totalpendingpercentage);
            	 // totalpendingpercentage = '80%';
            	 $('#divtotalpending').css({"width" : totalpendingpercentage+"%"});
            	 $('#lblpending').html(totalpending);

            	 $('#divtotalsending').css({"width" : totalsendingpercentage+"%"});
            	 $('#lblsending').html(totalsending);

            	 totalbatchoutdeliverpercentage 	= (totalbatchoutdeliver / totalsending) * 100; 
            	 totalbatchsentpercentage 			= (totalbatchsent / totalbatch) * 100; 
            	 totalbatchundeliveredpercentage 	= (totalbatchundelivered / totalbatch) * 100; 

            	 $('#divoutfordelivery').css({"width" : totalbatchoutdeliverpercentage+"%"});
            	 $('#lbloutdeliver').html(totalbatchoutdeliver);

            	 $('#divshipmentcomplete').css({"width" : totalbatchsentpercentage+"%"});
            	 $('#lblshipment').html(totalbatchsent);

            	 $('#divundelivered').css({"width" : totalbatchundeliveredpercentage+"%"});
            	 $('#lblundeliver').html(totalbatchundelivered);
               

                 $("#total-batchpending1").html(data.data.TransactionLogistic.TotalBatchPending1day);
                 $("#total-batchpending2").html(data.data.TransactionLogistic.TotalBatchPending2day);
                 $("#total-batchpending3").html(data.data.TransactionLogistic.TotalBatchPending3day);


            }

        });
	setTimeout(function() {fnProgressbar() }, 260000 );
}



});


</script>

</body>
</html>