@extends('layouts.master')
@section('title', 'Transaction')
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
    
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Transaction Management
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}transaction"><i class="fa fa-refresh"></i></a>
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
                                        <select name ="state" class="form-control" id="SelectionState">
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
                        <div><button class="btn btn-default pull-right" id="map-view-all" ><i class="fa fa-map-marker"></i> View Locations</button></div>
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
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Large -->
<div class="modal fade" id="myModalAll" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="width: 100%; padding-right: 10%;padding-left: 10%;" >
        <div class="modal-content" >
      <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel"><i class="fa fa-map-marker"></i> Location </h4>
        </div>
        <div class="modal-body" style="padding: 0px;">
            <div class="divLoading load-map-all"> </div>
          <div class="row" style="height: 740px;">
                <div class="col-md-9" style="border-right: solid 1px #d4d4d4;height: 100%;  padding-right: 0px;">
                    <div id="googleMapContainerAll" style="width:100%;height:100%;pos"></div>
                     
                </div>
                <div class="col-md-3" id="map-all-list-container" style="max-width: 400px;padding-left: 0px;overflow-y: auto;height: 740px;" >
                    <table width="100%" id="table-list" >
                          
                      </table>
                </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
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
@stop
@section('inputjs')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDCj_o4KnCEuRzZSmTgIxI1ILBeGCiKec" async defer></script>
<script>

    
function initMap(center_lat,center_long,listLocation,mapID) {
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
        zoom: 13,
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
    
    for (var i = 0; i < locations.length; i++) { 
        var newmarker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][0], locations[i][1]),
            icon:'/images/map_marker.png',
            title: "'"+locations[i][0]+"'",
        });
        newmarker.setMap(map);
        newmarker['infowindow'] = new google.maps.InfoWindow({
            content: '<div style="font-weight:bold;">Transaction ID: '+locations[i][2]+'</div>'
        });

        google.maps.event.addListener(newmarker, 'click', function() {
            this['infowindow'].open(map, this);
        });
        marker.push(newmarker);
    }
    
    google.maps.event.addListener(map, 'click', function(event){
        $("#map-marked-location").val(event.latLng.lat()+','+event.latLng.lng());
    });

    
    $('body').on('click', '.row-list-location', function(){ 
        map.setZoom(15);
        map.panTo({lat: $(this).data('lat'), lng: $(this).data('long')}); 
    });
    
    
    
}

</script>
<script>
    $(document).ready(function() {
        
        loadCityList($("#SelectionState").val(),'<?php echo Input::get('city'); ?>');
        
        $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
            format: 'YYYY-MM-DD'
        }); 
       
        $('body').on('click', '#map-view-all', function(){ 
             getAllLatLong();
        });

        $('body').on('click', '.triggerMap', function(){ 
            
            var transactionID = $(this).data('transaction-id')
            $("#mark-transaction-btn").attr("data-mark-transaction",transactionID);
            getLatLong(transactionID);
            $("#myModal").modal('show');
            
        });
        
        
        $("#SelectionState").change(function(){
            if($("#SelectionState").val() > 0 ){
                // Load list
                loadCityList($("#SelectionState").val(),"");
            }else{
                // set to default
            }
        })
        
        $("#mark-transaction-btn").click(function(){
            
            var transactionID = $("#mark-transaction-btn").data("mark-transaction");
            var latlong = $("#map-marked-location").val();
            
            $.ajax({
                method: "POST",
                url: "/transaction/updatelatlong",
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
        
        function getAllLatLong(){

            var state = $("#SelectionState").val();
            var city = $("#SelectionCity").val();
            var postcode = $("#postcode").val();
            var transaction_from = $("#transaction_from").val();
            var transaction_to = $("#transaction_to").val();
            var listLocation = [];
            $("#myModalAll").modal('show');
            $("div.load-map-all").addClass('show');
            $.ajax({
               method: "POST",
               url: "/transaction/maplocations",
               dataType:'json',
               data: {
                   'state':state,
                   'city':city,
                   'postcode':postcode,
                   'transaction_from':transaction_from,
                   'transaction_to':transaction_to
               },
               beforeSend: function(){

               },
               success: function(data) {
                    var list = "";
                    $.each(data, function (index, value) {
                        
                        var address = ' '+value.address.street_1 +', '+value.address.street_2 +' '+ value.address.postcode +' '+ value.address.city +' '+ value.address.state +', '+ value.address.country ;
                        
                        list = list + '<tr class="row-list-location" data-lat="'+value.latitude+'" data-long="'+value.longitude+'"><td valign="top"><div class="info-box" ><span class="info-id">'
                        +value.transaction_id+'</span><br><span class="info-address"><i class="fa fa-map-marker" style="color: #c16161;"></i>'
                        +address+'</span></div></td></tr>'
                        listLocation.push([value.latitude,value.longitude,value.transaction_id]);
                    });
                    
                    initMap(listLocation[0][0],listLocation[0][1],listLocation,'googleMapContainerAll');
                    $("#table-list").html(list);
                    $("div.load-map-all").removeClass('show');
               }
            })
            
        }
        
        function getLatLong(transactionID){
            $.ajax({
                method: "POST",
                url: "/transaction/getlocation",
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
        
        
        $('#dataTables-transaction').dataTable({
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{ URL::to('transaction/locationlisting?isDatatables=1&'.http_build_query(Input::all())) }}",
            "order": [[0,'desc']],
            "columnDefs": [{
                "targets": "_all",
                "defaultContent": ""
            }],
            "columns": [
            // { "data" : "0", "searchable" : false},
            // { "data" : "1", "orderable" : false, "searchable" : false}
            { "data" : "id"},
            { "data" : "transaction_date"},
            { "data" : "delivery_name" },
            { "data" : "delivery_state" },
            { "data" : "delivery_city" },
            { "data" : "delivery_postcode" },
            { data: function ( row, type, val, meta ) {
                return '<button class="btn btn-primary triggerMap" data-transaction-id="'+row.id+'" type="button" title="Map Location"><i class="fa fa-map-marker"></i></button>';
            }
            },
            ]
        });
        
    });
</script>
@stop
