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

    .btn1{
      border:0px #FFF transparent; /* this was 1px earlier */
      background-color: #FFF;
      border-color: #FFF;
      border-style: none;
     }

    .myModalAll {
    width: calc(100% - 50% / 3);
    padding: 5px calc(3% - 2px);
    margin-left: calc(10% + 10px);
}
ul,li { margin:0; padding:0; list-style:nont;}
select[multiple] {
        height: 100px;
    }

    .ms-options-wrap > button:focus, .ms-options-wrap > button {
    position: relative;
    width: 100%;
    text-align: left;
    border: 1px solid #ccc !important;
    background-color: #fff;
    padding: 5px 20px 5px 5px;
    margin-top: 1px;
    font-size: 13px;
    color: black !important;
    outline: none;
    white-space: nowrap;
    border-radius: 4px;
    height: 34px;
}
    
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Logistic Transaction Management 
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
                    <h3 class="panel-title"><i class="fa fa-search"></i> Advanced Search</h3>
                </div>
                <div class="panel-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-lg-4">
                               
                                <div class="form-group">
                                    <label for="transaction_from">State</label>
                                    <div >
                                        <select name ="state[]" class="form-control selectpicker" id="SelectionState" multiple>
                                            <option>-</option>
                                            <?php foreach ($States as $key => $value) { ?>
                                            <option value="<?php echo $value->id; ?>" <?php if(!empty(Input::get('state')) && Input::get('state') != "-" && Input::get('state') == $value->id  ){ echo "selected"; } ?>><?php echo $value->name; ?></option>

                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="transaction_from">City</label>
                                    <div >
                                        <select name ="city" class="form-control" id="SelectionCity">
                                            <option>-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="transaction_to">Postcode</label>
                                    <div id="">
                                        {{ Form::text('postcode', null, ['id' => 'postcode', 'class' => 'form-control', 'tabindex' => 2]) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                          
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="transaction_from">From Date</label>
                                    <div class="input-group" id="datetimepicker_from">
                                        <input id="transaction_from" class="form-control" tabindex="2" name="transaction_from" type="text" value="<?php echo !empty(Input::get('transaction_from')) ?  Input::get('transaction_from') : DATE('Y-m-d'); ?>">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="transaction_to">To Date</label>
                                    <div class="input-group" id="datetimepicker_to">
                                        <input id="transaction_to" class="form-control"  tabindex="2" name="transaction_to" type="text" value="<?php echo !empty(Input::get('transaction_to')) ?  Input::get('transaction_to') : DATE('Y-m-d'); ?>" ><span class="input-group-btn">
                                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="form-group @if ($errors->has('status')) has-error @endif">
                            {{ Form::label('status', 'Status', array('class'=> 'col-lg-4 control-label')) }}
                                <div class="col-lg-4">
                                    <div class="col-lg-3">
                                        <label class="checkbox-inline">
                                        {{ Form::checkbox('pending', '0', true, ['id' => 'pending', 'readonly' => 'true', 'class' => 'form-control']) }} {{ Pending }}
                                        </label>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="checkbox-inline">
                                        {{ Form::checkbox('undelivered', '1', true, ['id' => 'undelivered', 'class' => 'form-control']) }} {{ Undelivered }}
                                        </label>
                                    </div>    
                                    <div class="col-lg-3">
                                        <label class="checkbox-inline">
                                        {{ Form::checkbox('returned', '3', true, ['id' => 'returned', 'class' => 'form-control']) }} {{ Returned }}
                                        </label>
                                    </div>
                                    
                                </div>
                            </div> -->

                        </div>
                        <div class="row">
                            <div class="form-group @if ($errors->has('status')) has-error @endif">
                                <div class="form-group">
                                {{ Form::label('status', 'Status', array('class'=> 'col-lg-4 control-label')) }}
                                        <div class="col-lg-10" tyle="width:100%; vertical-align:middle;">
                                            <div class="col-lg-2">
                                                <label class="checkbox-inline">
                                                <?php 
                                                $adopted = (Input::has('pending') == '1' ? '1' : '0');
                                               
                                                // echo $adopted;

                                                // echo \Input::get('pending', true);
                                                //  echo "ssssss";

                                                  if(Input::has('pending'))
                                                  {
                                                    if(Input::has('pending')=='')
                                                    {
                                                      $condition=false;  
                                                    }
                                                    else
                                                    {
                                                       $condition=true;    
                                                    }
                                                    
                                                  }
                                                  else
                                                  {
                                                     $condition=true;
                                                  }
                                                  
                                                ?>
                                                {{ Form::checkbox('pending', '0', (\Input::has('pending')) ? true : $condition, ['id' => 'pending']) }} {{ Pending }}
                                                </label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label class="checkbox-inline">
                                                {{ Form::checkbox('undelivered', '1', true, ['id' => 'undelivered']) }} {{ Undelivered }}
                                                </label>
                                            </div>    
                                            <div class="col-lg-2">
                                                <label class="checkbox-inline">
                                                {{ Form::checkbox('returned', '3', true, ['id' => 'returned']) }} {{ Returned }}
                                                </label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label class="checkbox-inline">
                                                {{ Form::checkbox('partialSent', '2', Input::has('partialSent')? true : false, ['id' => 'partialSent']) }} {{ PartialSent }}
                                                </label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label class="checkbox-inline">
                                                {{ Form::checkbox('sending', '4', Input::has('sending')? true : false, ['id' => 'sending']) }} {{ Sending }}
                                                </label>
                                            </div>
                                            <div class="col-lg-1">
                                                <label class="checkbox-inline">
                                                {{ Form::checkbox('sent', '5', Input::has('sent')? true : false, ['id' => 'sent']) }} {{ Sent }}
                                                </label>
                                            </div>
                                            <div class="col-lg-1">
                                                <label class="checkbox-inline">
                                                {{ Form::checkbox('cancelled', '6', Input::has('cancelled')? true : false, ['id' => 'cancelled']) }} {{ Cancelled }}
                                                </label>
                                            </div>
                                        
                                    </div>
                                    <br><br>
                                </div>
                            </div>
                            
                        </div>
                        {{ Form::submit('Search', ['class' => 'btn btn-primary', 'tabindex' => 11]) }}
                    </form>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Transaction Listing</h3>
                </div>
                <div class="panel-body">
                    <div class="col-md-12" style="padding: 0px 0px 15px 0px; border-bottom: dashed 1px #e4e4e4; margin-bottom: 25px;">
                       
                        <!-- {{ Form::open(array('url' => array('jlogistic/maplocationslist') , 'class' => 'form-horizontal', 'method' => 'PUT')) }} -->
                        <form id="remove_frm" name="remove_frm" action="{{asset('/')}}jlogistic/maplocationslist" method="post" class="form-horizontal">

                        <!-- <div><a href="/Locationlist"><button class="btn btn-default pull-right" id="map-view-all" ><i class="fa fa-map-marker"></i> View Locations</button></a></div> -->
                        <input id="hidstate" class="form-control" name="hidstate[]" type="hidden" value="<?php if(!empty(Input::get('state')) && Input::get('state') != "-"){ echo Input::get('state'); } ?>" >
                        <input id="hidcity" class="form-control" name="hidcity" type="hidden" value="<?php if(!empty(Input::get('city')) && Input::get('city') != "-"){ echo Input::get('city'); } ?>">
                        <input id="hidpostcode" class="form-control" name="hidpostcode" type="hidden" value="<?php if(!empty(Input::get('postcode'))){ echo Input::get('postcode'); } ?>">
                        <input id="hidtransaction_from" class="form-control" name="hidtransaction_from" type="hidden" value="<?php echo !empty(Input::get('transaction_from')) ?  Input::get('transaction_from') : DATE('Y-m-d'); ?>">
                        <input id="hidtransaction_to" class="form-control" name="hidtransaction_to" type="hidden" value="<?php echo !empty(Input::get('transaction_to')) ?  Input::get('transaction_to') : DATE('Y-m-d'); ?>">

                        <input id="hidpending" class="form-control" name="hidpending" type="hidden" value="<?php if(!empty(Input::get('pending'))){ echo Input::get('pending'); } ?>">
                        <input id="hidundelivered" class="form-control" name="hidundelivered" type="hidden"  value="<?php if(!empty(Input::get('undelivered'))){ echo Input::get('undelivered'); } ?>">
                        <input id="hidreturned" class="form-control" name="hidreturned" type="hidden"  value="<?php if(!empty(Input::get('returned'))){ echo Input::get('returned'); } ?>">

                        <input id="hidpartialsent" class="form-control" name="hidpartialsent" type="hidden"  value="<?php if(!empty(Input::get('partialSent'))){ echo Input::get('partialSent'); } ?>">
                        <input id="hidsending" class="form-control" name="hidsending" type="hidden"  value="<?php if(!empty(Input::get('sending'))){ echo Input::get('sending'); } ?>">
                        <input id="hidsent" class="form-control" name="hidsent" type="hidden"  value="<?php if(!empty(Input::get('sent'))){ echo Input::get('sent'); } ?>">
                        <input id="hidcancelled" class="form-control" name="hidcancelled" type="hidden"  value="<?php if(!empty(Input::get('cancelled'))){ echo Input::get('cancelled'); } ?>">

                        <input type="submit" class="btn btn-lg btn-default glyphicon-map-marker pull-right" value="View Locations">

                        </form>
                        <button class="btn1 disabled pull-right" >&nbsp;</button>
                        <button id="map-driver" class="btn btn-lg btn-default pull-right">View Locations By Driver</button>
                        <!-- {{ Form::submit('View Locations', ['class' => 'btn btn-lg btn-default glyphicon-map-marker pull-right','fa-map-marker']) }} -->
                        <button class="btn1 disabled pull-right" >&nbsp;</button><button class="btn btn-lg btn-default pull-right" id="btnBatch"> Batch Statistics</button>
                    </div>

                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-transaction">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Transaction Date</th>
                                    <th>Delivery Name</th>
                                    <th>State</th>
                                    <th>City</th>
                                    <th>Postcode</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel"><i class="fa fa-map-marker"></i> Location <span id="map-transaction-id" style="float: right; margin-right: 10px;color: #589fdc;"></span></h4>
                        </div>
                        <div class="modal-body" style="padding: 0px;">
                             <div class="divLoading load-map-single"> </div>
                            <div id="googleMap" style="width:100%;height:420px;"></div>
                            <div class="col-md-12 col-xs-12" style="padding-top: 14px;">
                                <div class="map-deatils col-md-5 col-xs-12" >
                                    <address>
                                        <strong>Address</strong><br>
                                        <span id="map-add-street-1"></span><br>
                                      <span id="map-add-street-2"></span><br>
                                        <span id="map-add-postcode"></span> <span id="map-add-city"></span><br>
                                        <span id="map-add-state"></span>, <span id="map-add-country"></span><br>
                                      </address>
                                </div>
                                <div class="col-md-7 col-xs-12" style="padding-left: 0px;">
                                    <div class="input-group ">
                                        <input type="text" id="map-marked-location" readonly=""  style="background-color: #fff;" class="form-control" placeholder="selected latitude & longitude">
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" id="mark-transaction-btn" type="button"><i class="fa fa-map-marker"></i> Update Location</button>
                                        </span>
                                    </div>
                                    <span class="help-block" style="font-size:10px;text-align: left;"><i class="fa fa-exclamation-circle"></i> You may update the actual latitude and longitude for delivery location </span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            
                            <!-- /input-group -->
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                  </div>
                </div>

                <div class="modal fade" id="BatchModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                          <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Batch Statistics <!-- (<?php //echo date('Y-m-d');?>) --></h4>
                                    <div class="row">
                                        <div class="col-md-12 col-xs-12" style="margin-bottom:20px;padding-right: 0px;">
                                            <div class="col-md-12">
                                            <div class="btn-group pull-right" role="group" aria-label="..." >
                                                <button type="button" class="btn btn-default" id="nav-left"><i class="fa fa-arrow-left"></i></button>
                                                <button type="button" class="btn btn-default disabled"><i class="fa fa-calendar-o"></i> <span id="selected-date"> <?php echo date("d M Y")?></span></button>
                                                <button type="button" class="btn btn-default" id="nav-right"><i class="fa fa-arrow-right"></i></button>
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span id="selected-date-range">Daily</span> <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                    <input type="hidden" id="start-date-select" value="<?php echo date("Y-m-d")?>">
                                                    <input type="hidden" id="to-date-select" value="<?php echo date("Y-m-d")?>">
                                                    <input type="hidden" id="type-select" value="<?php echo 1;?>">
                                                    
                                                    <input type="hidden" id="start-today" value="<?php echo date("Y-m-d")?>">
                                                    <input type="hidden" id="start-weekly-date" value="">
                                                    <input type="hidden" id="end-weekly-date" value="">
                                                    <input type="hidden" id="start-month-date" value="">
                                                    <input type="hidden" id="end-month-date" value="">
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li ><a class="date-range" data-type="1"> Daily </a></li>
                                                    <li ><a class="date-range" data-type="2"> Weekly </a></li>
                                                    <li ><a class="date-range" data-type="3"> Monthly </a></li>
                                                </ul>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                  </div>

                                <div class="modal-body" style="padding: 0px;">
                                    <div class="col-md-12 col-xs-12" style="padding-top: 14px;">

                                       
                                            
                                            <div class="row"> 
                                                <div class="col-md-12">
                                                    <h5 class="modal-title" id="myModalLabel3">Drivers List</h5>  
                                                    <table class="table table-bordered table-striped" id="table-list-group-batch">

                                                    </table>
                                                </div> 
                                                
                                            </div>

                                            
                                    </div>
                                </div>
                                <div>&nbsp;</div> 
                                 <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                  </div>

                                <!-- <div class="modal-footer">
                                    
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div> -->
                            </div>
                          </div>
                        </div>
                <!-- Modal Eng -->
        </div>
    </div>


@stop


@section('inputjs')

<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">

<script src="http://localhost:8000/js/bootstrap-select.js"></script> -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>

<!-- (Optional) Latest compiled and minified JavaScript translation files -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/i18n/defaults-*.min.js"></script> -->


<?php if (Input::get('state')!=null) {
    $name = Input::get('state'); 
    $value=implode(",",$name);
}
?>
<script>

$(document).ready(function() {

        var name = $('#SelectionState').val([<?php echo $value;?>]);
  
        $('#SelectionState option[value="' + name + '"]').prop('selected', true);

  });

</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDCj_o4KnCEuRzZSmTgIxI1ILBeGCiKec" async defer></script>
 <script>

function initMap(center_lat,center_long,listLocation,mapID) {
  // Create an array of styles.
  // alert('ok');
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
]   

    // Create a new StyledMapType object, passing it the array of styles,
    // as well as the name to be displayed on the map type control.
    var styledMap = new google.maps.StyledMapType(styles,
      {name: "Simple Map"});
    var infowindow = new google.maps.InfoWindow({}); /* SINGLE */  
    // Create a map object, and include the MapTypeId to add
    // to the map type control.
    var myCenter = new google.maps.LatLng(center_lat, center_long);
    var mapOptions = {
        zoom: 15,
        center: myCenter,
        scrollwheel:  true,
        disableDefaultUI: false,
        mapTypeControlOptions: {
          mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
        }
    };
    var map = new google.maps.Map(document.getElementById(mapID),mapOptions);
    google.maps.event.trigger(map, "resize");

    //Associate the styled map with the MapTypeId and set it to display.
    map.mapTypes.set('map_style', styledMap);
    map.setMapTypeId('map_style');
    var locations = listLocation;
    var infowindow = [];
    var marker = [];
    // var grouptransaction = [];
     var arr   =[];
     var temparr    =[];
     var arrcheck =0;
     var exvalue  =0;
     var multi  =0;
    // alert(locations.length);

    for (var i = 0; i < locations.length; i++) { 
        // alert(locations[i][0]);
        // alert(locations[i][1]);
        // alert(locations[i][2]);

        var newmarker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][0], locations[i][1]),
            icon:'/images/map_marker.png',
            title: "'"+locations[i][0]+"'", 
            id:     locations[i][2],
            glat:locations[i][0],
            glong:locations[i][1],
        });
        newmarker.setMap(map);
        newmarker['infowindow'] = new google.maps.InfoWindow({
            content: '<div style="font-weight:bold;">Transaction ID: '+locations[i][2]+'</div>'
        });
        
        
        

        google.maps.event.addListener(newmarker, 'click', function() {
            this['infowindow'].open(map, this);
            // alert(this.id);
            // var existlist = document.getElementById('groubtrans').value;
            
            // alert(result);

            var thisLat=this.glat;
            var thislong=this.glong;
            var thistrans=this.id;

            arrcheck=arrcheck+1;
            

            for (var j = 0; j < locations.length; j++) { 
            if(thisLat==locations[j][0] && thislong==locations[j][1]){
                    arr.push(locations[j][2]);
                    // temparr.push(locations[j][2]);
                    multi=multi+1;
             }
            }

            console.log(this);

        });

        // marker.push(newmarker);
    }
    
    google.maps.event.addListener(map, 'click', function(event){
        $("#map-marked-location").val(event.latLng.lat()+','+event.latLng.lng());

    });
    
    $('body').on('click', '.row-list-location', function(){ 
        map.setZoom(15);
        map.panTo({lat: $(this).data('lat'), lng: $(this).data('long')}); 
        // var transid=$(this).data('tid');
    
    });
    

     
    
    
}



    $(document).ready(function() {

    //DatePicker Start
        
        $(".date-range").click(function(){
            $("#selected-date-range").html($(this).html());
            $('#type-select').val($(this).attr("data-type"))
            
            if($('#type-select').val() == 1){
                // alert('DAIly');
                var type = $('#type-select').val();
                var startDate = $('#start-today').val();
                var toDate = $('#start-today').val();
                getStatistic(0,type,startDate,toDate);
                
            }
            
            if($('#type-select').val() == 2){
                // alert('WEEK');
                var type = $('#type-select').val();
                var startDate = $('#start-weekly-date').val();
                var toDate = $('#end-weekly-date').val();
                getStatistic(0,type,startDate,toDate);
            }
            
            if($('#type-select').val() == 3){
                // alert('Month');
                var type = $('#type-select').val();
                var startDate = $('#start-month-date').val();
                var toDate = $('#end-month-date').val();
                getStatistic(0,type,startDate,toDate);

                
            }
            
        });
        
        $("#nav-left").click(function(){
            // alert('LEFT');
            console.log('LEFT');
            var type = $('#type-select').val();
            var startDate = $('#start-date-select').val();
            var toDate = $('#to-date-select').val();
            getStatistic(1,type,startDate,toDate);


        });
        
        $("#nav-right").click(function(){
            // alert('RIGHT');
            console.log('RIGHT');
            console.log('LEFT');
            var type = $('#type-select').val();
            var startDate = $('#start-date-select').val();
            var toDate = $('#to-date-select').val();
            getStatistic(2,type,startDate,toDate);
            
        });

        function getStatistic(navigation,type,startDate,toDate){
            // alert(startDate+'Arg'+toDate);
            var typename = type;
        $.ajax({
            method: "POST",
            url: "/jlogistic/dashboardstatistic",
            dataType:'json',
            data: {
                'navigation':navigation,
                'rangeType':type,
                'startDate':startDate,
                'toDate':toDate
            },
            beforeSend: function(){
                
            },
            success: function(data) {

               $("#start-date-select").val(data.data.DateSelection.startDate);
               $("#to-date-select").val(data.data.DateSelection.toDate);

                
                if($('#type-select').val() == 1){
                   $("#selected-date").html(data.data.DateSelection.displayStartDate); 
                }else{
                    $("#selected-date").html(data.data.DateSelection.displayStartDate+ ' - '+ data.data.DateSelection.displayEndDate); 
                }
               
                
                $("#start-weekly-date").val(data.data.DateSelection.WeeklyStartDate);
                $("#end-weekly-date").val(data.data.DateSelection.WeeklyEndDate);
                $("#start-month-date").val(data.data.DateSelection.MonthStartDate);
                $("#end-month-date").val(data.data.DateSelection.MonthEndDate);

                 fnBatchlist($("#start-date-select").val(),$("#to-date-select").val());   


                 // if(typename ==1){
                 //    fnBatchlist(data.data.DateSelection.displayStartDate,data.data.DateSelection.displayEndDate);   
                 // }   
                 // else if(typename ==2){
                 //    fnBatchlist(data.data.DateSelection.WeeklyStartDate,data.data.DateSelection.WeeklyEndDate);   
                 // }
                 // else if(typename ==3){
                 //    fnBatchlist(data.data.DateSelection.MonthStartDate,data.data.DateSelection.MonthEndDate);  
                 // }

                
            },
            error: function (error) {
                location.reload();
            }
        })
    } 

    $('body').on('click','#btnBatch', function(){
            // alert('ok');
            var startDate = $('#start-today').val();
            var toDate = $('#start-today').val();
            getStatistic(0,1,startDate,toDate);

            // fnBatchlist(startDate,toDate);

        });   

    function fnBatchlist(startDate,toDate){
            var listg='';
             // alert('======'+startDate+'___'+toDate+'=======');
            $("#BatchModal").modal('show');  

               $.ajax({
                    method: "POST",
                    url: "/jlogistic/batchlists",
                    dataType:'json',
                    data: { 
                        'startDate':startDate,
                        'toDate':toDate
                    },
                    beforeSend: function(){
                        
                    },
                    success: function(data) {

                        listg='<tr><td>ID</td><td>Driver Name</td><td>Total Assigned Batches</td></tr>'; 

                         $.each(data, function (index, value) {
                           
                               listg=listg+'<tr><td>'+value.id+'</td><td>'+value.driver+'</td><td>'+value.total+'</td></tr>';
                                 // listg=listg+'&nbsp; <a href=edit/'+value.id+' target="_blank">Assign Driver</a> </td></tr>';
                        });

                         if(data.length ==0){
                            listg=listg+'<tr><td colspan=2 align=center>No Record Found</td></tr>'; 
                         }

                          $("#table-list-group-batch").html(listg);

                    }
                });
        } 

        //Date Range end

        loadCityList($("#SelectionState").val(),'<?php echo Input::get('city'); ?>');
        
        $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
            format: 'YYYY-MM-DD'
        }); 
    
    var blank="";
    var vpending = $('#pending').val();
        $('#hidpending').val(vpending); 

    var vundelivered = $('#undelivered').val(); 
        $('#hidundelivered').val(vundelivered);

    var vundelivered = $('#undelivered').val(); 
        $('#hidundelivered').val(vundelivered);



    $('#pending').click(function() {
      if ($(this).is(':checked')) {
        // $('#pending').attr('checked', false);
        $('#hidpending').val(vpending); 
       // $('span input').attr('checked', true);
      } else {
        $('#hidpending').val(blank); 

      }
    });


    $('#undelivered').click(function() {
      if ($(this).is(':checked')) {
        $('#hidundelivered').val(vpending); 
      } else {
        $('#hidundelivered').val(blank); 
      }
    });

    $('#returned').click(function() {
      if ($(this).is(':checked')) {
        $('#hidreturned').val(vpending); 
      } else {
        $('#hidreturned').val(blank); 
      }
    });

    $('#partialSent').click(function() {
      if ($(this).is(':checked')) {
        $('#hidpartialsentp').val(vpending); 
      } else {
        $('#hidpartialsentp').val(blank); 
      }
    });

    $('#sending').click(function() {
      if ($(this).is(':checked')) {
        $('#hidsending').val(vpending); 
      } else {
        $('#hidsending').val(blank); 
      }
    });

    $('#sent').click(function() {
      if ($(this).is(':checked')) {
        $('#hidsent').val(vpending); 
      } else {
        $('#hidsent').val(blank); 
      }
    });

    $('#cancelled').click(function() {
      if ($(this).is(':checked')) {
        $('#hidcancelled').val(vpending); 
      } else {
        $('#hidcancelled').val(blank); 
      }
    });
    
       

    $("#SelectionState").change(function(){
            if($("#SelectionState").val() > 0 ){
                // Load list
                loadCityList($("#SelectionState").val(),"");
                var cstate = $("#SelectionState").val();
                $("#hidstate").val(cstate);
                // alert($("#hidstate").val());
            }else{
                // set to default
            }
        });



    $("#SelectionCity").change(function(){
            if($("#SelectionCity").val() > 0 ){
                var ccity = $("#SelectionCity").val();
                $("#hidcity").val(ccity);
                // alert($("#hidcity").val());
            }
        });
    
    $("#postcode").change(function(){
            if($("#postcode").val() > 0 ){
                var cpostcode = $("#postcode").val();
                $("#hidpostcode").val(cpostcode);
                // alert($("#hidpostcode").val());
            }
        });

   
    $("#datetimepicker_from").on("dp.change", function(e) {
            var ctransactionfrom = $("#transaction_from").val();
            $("#hidtransaction_from").val(ctransactionfrom);
        });


    $("#datetimepicker_to").on("dp.change", function(e) {
            var ctransactionto = $("#transaction_to").val();
            $("#hidtransaction_to").val(ctransactionto);
        });

  


     $('body').on('click', '#map-view-all', function(){ 
             getAllLatLong();
        });

    function loadCityList(state_id,selectedCity){
            $.ajax({
                method: "GET",
                url: "/cities",
                dataType:'json',
                data: {
                    'state_id':state_id
                },
                beforeSend: function(){
                     var selectHTML = '<option>-</option>';
                     $("#SelectionCity").html(selectHTML);
                },
                success: function(data) {
                    var selectHTML = '<option> - </option>';
                    $( data).each(function( index ) {
                        if((data.length > 0) && (selectedCity == data[index].id)){
                            selectHTML = selectHTML + '<option value="'+data[index].id+'" selected>'+data[index].name+'</option>' ;
                        } else{
                            if(data.length > 0){
                                selectHTML = selectHTML + '<option value="'+data[index].id+'">'+data[index].name+'</option>' ;
                            }   
                        }
                    });
                    
                    $("#SelectionCity").html(selectHTML);
                }
            })
        }

    function getAllLatLong(){

            var state = $("#SelectionState").val();
            var city = $("#SelectionCity").val();
            var postcode = $("#postcode").val();
            var transaction_from = $("#transaction_from").val();
            var transaction_to = $("#transaction_to").val();
            var listLocation = [];
            var showall = $('#groubtrans').val();
            var pending         = $('#pending').val();
            var undelivered     = $('#undelivered').val();
            var returned        = $('#returned').val();
            var partialsent     = 0;
            var sending         = 0;
            var sent            = 0;
            var cancelled       = 0;

            
            if($("input[name='partialsent']").prop("checked")){
                    partialsent=2;
                }
            if($("input[name='sending']").prop("checked")){
                    sending=4;
                }
            if($("input[name='sent']").prop("checked")){
                    sent=5;
                }
            if($("input[name='cancelled']").prop("checked")){
                    cancelled=6;
                }

            $("#myModalAll").modal('show');
            $("div.load-map-all").addClass('show');
            
            $.ajax({
               method: "POST",
               url: "/jlogistic/maplocations",
               dataType:'json',
               data: {
                   'state':state,
                   'city':city,
                   'postcode':postcode,
                   'transaction_from':transaction_from,
                   'transaction_to':transaction_to,
                   'showall':showall,
                   'pending':pending,
                   'undelivered':undelivered,
                   'returned':returned,
                   'partialsent':partialsent,
                   'sending':sending,
                   'sent':sent,
                   'cancelled':cancelled


               },
               beforeSend: function(){

               },
               success: function(data) {
                    var list = "";
                    if(data.length==0)
                    {
                       
                       alert('No transactions found on the map. Please show all group');
                        // bootbox.alert("No transactions found on the map. Please show all group", function(e){
                        //                 parent.$.fn.colorbox.close();
                        //             });
                        // $('#groubtrans').focus();
                        
                    }
                    else 
                    {
                         // alert('ok');
                        <?php 
                         // Redirect::to('jlogistic/maplocations');
                        ?>
                       
                    // window.Location.href='{{asset('/')}}jlogistic/locationlist/xyz='+data;

                    // $.each(data, function (index, value) {
                        
                    //     var address = ' '+value.address.street_1 +', '+value.address.street_2 +' '+ value.address.postcode +' '+ value.address.city +' '+ value.address.state +', '+ value.address.country ;
                        
                    //     list = list + '<tr lass="row-list-location" data-transid="'+value.ltid+'" data-lat="'+value.latitude+'" data-long="'+value.longitude+'"><td valign="top"><div class="info-box" ><span class="info-id">'
                    //     +value.transaction_id+'</span><br><span class="info-address row-list-location" data-transid="'+value.ltid+'" data-tid="'+value.transaction_id+'" data-lat="'+value.latitude+'" data-long="'+value.longitude+'"><i class="fa fa-map-marker" style="color: #c16161;"></i>'
                    //     +address+'</span></div></td></tr>'
                    //     listLocation.push([value.latitude,value.longitude,value.transaction_id]);
                    // });
                    
                    // initMap(listLocation[0][0],listLocation[0][1],listLocation,'googleMapContainerAll');
                    // $("#table-list").html(list);
                    // $("div.load-map-all").removeClass('show');
                   }
               }
            })
            
        }

     $('body').on('click', '.triggerMap', function(){ 
            
            var transactionID = $(this).data('transaction-id')
            $("#mark-transaction-btn").attr("data-mark-transaction",transactionID);
            getLatLong(transactionID);
            $("#myModal").modal('show');
            
        })   


     function getLatLong(transactionID){
            $.ajax({
                method: "POST",
                url: "/jlogistic/getlocation",
                dataType:'json',
                data: {
                    'transactionID':transactionID
                },
                beforeSend: function(){
                    $("div.load-map-single").addClass('show');
                },
                success: function(data) {
                    var list = [[data.latitude,data.longitude,data.transaction_id]];
                    initMap(data.latitude,data.longitude,list,'googleMap');

                    $("#map-add-street-1").html(data.address.street_1);
                    $("#map-add-street-2").html(data.address.street_2);
                    $("#map-add-postcode").html(data.address.postcode);
                    $("#map-add-city").html(data.address.city);
                    $("#map-add-state").html(data.address.state);
                    $("#map-add-country").html(data.address.country);
                    $("#map-transaction-id").html(transactionID);
                    $("div.load-map-single").removeClass('show');
                    //
                    
                }
            })
        }


    $("#mark-transaction-btn").click(function(){
            
            var transactionID = $("#mark-transaction-btn").data("mark-transaction");
            console.log(transactionID);
            var latlong = $("#map-marked-location").val();
            $.ajax({
                method: "POST",
                url: "/jlogistic/updatelatlong",
                dataType:'json',
                data: {
                    'transactionID':transactionID,
                    'latlong':latlong
                },
                beforeSend: function(){
                    
                },
                success: function(data) {
                    if(data.response == 1){
                        var list = [[data.latitude,data.longitude]];
                        initMap(data.latitude,data.longitude,list,'googleMap');
                    }else{
                        
                    }
                }
            })
        })


    $('#dataTables-transaction').dataTable({
                "autoWidth": false,
                "processing": true,
                "serverSide": true,
                "ajax": "{{ URL::to('jlogistic/locationlisting?isDatatables=1&'.http_build_query(Input::all())) }}",
                "order": [[0,'desc']],
                "columnDefs": [{
                    "targets": "_all",
                    "defaultContent": ""
                }],
                "columns": [
                // { "data" : "0", "searchable" : false},
                // { "data" : "1", "orderable" : false, "searchable" : false}
                { "data" : "transaction_id"},
                { "data" : "transaction_date"},
                { "data" : "delivery_name" },
                { "data" : "delivery_state" },
                { "data" : "delivery_city" },
                { "data" : "delivery_postcode" },
                { "data" : "status" },
                { data: function ( row, type, val, meta ) {
                     return '<button class="btn btn-primary triggerMap" data-transaction-id="'+row.transaction_id+'" type="button" title="Map Location"><i class="fa fa-map-marker"></i></button>';
                }
                },
                ]
            });

     });
     
    $('#map-driver').on('click', function() {

        var stateId = $('#hidstate').val();
        var cityId = $('#hidcity').val();
        var postCode = $('#hidpostcode').val();
        var transactionFrom = $('#hidtransaction_from').val();
        var transactionTo = $('#hidtransaction_to').val();
        var showAll = $('#groubtrans').val();
        var pending = $('#hidpending').val();
        var undelivered = $('#hidundelivered').val();
        var returned = $('#hidreturned').val();
        var partialSent = $('#hidpartialsent').val();
        var sending = $('#hidsending').val();
        var sent = $('#hidsent').val();
        var cancelled = $('#hidcancelled').val();

        var query = 'hidstate='+stateId+'&hidcity='+cityId+'&hidpostcode='+postCode+'&hidtransaction_from='+transactionFrom+'&hidtransaction_to='+transactionTo+'&showall='+showAll+'&hidpending='+pending+'&hidundelivered='+undelivered+'&hidreturned='+returned+'&hidpartialsent='+partialSent+'&hidsending='+sending+'&hidsent='+sent+'&hidcancelled='+cancelled;

        window.location.href='/jlogistic/maplocationsdriverlist';

    });

</script>
@stop        