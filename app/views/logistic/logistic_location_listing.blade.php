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

select[multiple] {
        height: 100px;
    }
    
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Logistic Transaction Management 
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}jlogistic/location"><i class="fa fa-refresh"></i></a>
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
        </div>
    </div>
</div>
<!-- Modal Large -->
<div class="modal fade" id="myModalAll" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="width:100%; overflow-y: auto;overflow-x: auto; height: 100%;">
    <div class="modal-dialog" style="width: 100%; padding-right: 10%;padding-left: 10%;" >
        <div class="modal-content" >
      <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel"><i class="fa fa-map-marker"></i> Location </h4>
        </div>
        <div class="modal-body" style="padding: 0px;">
            <div class="divLoading load-map-all"> </div>
          <div class="row" style="height: 700px;">
                <div class="col-md-9" style="border-right: solid 1px #d4d4d4;height: 100%; width: 70%; padding-right: 0px;">
                    <div id="googleMapContainerAll" style="width:100%;height:100%;pos"></div>
                     
                </div>
                <div class="col-md-9" d="map-all-list-container" style="width: 30%; padding-left: 0px;overflow-y: auto;overflow-x: auto;height: 440px;" >
                    <div id="table-list" style="width:100%;" >
                        

                    </div>

                    <!-- <table width="100%" id="table-list" >
                          
                      </table> -->
                </div>
                <div class="col-md-9" d="map-all-group-container" style="width: 30%;padding-left: 5px;overflow-y: auto;overflow-x: auto;height: 300px;" >
                     <div id="table-list-group" style="width:100%; pos" >
                        

                    </div>
                   <!--  <table width="100%" id="table-list-group" >
                        
                      </table> -->
                </div>

          </div>
      </div>

      <!-- <div class="modal-footer">
        <input type="submit" name="btnSubmit"> 
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div> -->
      <div class="row" style="padding: 15px;">
         {{ Form::open(array('url'=>'jlogistic/printdo/', 'class' => 'form-horizontal','id'=>'thisForm')) }}
          <!-- <form method="POST"> -->
             <div class="col-xs-6" lass="modal-footer" style="border-top: 1px solid #e5e5e5; padding-top:5px;">
                 <div>
                   <!--  <input type="button" name="btnGroup" id="btnGroup" value="Display Current Group" class="btn btn-default">&nbsp; -->
                   <span>
                   <!--  <input type="button" name="btnPrintDO" id="btnPrintDO" value="Download" class="btn btn-default">  -->
                   <!-- <div class="dropdown">
                      <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Action
                      <span class="caret"></span></button>
                      <ul class="dropdown-menu">
                        <li><a name="btnMake" id="btnMake" href="javascript:void(0)">Make Group</a></li>
                        <li><a name="btnShow" id="btnShow" href="javascript:void(0)">Show Group</a></li>
                        <li><a name="btnDelete" id="btnDelete" href="javascript:void(0)">Delete Group</a></li>
                        <li><a name="btnShowall" id="btnShowall" href="javascript:void(0)">Show all</a></li>
                        <li><a name="btnReset" id="btnReset" href="javascript:void(0)">Reset</a></li>
                        <li><a name="btnPrint" id="btnPrint" href="javascript:submit();">Print</a></li>
                      </ul>
                    </div> -->

                    <input type="button" name="btnPrint" id="btnPrint" value="Print" class="btn btn-default" disabled>
                    <input type="button" name="btnReset" id="btnReset" value="Reset" class="btn btn-default">
                    <input type="button" name="btnMake" id="btnMake" value="Make Group" class="btn btn-default">
                    <input type="button" name="btnShow" id="btnShow" value="Show Group" class="btn btn-default">
                    <input type="button" name="btnDelete" id="btnDelete" value="Delete Group" class="btn btn-default">
                    <input type="button" name="btnShowall" id="btnShowall" value="Show all" class="btn btn-default">
                    <input type="button" name="btnUpdate" id="btnUpdate" value="Update Group" class="btn btn-default disabled">

                   
                    

                   </span>
                    &nbsp;

                 </div>
                 <div class="col-lg-4">
                    <div class="form-group">
                        <label>Group Transaction </label> 
                        <div >
                           <textarea id="groubtrans" class="form-control" style="width: 300px;" name="groubtrans" > </textarea>
                           <input type="hidden" name="hidgroup" id="hidgroup">

                        </div>
                    </div>

                </div>


            </div>
             <div class="col-xs-6" style="text-align: right; border-top: 1px solid #e5e5e5; padding-top:5px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
         {{ Form::close() }}
        <!-- </form> -->
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
<!-- DO Modal  -->
<div class="modal fade" id="doModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-body" style="padding: 0px;">
            <div class="col-md-12 col-xs-12" style="padding-top: 14px;">
                  <table width="100%" id="table-list-do" >
                        
                  </table>  
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>

<!-- (Optional) Latest compiled and minified JavaScript translation files -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/i18n/defaults-*.min.js"></script>

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

          // alert(multi);
          // multi=0;
                 


            arr.push(this.id);
           
            var unique = arr.unique();
            // var strblank = "";
          
            // alert('n'+temparr);
            // var result = existlist.indexOf(temparr);
          

            // if(exvalue==this.id)
            // {
            //     exvalue=this.id;
                    
            //         for (var k = 0; k < locations.length; k++) { 
            //         if(exvalue==locations[k][2]){
            //             var alen=k;
            //             unique.splice(alen, 1);
            //             this['infowindow'].close(map, this);  
            //             arrcheck  = 0; 
            //             arr=unique;
            //          }
            //         }
            //         exvalue=0;     
            // }
            // else
            // {
            //     exvalue=this.id;
            // }

            
            
           
            // alert(arrcheck);
            

            // if(result==0)
            // {
            //     var str=existlist.replace(temparr,strblank);
            //     alert('k'+str);
            //     console.log(str);
            //    document.getElementById('groubtrans').value=str;
            // }  
            
                document.getElementById('groubtrans').value=unique;
            

            


            

            temparr = [];

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
        var transid=$(this).data('tid');
        // var verifylat=$(this).data('lat');
        // var verifylong=$(this).data('long');

        // window.location.href="edit/"+transid;
        // alert(transid);
        // callLocationID(transid);

          // for (var i = 0; i < locations.length; i++) { 
          //   if(verifylat==locations[i][0] && verifylong==locations[i][1]){

          //        arr.push(locations[i][2]);
          //   }
          // }


            arr.push(transid);
            var unique = arr.unique();
            document.getElementById('groubtrans').value=unique;

    });
    

     
    
    
}



function initMapgroup(center_lat,center_long,listLocation,mapID) {
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
    var grouptransaction = [];
     var arr   =[];
    
    for (var i = 0; i < locations.length; i++) { 
        var newmarker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][0], locations[i][1]),
            icon:'/images/map_marker.png',
            title: "'"+locations[i][2]+"'", 
            id:     locations[i][2],
        });
        newmarker.setMap(map);
        newmarker['infowindow'] = new google.maps.InfoWindow({
            content: '<div style="font-weight:bold;">Transaction ID: '+locations[i][2]+'</div>'
        });
        // infowindow.open(map, newmarker);
        
        // alert(locations[i][2]);
        // this['infowindow'].open(map, this);


        // google.maps.event.trigger(newmarker.map,"resize");

        // var infowindow1 = new google.maps.InfoWindow();
        // infowindow1.setContent('some content here');
        // infowindow.open(map, newmarker);

        // infowindow.open(map, newmarker);
         // alert('s');

         

        google.maps.event.addListener(newmarker, 'click', function() {
            this['infowindow'].open(map, this);

            
        });



        // alert('ok');
        // marker.push(newmarker);
    }
    
    

     
    
    
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
        loadCityList($("#SelectionState").val(),'<?php echo Input::get('city'); ?>');
        
        $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
            format: 'YYYY-MM-DD'
        }); 
       
        $('body').on('click', '#map-view-all', function(){ 
            
            
             getAllLatLong();
        });

        $('body').on('click', '#btnShowall', function(){ 
            $('#groubtrans').val('showall'); 
            $('#btnUpdate').addClass('disabled');
            
             getAllLatLong();
        });

        $('body').on('click', '#btnUpdate', function(){ 
            var groupname       = $('#hidgroup').val();
            var transactions    = $('#groubtrans').val();
            // alert(groupname);

                $.ajax({
                    method: "POST",
                    url: "/jlogistic/updategroup",
                    dataType:'json',
                    data: {
                        'groupname':groupname,
                        'transactions':transactions    
                    },
                    beforeSend: function(){
                        
                    },
                    success: function(data) {
                           // alert(data.response);
                           if(data.response==1){
                            // bootbox.alert("Group was successfully deleted", function(e){
                            //             parent.$.fn.colorbox.close();
                            //         });  
                            alert('Group was successfully Updated');

                            $('#btnUpdate').addClass('disabled');

                            // Callshowgroup();

                           }
                           else
                           {
                            // bootbox.alert("Please select group name", function(e){
                            //             parent.$.fn.colorbox.close();
                            //         });
                            alert('Please select group name');
                           }
                    }
                });


        });
        

        


        $('body').on('click', '.select-group', function(){  //
             var groupname = $(this).data( "groupname" );
             $('#hidgroup').val(groupname);

             $('.select-group').removeClass('btn-primary').addClass('btn btn-default');

             $(this).removeClass('btn-default').addClass('btn-primary');

             $('#btnUpdate').removeClass('disabled');
             
             // document.getElementById(groupname).style.color = "#ff0000";

             if(groupname=='showall')
             {
                getAllLatLong();
                $('#groubtrans').val(''); 
             }
             else{
                getTransaction(groupname);
                $.ajax({
                    method: "POST",
                    url: "/jlogistic/selectgroup",
                    dataType:'json',
                    data: {
                        'groupname':groupname
                    },
                    beforeSend: function(){
                        
                    },
                    success: function(data) {
                         // alert(data[0].latitude);
                         // alert('ss')
                         // fn();
                          var list="";
                          var listLocation = [];
                            // alert(data.length);

                              $.each(data, function (index, value) {

                                var address = ' '+value.address.street_1 +', '+value.address.street_2 +' '+ value.address.postcode +' '+ value.address.city +' '+ value.address.state +', '+ value.address.country ;
                    
                                    list = list + '<tr lass="row-list-location" ><td valign="top"><div class="info-box" ><span class="info-id">'
                                    +value.transaction_id+'</span><br><span class="info-address row-list-location" data-transid="'+value.ltid+'" data-tid="'+value.transaction_id+'" data-lat="'+value.latitude+'" data-long="'+value.longitude+'"><i class="fa fa-map-marker" style="color: #c16161;"></i>'
                                    +address+'</span></div></td></tr>'
                                    
                                    listLocation.push([value.latitude,value.longitude,value.transaction_id]);
                                });
                                //initMapgroup
                                initMap(listLocation[0][0],listLocation[0][1],listLocation,'googleMapContainerAll');
                               // ;

                              $("#table-list").html(list);
                              document.getElementById('btnPrint').disabled=false;

                    }
                });

             }

        });

        //btnDelete

        $('body').on('click', '#btnDelete', function(){ 
             // getAllLatLong();
             // document.getElementById('groubtrans').value='';
             var transactionid = $('#groubtrans').val().trim();

             var hidgroup = $('#hidgroup').val().trim();

             $('#btnUpdate').addClass('disabled');

             // alert(hidgroup);
             // alert(transactionid);
             if (transactionid=='' || transactionid=='showall')
             {
                // bootbox.alert("Please select group name", function(e){
                //                         parent.$.fn.colorbox.close();
                //                     });
                alert('Please select group name');
             }
             else 
             {
                $.ajax({
                    method: "POST",
                    url: "/jlogistic/deletegroup",
                    dataType:'json',
                    data: {
                        'transactionid':transactionid,
                        'hidgroup':hidgroup
                    },
                    beforeSend: function(){
                        
                    },
                    success: function(data) {
                           // alert(data.response);
                           if(data.response==1){
                            // bootbox.alert("Group was successfully deleted", function(e){
                            //             parent.$.fn.colorbox.close();
                            //         });  
                            alert('Group was successfully deleted');

                            $('#groubtrans').val(''); 

                            Callshowgroup();

                           }
                           else
                           {
                            // bootbox.alert("Please select group name", function(e){
                            //             parent.$.fn.colorbox.close();
                            //         });
                            alert('Please select group name');
                           }
                    }
                });
            }
        });



        $('body').on('click', '#btnReset', function(){ 
             
             
            $('#btnUpdate').addClass('disabled');
             $.ajax({
                    method: "POST",
                    url: "/jlogistic/resetgroup",
                    dataType:'json',
                    data: {

                    },
                    beforeSend: function(){
                        
                    },
                    success: function(data) {
                          // bootbox.alert("Group was successfully reset", function(e){
                          //               parent.$.fn.colorbox.close();
                          //           });  
                          alert('Group was successfully reset');

                          $("#table-list-group").html('');
                    }
                });
             getAllLatLong();
             document.getElementById('groubtrans').value='';
             document.getElementById('btnPrint').disabled=true;
        });

        
        $('body').on('click', '#btnShow', function(){
                
            Callshowgroup();
            $('#btnUpdate').addClass('disabled');
        }); 

        $('body').on('click', '#btnPrint', function(){
            var transactionIDs=$('#groubtrans').val().trim();  
            var listg='';
            $("#doModal").modal('show');  
              
            $.ajax({
                    method: "POST",
                    url: "/jlogistic/printdo",
                    dataType:'json',
                    data: {
                        'groubtrans':transactionIDs
                    },
                    beforeSend: function(){
                        
                    },
                    success: function(data) {
                         $.each(data, function (index, value) {
                                listg=listg+'<tr><td>Transaction No: '+value.transaction_id+'&nbsp; DO Number :'+value.do_no+'&nbsp;<a href=../'+value.file+' target="_blank"> Print </a> | ';
                                listg=listg+'&nbsp; <a href=edit/'+value.id+' target="_blank">Assign Driver</a> </td></tr>';
                        });

                          $("#table-list-do").html(listg);

                    }
                });
        }); 

        $('body').on('click', '#btnMake', function(){ 
             var transactionIDs=$('#groubtrans').val().trim();
             // document.getElementById('groubtrans').value='';
             $('#btnUpdate').addClass('disabled');

             if (transactionIDs=='' || transactionIDs=='showall')
             {
                // bootbox.alert("Please select transactions", function(e){
                //                         parent.$.fn.colorbox.close();
                //                     });
                alert('Please select transactions');
             }
             else 
             {

                $.ajax({
                    method: "POST",
                    url: "/jlogistic/makegroup",
                    dataType:'json',
                    data: {
                        'transactionIDs':transactionIDs
                        
                    },
                    beforeSend: function(){
                        
                    },
                    success: function(data) {
                        var listg = "";
                        var itnar=0;

                        if(data.recordtype==1)
                        {
                            $('#groubtrans').val('');  
                                // bootbox.alert("Successfully created group.", function(e){
                                //         parent.$.fn.colorbox.close();
                                //     });
                                alert('Successfully created group.');
                        }
                        else
                        {
                            $('#groubtrans').val('');  
                                // bootbox.alert("Sorry, Group already Exists", function(e){
                                //         parent.$.fn.colorbox.close();
                                //     });
                                alert('Sorry, Group already Exists');
                        }
                        
                        getAllLatLong();

                        listg='<button class="btn btn-default select-group"   data-groupname="showall" value="Show all" id="showall" name="showall" type="button" title="Show Ungrouped">Show Ungrouped</button><br><hr>';
                       if (data.count==1) {
                        listg=listg+'<button class="btn btn-default select-group"   data-groupname="'+data.groupname+'"  value="'+data.groupname+'" id="'+data.groupname+'" name="'+data.groupname+'" type="button" title="Group">'+data.groupname+'</button>';
                        // console.log(listg);  
                       } else {

                            $.each(data.groupname, function (index, value) {
                                itnar=itnar + 1; 
                                listg=listg+'<button class="btn btn-default select-group"  data-groupname="'+value+'" value="'+value+'" id="'+value+'" name="'+value+'" type="button" title="'+value+'">'+value+'</button>&nbsp;';
                                if(itnar==3)
                                {
                                   listg=listg+ '<br>';
                                   itnar=0; 
                                }
                        }); 
                       }

                        $("#table-list-group").html(listg);
                        document.getElementById('btnPrint').disabled=false;

                    }
                })
             }
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

                    

                    $.each(data, function (index, value) {
                        
                        var address = ' '+value.address.street_1 +', '+value.address.street_2 +' '+ value.address.postcode +' '+ value.address.city +' '+ value.address.state +', '+ value.address.country ;
                        
                        list = list + '<tr lass="row-list-location" data-transid="'+value.ltid+'" data-lat="'+value.latitude+'" data-long="'+value.longitude+'"><td valign="top"><div class="info-box" ><span class="info-id">'
                        +value.transaction_id+'</span><br><span class="info-address row-list-location" data-transid="'+value.ltid+'" data-tid="'+value.transaction_id+'" data-lat="'+value.latitude+'" data-long="'+value.longitude+'"><i class="fa fa-map-marker" style="color: #c16161;"></i>'
                        +address+'</span></div></td></tr>'
                        listLocation.push([value.latitude,value.longitude,value.transaction_id]);
                    });
                    
                    initMap(listLocation[0][0],listLocation[0][1],listLocation,'googleMapContainerAll');
                    $("#table-list").html(list);
                    $("div.load-map-all").removeClass('show');
                   }
                    Callshowgroup();
               }
            })
            
        }

        function getVerifyaddress(){

            die('verify');
        }

        
        function getTransaction(groupname){
            var transact   =[];
            $.ajax({
                method: "POST",
                url: "/jlogistic/getgrouptrans",
                dataType:'json',
                data: {
                    'groupname':groupname
                },
                beforeSend: function(){
                   
                },
                success: function(data) {
                    // alert(data.grouptransaction.length);
                    $.each(data.grouptransaction, function (index, value) {
                        transact.push(value.transaction_id);
                    });
                    $('#groubtrans').val(transact);
                }

            })


        }

       

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

        function Callshowgroup(){
            $.ajax({
                    method: "POST",
                    url: "/jlogistic/showgroup",
                    dataType:'json',
                    data: {

                    },
                    beforeSend: function(){
                        
                    },
                    success: function(data) {
                        var listg = "";
                        var itnar=0;

                        listg='<button class="btn btn-default select-group"   data-groupname="showall" value="Show all" id="showall" name="showall" type="button" title="Show Ungrouped">Show Ungrouped</button><br><hr>';

                        //onclick="Callttrigger('+data.groupname+')"
                       if (data.count==1) {

                        listg=listg+'<button class="btn btn-default select-group"   data-groupname="'+data.groupname+'" value="'+data.groupname+'" id="'+data.groupname+'" name="'+data.groupname+'" type="button" title="Group1">'+data.groupname+'</button>';
                        console.log(listg);  
                       } else {

                            $.each(data.groupname, function (index, value) {
                                itnar=itnar + 1; 
                                listg=listg+'<button  data-groupname="'+value+'" class="btn btn-default select-group" value="'+value+'" id="'+value+'" name="'+value+'" type="button" title="'+value+'">'+value+'</button>&nbsp;';
                                if(itnar==3)
                                {
                                   listg=listg+ '<br>';
                                   itnar=0; 
                                }
                        }); 
                       }

                        $("#table-list-group").html(listg);


                    }
                });

        }
        
        
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
</script>
@stop