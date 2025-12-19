@extends('layouts.master')
@section('title', 'Logistic Transaction')
@section('content')
<style>
 

    .row-list-location{
        height: 65px;
        background-color: #fbfbfb;
        border-bottom: solid 1px #efefef;
        cursor:pointer;
    }
    .row-list-location:hover{
        height: 65px;
        background-color: #fff;
        border-bottom: solid 1px #efefef;
        cursor:pointer;
    }
    .info-id{
        font-weight: bold;color: #6a6c6d;
    }
    
    .info-address{
        font-size:12px;
    }
    
    .info-box{
        padding: 5px;padding-left: 10px;
    }
    
    #map-all-list-container::-webkit-scrollbar { 
    display: none; 
    }

    #map-all-group-container::-webkit-scrollbar { 
    display: none;
    }


    
    .divLoading
    {
        display : none;
    }
    .divLoading.show
    {
        display : block;
        position : absolute;
        z-index: 100;
        background-image : url('http://loadinggif.com/images/image-selection/10.gif');
        background-color:#f7f7f7;
        opacity : 0.4;
        background-repeat : no-repeat;
        background-position : center;
        left : 0;
        bottom : 0;
        right : 0;
        top : 0;
    }
    .loadinggif.show
    {
        left : 50%;
        top : 50%;
        position : absolute;
        z-index : 101;
        width : 32px;
        height : 32px;
        margin-left : -16px;
        margin-top : -16px;
    }


    .myModalAll {
    width: calc(100% - 50% / 3);
    padding: 5px calc(3% - 2px);
    margin-left: calc(10% + 10px);
}
    .vcenter{
    position: relative;
    top: 50%;
    transform: translateY(-75%); 
    }

    .arelative{
        position: relative;
    }

    #legend {
        font-family: Arial, sans-serif;
        background: #fff;
        padding: 10px;
        margin: 10px;
        border: 3px solid #000;
      }
      #legend h3 {
        margin-top: 0;
      }
      #legend img {
        vertical-align: middle;
      }

    select[multiple] {
        height: 100px;
    }
    
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Logistic Transaction Driver Management
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}jlogistic/locationlist"><i class="fa fa-refresh"></i></a>
                    <!-- @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 5, 'AND'))
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}transaction/add"><i class="fa fa-plus"></i></a>
                    @endif -->
                </span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            @if (Session::has('message'))
            <div class="alert alert-danger">
                <i class="fa fa-exclamation"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
            @endif
            @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-search"></i> Location Map</h3>
                    <a href="{{asset('/')}}jlogistic/locationlist" class="btn btn-primary pull-right vcenter" role="button"><span class="glyphicon glyphicon-search"></span> Search Again</button> </a>

                    <!-- <span><button id="btnSearch" name="btnSearch" class="btn btn-primary pull-right vcenter ">View Batch Statistics</button>
                    </span> -->
                    
                </div>

                <div lass="panel-body">
                    <form method="POST" name="frmmap" id="frmmap">
                        <div class="row">
                            <div class="col-lg-4"  style="width: 100%; height: 80%;">
                                <div class="form-group">
                                    <div>
								          <div class="divLoading load-map-all"> </div>
								          <div class="row" style="height: 900%; width: 100%;">
								                <div class="col-md-9" style="height: 80%; width: 80%; ">
								                    <div id="googleMapContainerAll" style="width: 100%; height: 740px;"></div>
								                </div>

                                                <div class="col-md-3" style="height: 80%; width: 20%; ">
                                                    
                                                    <label for="transaction_from">From Date</label>
                                                    <div class="input-group" id="datetimepicker_from">
                                                        <input id="transaction_from" class="form-control" tabindex="2" name="transaction_from" type="text">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                                        </span>
                                                    </div>
                                                    <label for="transaction_from">To Date</label>
                                                    <div class="input-group" id="datetimepicker_to">
                                                        <input id="transaction_to" class="form-control" tabindex="2" name="transaction_from" type="text">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                                        </span>
                                                    </div>
                                                    <button type="button" class="btn btn-default" id="allTransaction">All Transactions</button>
                                                    <br><br>
                                                    <label for="transaction_from">Driver List</label>
                                                    <div id="driverList" style="width: 100%; height: 740px;">
                                                        <div class="list-group" id="list-tab" role="tablist">
                                                          @foreach ($driver_list as $driver)
                                                            <a class="list-group-item list-group-item-action" id="list-home-list" data-toggle="list" href="#list-home" role="tab" aria-controls="home" data-driver-id="{{$driver->id}}">{{$driver->name}}</a>
                                                          @endforeach
                                                        </div>
                                                    </div>
                                                </div>
								          </div>
                                    </div>
                                </div>
                            </div>
                         <!--    <pre>
                        <?php //print_r($inputparams)."newww..."; ?>
                        </pre> -->

                        </div>
                    </form>
                    <div id="legend"><h3>Legend</h3></div>
                </div>
            </div>

        </div>
    </div>

@stop

@section('inputjs')

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDcTN4TPOfZRUmCF_7S_4w3sFlxaGEr3f4&callback=initMap" async defer></script>
<!-- Latest compiled and minified JavaScript -->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>
<!-- {{ HTML::script('css/bootstrap-select.min.css') }} -->
{{ HTML::script('js/bootstrap-select.min.js') }}



<!-- (Optional) Latest compiled and minified JavaScript translation files -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/i18n/defaults-*.min.js"></script>
 -->

<script>

    var map;
    var markers = [];

    // var iconBase = 'https://maps.google.com/mapfiles/kml/shapes/';
    var iconBase = 'http://maps.google.com/mapfiles/ms/icons/';
    var icons = {
        "0": {
          name: 'Pending',
          icon: iconBase + 'yellow-dot.png'
        },
        "4": {
          name: 'Sending',
          icon: iconBase + 'green-dot.png'
        },
        "3": {
          name: 'Returned',
          icon: iconBase + 'blue-dot.png'
        },
        "1": {
          name: 'Undelivered',
          icon: iconBase + 'red-dot.png'
        }
    };


    function initMap() {
  // Create an array of styles.

  var styles = [
    {
        "featureType": "all",
        "stylers": [
            {
                "saturation": 0
            },
            {
                "hue": "#e7ecf0"
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
  ];

    // Create a new StyledMapType object, passing it the array of styles,
    // as well as the name to be displayed on the map type control.
    var styledMap = new google.maps.StyledMapType(styles,{name: "Simple Map"});
    var infowindow = new google.maps.InfoWindow({}); /* SINGLE */  
    // Create a map object, and include the MapTypeId to add
    // to the map type control.
    var myCenter = new google.maps.LatLng(3.111236, 101.667445);
    var mapOptions = {
        zoom: 15,
        center: myCenter,
        scrollwheel:  true,
        disableDefaultUI: false,
        mapTypeControlOptions: {
          mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
        }
    };
    map = new google.maps.Map(document.getElementById('googleMapContainerAll'),mapOptions);
    google.maps.event.trigger(map, "resize");

    //Associate the styled map with the MapTypeId and set it to display.
    map.mapTypes.set('map_style', styledMap);
    map.setMapTypeId('map_style');
    // var locations = listLocation;


    var legend = document.getElementById('legend');
  for (var key in icons) {
    var type = icons[key];
    var name = type.name;
    var icon = type.icon;
    var div = document.createElement('div');
    div.innerHTML = '<img src="' + icon + '"> ' + name;
    legend.appendChild(div);
  }

  map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend);
  $("div.load-map-all").removeClass('show');
    
    google.maps.event.addListener(map, 'click', function(event){
        $("#map-marked-location").val(event.latLng.lat()+','+event.latLng.lng());
    });



}

function updateMap(coordinates) {
  $("div.load-map-all").removeClass('show');

    removeMarkers();
    if (coordinates.length > 0) {
        

        for (var i = 0; i < coordinates.length; i++) { 

            var newmarker = new google.maps.Marker({
                position: new google.maps.LatLng(coordinates[i][0], coordinates[i][1]),
                icon:icons[coordinates[i][3]].icon,
                title: "'"+coordinates[i][3]+"'", 
                id:     coordinates[i][2],
                glat:coordinates[i][0],
                glong:coordinates[i][1],
                type: 'parking'
            });
            newmarker.setMap(map);
            newmarker['infowindow'] = new google.maps.InfoWindow({
                content: '<div style="font-weight:bold;">Transaction ID: '+coordinates[i][2]+'</div>'
            });
            markers.push(newmarker);

            google.maps.event.addListener(newmarker, 'click', function() {
                this['infowindow'].open(map, this);
                // alert(this.id);
                // var existlist = document.getElementById('groubtrans').value;
                
                // alert(result);

                var thisLat=this.glat;
                var thislong=this.glong;
                var thistrans=this.id;

                // arrcheck=arrcheck+1;
                

                // for (var j = 0; j < locations.length; j++) { 
                // if(thisLat==locations[j][0] && thislong==locations[j][1]){
                //         arr.push(locations[j][2]);
                //         // temparr.push(locations[j][2]);
                //         multi=multi+1;
                //  }
                // }

                console.log(this);

            });

        }
    }
    
}

function removeMarkers() {

    if (markers.length > 0) {
        for (i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
            markers[i] = null;
        }
    }

    markers = [];

}

Array.prototype.unique = function () {
    var r = new Array();
    o:for(var i = 0, n = this.length; i < n; i++)
    {
        for(var x = 0, y = r.length; x < y; x++)
        {
            if(r[x]==this[i])
            {
                continue o;
            }
        }
        r[r.length] = this[i];
    }
    return r;
}
</script>

<script>

    $(document).ready(function() {

        $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        $('a[data-toggle="list"]').on('click', function (e) {

            var transaction_from = $("#transaction_from").val();
            var transaction_to = $("#transaction_to").val();
            var driver_id = $(this).attr('data-driver-id');
            $("div.load-map-all").addClass('show');

            
            $.ajax({
               method: "POST",
               url: "/jlogistic/mapdriverlocations",
               dataType:'json',
               data: {
                   'transaction_from':transaction_from,
                   'transaction_to':transaction_to,
                   'driver_id':driver_id
               },
               beforeSend: function(){

               },
               success: function(data) {
                    var list = "";
                    console.log(data);
                    var listLocation = [];
                    if(data.length==0)
                    {
                       
                    }
                    else 
                    {
                        $.each(data, function (index, value) {

                            var address = ' '+value.address.street_1 +', '+value.address.street_2 +' '+ value.address.postcode +' '+ value.address.city +' '+ value.address.state +', '+ value.address.country ;
                            
                            list = list + '<tr lass="row-list-location" data-transid="'+value.ltid+'" data-lat="'+value.latitude+'" data-long="'+value.longitude+'"><td valign="top"><div class="info-box" ><span class="info-id">'
                            +value.transaction_id+'</span><br><span class="info-address row-list-location" data-transid="'+value.ltid+'" data-tid="'+value.transaction_id+'" data-lat="'+value.latitude+'" data-long="'+value.longitude+'"><i class="fa fa-map-marker" style="color: #c16161;"></i>'
                            +address+'</span></div></td></tr>'
                            listLocation.push([value.latitude,value.longitude,value.transaction_id,value.status]);
                        });
                        
                   }
                   updateMap(listLocation);
                   $("div.load-map-all").removeClass('show');
               }
            })
        });
       
        $('#allTransaction').on('click', function() {
            $('#transaction_from').val(null);
            $('#transaction_to').val(null);
        });

    });


    


 </script>
@stop