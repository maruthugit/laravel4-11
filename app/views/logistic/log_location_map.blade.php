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

    select[multiple] {
        height: 100px;
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
                    <h3 class="panel-title"><i class="fa fa-search"></i> Location Map</h3>
                    <a href="{{asset('/')}}jlogistic/locationlist" class="btn btn-primary pull-right vcenter" role="button"><span class="glyphicon glyphicon-search"></span> Search Again</button> </a>

                    <!-- <span><button id="btnSearch" name="btnSearch" class="btn btn-primary pull-right vcenter ">View Batch Statistics</button>
                    </span> -->
                    
                </div>

                <div lass="panel-body">
                    <form method="POST" name="frmmap" id="frmmap">
                        <div class="row">
                            <div class="col-lg-4"  style="width: 100%; eight: 80%;">
                                <div class="form-group">
                                    <div>
								          <div class="divLoading load-map-all"> </div>
								          <div class="row" style="eight: 900%; width: 100%;">
								                <div class="col-md-9" style="height: 80%; width: 100%; ">
								                    <div id="googleMapContainerAll" style="width: 100%; height: 740px;"></div>
								                </div>
								                <!-- <div class="col-md-9" d="map-all-list-container" style="width: 100%; padding-left: 0px;overflow-y: auto;overflow-x: auto;height: 840px;" >
								                    <div id="table-list" style="width:100%;" >
								                        

								                    </div>
								                </div> -->
								                

								          </div>
								     

                                    </div>

                                </div>
                                <input type="hidden" name="hidData" id="hidData" value='<?php print_r($locations);?>'>

                                 <input id="hidstate[]" class="form-control" name="hidstate[]" type="hidden" value="<?php echo $inputparams['hidstate'] ?>" >
                                <input id="hidcity" class="form-control" name="hidcity" type="hidden" value="<?php echo $inputparams['hidcity'] ?>">
                                <input id="hidpostcode" class="form-control" name="hidpostcode" type="hidden" value="<?php echo $inputparams['hidpostcode'] ?>">
                                <input id="hidtransaction_from" class="form-control" name="hidtransaction_from" type="hidden" value="<?php echo $inputparams['hidtransaction_from'] ?>">
                                <input id="hidtransaction_to" class="form-control" name="hidtransaction_to" type="hidden" value="<?php echo $inputparams['hidtransaction_to'] ?>">

                                <input id="hidpending" class="form-control" name="hidpending" type="hidden" value="<?php echo $inputparams['hidpending'] ?>">
                                <input id="hidundelivered" class="form-control" name="hidundelivered" type="hidden"  value="<?php echo $inputparams['hidundelivered'] ?>">
                                <input id="hidreturned" class="form-control" name="hidreturned" type="hidden"  value="<?php echo $inputparams['hidreturned'] ?>">

                                <input id="hidpartialsent" class="form-control" name="hidpartialsent" type="hidden"  value="<?php echo $inputparams['hidpartialsent'] ?>">
                                <input id="hidsending" class="form-control" name="hidsending" type="hidden"  value="<?php echo $inputparams['hidsending'] ?>">
                                <input id="hidsent" class="form-control" name="hidsent" type="hidden"  value="<?php echo $inputparams['hidsent'] ?>">
                                <input id="hidcancelled" class="form-control" name="hidcancelled" type="hidden"  value="<?php echo $inputparams['hidcancelled'] ?>">
                            </div>
                         <!--    <pre>
                        <?php //print_r($inputparams)."newww..."; ?>
                        </pre> -->

                        </div>
                        <div class="row arelative">
                                <div class="form-group" style="padding-left:5px;">
                                    <!-- <input type="button" name="btnBatch" id="btnBatch" value="View Batch Statistics" class="btn btn-default">     -->
                                    <input type="button" name="btnPrint" id="btnPrint" value="Print" class="btn btn-default" disabled>
                                    <input type="button" name="btnReset" id="btnReset" value="Reset" class="btn btn-default">
                                    <input type="button" name="btnMake" id="btnMake" value="Make Group" class="btn btn-default">
                                    <!--<input type="button" name="btnShow" id="btnShow" value="Show Group" class="btn btn-default">-->
                                    <input type="button" name="btnDelete" id="btnDelete" value="Delete Group" class="btn btn-default">
                                    <input type="button" name="btnDriver" id="btnDriver" value="Assign Driver" class="btn btn-default">

                                    <input type="button" name="btnShowall" id="btnShowall" value="Show all" class="btn btn-default">

                                    <input type="button" name="btnUpdate" id="btnUpdate" value="Update Group" class="btn btn-default disabled">

                                   <!--  <a href="/routeplanner/index.html" target="_blank" rel="button" class="btn btn-default">Route Planner</a> -->

                                    <input type="button" name="btnRoute" id="btnRoute" value="Route Planner" formtarget="_blank" class="btn btn-default disabled">

                                    <button type="button" class="btn btn-default select-group"   data-groupname="showall" value="Show all" id="showall" name="showall" title="Show Ungrouped">Show Ungrouped</button>
                                </div>
                        </div>
                        <div class="row border-between arelative">
                            <div class="col-sm-6" >
                                <div class="form-group">
                                    <label>Group Transaction </label> 
                                    <div >
                                       <textarea id="groubtrans" class="form-control" style="height: 100px;" name="groubtrans" > </textarea>
                                       <input type="hidden" name="hidgroup" id="hidgroup">
                                       <input type="hidden" name="hidgroupid" id="hidgroupid">

                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                    <div class="form-group">
                                    <label>Group List </label> 
                                       <div id="table-list-group" style="width:100%; pos" >
                                        </div> 
                                    </div>
                            </div>

                        </div>

                        <div class="modal fade" id="driverModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                          <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Assign Drivers</h4>
                                  </div>
                                <div class="modal-body" style="padding: 0px;">
                                    <div class="col-md-12 col-xs-12" style="padding-top: 14px;">

                                       

                                            <div class="row">   
                                                <div class="col-md-6">
                                                    <h5 class="modal-title" id="myModalLabel">Available Groups</h5>  
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">



                                                            <!-- Note the missing multiple attribute! -->
                                                            
                                                            <select ame ="availablegroup[]" class="form-control selectpicker" id="GroupAvailable" multiple required> 
                                                               
                                                            </select>




                                                            <table width="100%" id="table-list-Groups" >
                                                                
                                                            </table>

                                                        </div>
                                                           
                                                      </div>
                                                        
                                                  
                                                </div>
                                                <div class="col-md-6">
                                                    <h5 class="modal-title" id="myModalLabel2">Available Drivers</h5>  
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">

                                                            <table width="100%" id="table-list-driver" >
                                                                <select  class="form-control selectpicker" id="availableDrivers" required> 
                                                                </select>
                                                            </table>

                                                        </div>
                                                           
                                                      </div>
                                                </div>
                                            </div>
                                            <div class="row"> 
                                                 <div class="col-md-12">
                                                         <button type="button" id="btnAssigndriver" class="btn btn-primary center-block">Assign Driver</button>   
                                                 </div>

                                            </div>
                                            <div class="row"> 
                                                <div class="col-md-12">
                                                    <h5 class="modal-title" id="myModalLabel2">Manage Drivers</h5>  
                                                    <table class="table table-bordered table-striped" id="table-list-group-driver">

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
                        
                        <div class="modal fade" id="BatchModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                          <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Daily Batch Statistics <!-- (<?php //echo date('Y-m-d');?>) --></h4>
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

                        <!-- DO Modal  -->
                        <div class="modal fade" id="doModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                          <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Print DO </h4>
                                  </div>
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

                       
                    </form>
                </div>
            </div>

        </div>
    </div>

@stop

@section('inputjs')

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDcTN4TPOfZRUmCF_7S_4w3sFlxaGEr3f4" async defer></script>
<!-- Latest compiled and minified JavaScript -->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">
<!-- {{ HTML::script('css/bootstrap-select.min.css') }} -->
{{ HTML::script('js/bootstrap-select.min.js') }}



<!-- (Optional) Latest compiled and minified JavaScript translation files -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/i18n/defaults-*.min.js"></script>
 -->

<script>

    function initMap(center_lat,center_long,listLocation,mapID) {
  // Create an array of styles.

  // alert(center_lat+center_long+mapID);
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
    var styledMap = new google.maps.StyledMapType(styles,{name: "Simple Map"});
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
    // console.log(locations[1].length);
    for (var i = 0; i < locations.length; i++) { 
        // alert(locations[i][0]);
        // alert(locations[i][1]);
        // alert(locations[i][2]);
    //   console.log(locations[i][2]);
    //   console.log('break');
        var newmarker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][0], locations[i][1]),
            icon:'/images/map_marker.png',
            title: "'"+locations[i][3]+"'", 
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


        //DatePicker Start
        
        $(".date-range").click(function(){
            $("#selected-date-range").html($(this).html());
            $('#type-select').val($(this).attr("data-type"))
            
            if($('#type-select').val() == 1){
                alert('DAIly');
                var type = $('#type-select').val();
                var startDate = $('#start-today').val();
                var toDate = $('#start-today').val();
                getStatistic(0,type,startDate,toDate);
                
            }
            
            if($('#type-select').val() == 2){
                alert('WEEK');
                var type = $('#type-select').val();
                var startDate = $('#start-weekly-date').val();
                var toDate = $('#end-weekly-date').val();
                getStatistic(0,type,startDate,toDate);
            }
            
            if($('#type-select').val() == 3){
                alert('Month');
                var type = $('#type-select').val();
                var startDate = $('#start-month-date').val();
                var toDate = $('#end-month-date').val();
                getStatistic(0,type,startDate,toDate);

                
            }
            
        });
        
        $("#nav-left").click(function(){
            alert('LEFT');
            console.log('LEFT');
            var type = $('#type-select').val();
            var startDate = $('#start-date-select').val();
            var toDate = $('#to-date-select').val();
            getStatistic(1,type,startDate,toDate);

        });
        
        $("#nav-right").click(function(){
            alert('RIGHT');
            console.log('RIGHT');
            console.log('LEFT');
            var type = $('#type-select').val();
            var startDate = $('#start-date-select').val();
            var toDate = $('#to-date-select').val();
            getStatistic(2,type,startDate,toDate);
            
        });

        function getStatistic(navigation,type,startDate,toDate){
            alert(startDate+'Arg'+toDate);
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
                alert(typename);
//                console.log($("#total-logistic-sending").html(data.data.TotalStatistic.TransactionLogistic.Sending));
                
               

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
                 
                 if(typename ==1){
                    alert(data.data.DateSelection.displayStartDate + data.data.DateSelection.displayEndDate);
                    fnBatchlist(data.data.DateSelection.displayStartDate,data.data.DateSelection.displayEndDate);   
                 }   
                 else if(typename ==2){
                    alert(data.data.DateSelection.WeeklyStartDate + data.data.DateSelection.WeeklyEndDate);    
                    fnBatchlist(data.data.DateSelection.WeeklyStartDate,data.data.DateSelection.WeeklyEndDate);   
                 }
                 else if(typename ==3){
                    alert(data.data.DateSelection.MonthStartDate + data.data.DateSelection.MonthEndDate);
                    fnBatchlist(data.data.DateSelection.MonthStartDate,data.data.DateSelection.MonthEndDate);  
                 }


                  
                
                /*
                 *   <input type="text" id="start-weekly-date" value="">
                    <input type="text" id="end-weekly-date" value="">
                    <input type="text" id="start-month-date" value="">
                    <input type="text" id="end-month-date" value="">
                 */
                
            },
            error: function (error) {
                location.reload();
            }
        })
    }    


        //DatePicker End

       
        $("div.load-map-all").addClass('show');
        

        loadMap();
        getAllLatLong();

        // console.log('ok');

        function loadMap(){
            var list="";
            var listLocation = [];
            // var data = [];
             // data = $('#hidData').val();
             
             data = JSON.stringify(<?php print_r($locations);?>);
             // alert(data);
             // console.log(data);
            // var data = <?php// print_r($locations);?>;
            // var obj = JSON.parse(data);
           // var b = JSON.parse(JSON.stringify(data));
            // console.log(data.locations.length);
            // alert(b.length);
            var d = JSON.parse(data)
            // alert(b.transaction_id.length);
            $.each(d, function (index, value) {
                     
                     // alert(value);

                        var address = ' '+value.address.street_1 +', '+value.address.street_2 +' '+ value.address.postcode +' '+ value.address.city +' '+ value.address.state +', '+ value.address.country ;
                        // console.log(address);
                        list = list + '<tr lass="row-list-location" data-transid="'+value.ltid+'" data-lat="'+value.latitude+'" data-long="'+value.longitude+'"><td valign="top"><div class="info-box" ><span class="info-id">'
                        +value.transaction_id+'</span><br><span class="info-address row-list-location" data-transid="'+value.ltid+'" data-tid="'+value.transaction_id+'" data-lat="'+value.latitude+'" data-long="'+value.longitude+'"><i class="fa fa-map-marker" style="color: #c16161;"></i>'
                        +address+'</span></div></td></tr>';
                        listLocation.push([value.latitude,value.longitude,value.transaction_id,address]);
                    });

                   console.log(listLocation.length);
                   // console.log(listLocation[0][0]);
                    // alert(listLocation[0][0]);


                    // initMap(listLocation[0][0],listLocation[0][1],listLocation,'googleMapContainerAll');

                    // $("div.load-map-all").removeClass('show');

                    // Callshowgroup();

        }


        function getAllLatLong(){

            var state = $("#hidstate").val();
            var city = $("#hidcity").val();
            var postcode = $("#hidpostcode").val();
            var transaction_from = $("#hidtransaction_from").val();
            var transaction_to = $("#hidtransaction_to").val();
            var listLocation = [];
            var showall = $('#groubtrans').val();
            var pending         = $('#hidpending').val();
            var undelivered     = $('#hidundelivered').val();
            var returned        = $('#hidreturned').val();
            var partialsent     = 0;
            var sending         = 0;
            var sent            = 0;
            var cancelled       = 0;

            
            if($("input[name='hidpartialsent']").prop("checked")){
                    partialsent=2;
                }
            if($("input[name='hidsending']").prop("checked")){
                    sending=4;
                }
            if($("input[name='hidsent']").prop("checked")){
                    sent=5;
                }
            if($("input[name='hidcancelled']").prop("checked")){
                    cancelled=6;
                }

            
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
                    //console.log(data);
                    if(data.length==0)
                    {
                       
                       // alert('No transactions found on the map. Please Search Again');
                        bootbox.alert("No transactions found on the map. Please Search Again", function(e){
                                        window.open('{{asset('/')}}jlogistic/locationlist', '_self');    
                                        // parent.$.fn.colorbox.close();
                                    });
                        // $('#groubtrans').focus();
                        
                    }
                    else 
                    {

                    

                    $.each(data, function (index, value) {
                        //  console.log(value.transaction_id);
                        var address = ' '+value.address.street_1 +', '+value.address.street_2 +' '+ value.address.postcode +' '+ value.address.city +' '+ value.address.state +', '+ value.address.country ;
                        
                        list = list + '<tr lass="row-list-location" data-transid="'+value.ltid+'" data-lat="'+value.latitude+'" data-long="'+value.longitude+'"><td valign="top"><div class="info-box" ><span class="info-id">'
                        +value.transaction_id+'</span><br><span class="info-address row-list-location" data-transid="'+value.ltid+'" data-tid="'+value.transaction_id+'" data-lat="'+value.latitude+'" data-long="'+value.longitude+'"><i class="fa fa-map-marker" style="color: #c16161;"></i>'
                        +address+'</span></div></td></tr>'
                        listLocation.push([value.latitude,value.longitude,value.transaction_id]);
                    });
                    
                    initMap(listLocation[0][0],listLocation[0][1],listLocation,'googleMapContainerAll');
                    $("div.load-map-all").removeClass('show');
                   }
                    Callshowgroup();
               }
            })
            
        }

        $('body').on('click','#btnRoute', function(){
            var transactionIDs=$('#groubtrans').val().trim();
            var link = "/jlogistic/routeplanner";

            $('#frmmap').attr('action','/jlogistic/routeplanner');
            $('#frmmap').submit();

        });

        $('body').on('click','#btnAssigndriver', function(){
            var groupvar   = $('#GroupAvailable').val();
            var drivervar  = $('#availableDrivers').val(); 
            var drivername = '';

            if(groupvar=='' || groupvar ==null || drivervar =='' || drivervar ==null)
            {
                bootbox.alert("Please select Available Groups/Driver", function(e){
                                        parent.$.fn.colorbox.close();
                                    }); 
            }
            else{
                $.ajax({
                        method: "POST",
                        url: "/jlogistic/driver",
                        data: {
                            'driverid':drivervar
                        },
                        beforeSend: function(){
                            // alert('new');
                        },
                        success: function(datadriver) {
                            
                            // console.log('In');

                                bootbox.confirm({
                                    title: "Assign Driver",
                                    message: "Are you sure you want to assign this Groups to  "+datadriver+"?",
                                    buttons: {
                                            cancel: {
                                                label: '<i class="fa fa-times"></i> Cancel'
                                            },
                                            confirm: {
                                                label: '<i class="fa fa-check"></i> Confirm'
                                            }
                                        },
                                    callback: function(result) {
                                        if (result === true) {
                                               $.ajax({
                                                method: "POST",
                                                url: "/jlogistic/assigndriver",
                                                dataType:'json',
                                                data: {
                                                    'groupids':groupvar,
                                                    'driverid':drivervar 
                                                },
                                                beforeSend: function(){ 
                                                    
                                                },
                                                success: function(data) {
                                                      bootbox.alert("Group was successfully Assigned to "+datadriver, function(e){
                                                                    parent.$.fn.colorbox.close();
                                                                });  
                                                      // alert('Group was successfully reset');

                                                      // $("#table-list-group").html('');

                                                      Callassigndriver(); 
                                                }

                                            });


                                                

                                        } else {
                                            console.log("IGNORE");
                                        }
                                    } 
                                }); 

                        }
                });                    

             

            }

                
                



        });

        
        $('body').on('click', '.select-group-driver', function(){

            var groupname = $(this).data("groupname");
            var groupid = $(this).data("groupid");
                
                 bootbox.confirm({
                    title: "Delete Grouping",
                    message: "Are you sure you want to delete this "+groupname+" ?",
                    buttons: {
                            cancel: {
                                label: '<i class="fa fa-times"></i> Cancel'
                            },
                            confirm: {
                                label: '<i class="fa fa-check"></i> Confirm'
                            }
                        },
                    callback: function(result) {
                        if (result === true) {
                           //Start Reset Group
                           // $('#btnUpdate').addClass('disabled');
                             $.ajax({
                                    method: "POST",
                                    url: "/jlogistic/deletgroup",
                                    dataType:'json',
                                    data: {
                                        'groupname':groupname,
                                        'groupid':groupid
                                    },
                                    beforeSend: function(){ 
                                        
                                    },
                                    success: function(data) {

                                          bootbox.alert("Group was successfully deleted", function(e){
                                                        parent.$.fn.colorbox.close();
                                                    });  
                                          
                                    }
                                });

                                Callassigndriver();
                             
                             //End Reset Group

                        } else {
                            console.log("IGNORE");
                        }
                    } 
                }); 


        });


        $('body').on('click', '.select-group-print', function(){

            var groupname = $(this).data("groupprint");
            var listg='';

            $('#btnUpdate').addClass('disabled');

                 $.ajax({
                        method: "POST",
                        url: "/jlogistic/getgrouptransactions",
                        dataType:'json',
                        data: {
                            'groupname':groupname
                        },
                        beforeSend: function(){ 
                            
                        },
                        success: function(data1) {
                              
                            $('#driverModal').modal("hide");
                            $("#doModal").modal('show');  
                            // alert(data1);
                            $('#groubtrans').val(data1);
                            var transactionIDs = $('#groubtrans').val().trim();
                            document.getElementById('btnPrint').disabled=false;
                            // var transactionIDs = data1;
              
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
                                           
                                               listg=listg+'<tr><td>Transaction No: '+value.transaction_id+'&nbsp; DO Number :'+value.do_no+'&nbsp;<a href={{asset('/')}}transaction/files/'+value.filenew+' target="_blank"> Print </a> </td></tr>';
                                                 // listg=listg+'&nbsp; <a href=edit/'+value.id+' target="_blank">Assign Driver</a> </td></tr>';
                                        });

                                          $("#table-list-do").html(listg);

                                    }
                                });

                        }
                    });
            

        });


        $('body').on('click','#btnBatch', function(){
            
            var startDate = $('#start-today').val();
            var toDate = $('#start-today').val();
            getStatistic(0,1,startDate,toDate);


            fnBatchlist(startDate,toDate);

        })

        function fnBatchlist(startDate,toDate){
            var listg='';
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

                        // alert(data.driver);

                        listg='<tr><td>ID</td><td>Driver Name</td><td>Total Batch Assign</td></tr>'; 

                         $.each(data, function (index, value) {
                           
                           // alert(value.id);
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


        $('body').on('click','#btnDriver', function(){

            Callassigndriver()


        })

        $('body').on('click', '#btnReset', function(){ 
             
            bootbox.confirm({
                    title: "Reset Grouping",
                    message: "Are you sure you want to reset all groups ?",
                    buttons: {
                            cancel: {
                                label: '<i class="fa fa-times"></i> Cancel'
                            },
                            confirm: {
                                label: '<i class="fa fa-check"></i> Confirm'
                            }
                        },
                    callback: function(result) {
                        if (result === true) {
                           //Start Reset Group
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
                                          bootbox.alert("Group was successfully reset", function(e){
                                                        parent.$.fn.colorbox.close();
                                                    });  
                                          // alert('Group was successfully reset');

                                          $("#table-list-group").html('');
                                    }
                                });
                             getAllLatLong();
                             document.getElementById('groubtrans').value='';
                             document.getElementById('btnPrint').disabled=true;
                             //End Reset Group

                        } else {
                            console.log("IGNORE");
                        }
                    } 
                }); 

             
            
        });

        function Callassigndriver(){

            $('#driverModal').modal("show");

            $.ajax({
                    method: "POST",
                    url: "/jlogistic/grouplist",
                    dataType:'json',
                    data: {

                    },
                    beforeSend: function(){
                        
                    },
                    success: function(data) {
                         var listg='';
                         var listg1='';
                         var listg2='';

                         console.log(data.pendinggroup.length)
                         listg='<tr><td>ID</td><td>Group Name</td><td>Driver Name</td><td>Action</td></tr>'; 

                         $.each(data.group, function (index, value) {
                                listg=listg+'<tr><td>'+value.id+'</td><td>'+value.groupname+'</td><td>'+value.drivername+'</td><td> ';
                                
                                listg=listg+'<button class="btn-link select-group-print" data-id="'+value.groupname+'"  data-groupprint="'+value.groupname+'" value="'+value.id;
                                listg=listg+'" id="'+value.groupname+'" name="'+value.groupname+'" type="button" title="Print DO">Print DO</button>  ';

                                // listg=listg+'<button class="btn-link select-group-driver" data-groupname="'+value.groupname+'" data-groupid="'+value.id;
                                // listg=listg+'" id="'+value.groupname+'" name="'+value.groupname+'" type="button" title="Remove Driver">Remove Driver</button> </td></tr>';


                        // listg=listg+'<button class="btn btn-default select-group"   data-groupname="'+value.groupname+'" value="'+value.id+'" id="'+value.groupname+'" name="'+value.groupname+'" type="button" title="Remove Driver">Remove Driver</button>'

                         }); 

                         if(data.group.length ==0){
                            listg=listg+'<tr><td colspan=4 align=center>No Record Found</td></tr>'; 

                         }

                         
                         $.each(data.pendinggroup, function (index, value) {
                                listg1=listg1+'<option value='+value.id+'>'+value.groupname+'</option> ';
                                // $("#GroupAvailable").append('<option value="'+value.id+'">'+value.groupname+'</option>');
                         }); 
                         
                          $("#GroupAvailable").html(listg1);
                          $("#GroupAvailable").selectpicker("refresh");


                         listg2="<option value=''>Please Select</option> ";
                         $.each(data.driver, function (index, valudriver) {
                                listg2=listg2+'<option value='+valudriver.id+'>'+valudriver.drivername+'</option> ';
                                // $("#availableDrivers").append('<option value="'+valudriver.id+'">'+valudriver.drivername+'</option>');
                         }); 
                          $("#availableDrivers").html(listg2); 
                          $("#availableDrivers").selectpicker("refresh");
                           

                          $("#table-list-group-driver").html(listg);
                          // $("#GroupAvailable").html(listg1);
                          // $("#availableDrivers").html(listg2);  

                    }
                });

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

                        // listg='<button class="btn btn-default select-group"   data-groupname="showall" value="Show all" id="showall" name="showall" type="button" title="Show Ungrouped">Show Ungrouped</button><br><hr>';

                        //onclick="Callttrigger('+data.groupname+')"
                       if (data.count==1) {

                        listg=listg+'<button class="btn btn-default select-group" data-id="'+data.groupid+'"  data-groupname="'+data.groupname+'" value="'+data.groupname+'" id="'+data.groupname+'" name="'+data.groupname+'" type="button" title="Group1">'+data.groupname+'</button>';
                        // console.log(listg);  
                       } else {
                            var i=0;
                            // alert(data.count);


                            for(i=0;i<data.count;i++){

                                // alert(data.groupname[i]);
                                itnar=itnar + 1; 
                                listg=listg+'<button  data-groupname="'+data.groupname[i]+'" data-id="'+data.groupid[i]+'"  class="btn btn-default select-group" value="'+data.groupname[i]+'" id="'+data.groupname[i]+'" name="'+data.groupname[i]+'" type="button" title="'+data.groupname[i]+'">'+data.groupname[i]+'</button>&nbsp;';

                       }


                        //     $.each(data.groupname, function (index, value) {
                                
                        //         itnar=itnar + 1; 
                        //         listg=listg+'<button  data-groupname="'+value.groupname+'" data-id="'+value.groupid+'"  class="btn btn-default select-group" value="'+value.groupname+'" id="'+value.groupname+'" name="'+value.groupname+'" type="button" title="'+value.groupname+'">'+value.groupname+'</button>&nbsp;';
                               
                        // }); 
                       }

                        $("#table-list-group").html(listg);
                        $('#btnRoute').addClass('disabled');    

                    }
                });

        }


        $('body').on('click', '.select-group', function(){  //
            var groupid = $(this).data( "id" );
             var groupname = $(this).data( "groupname" );
             $('#hidgroup').val(groupname);
             $('#hidgroupid').val(groupid);

             $('.select-group').removeClass('btn-primary').addClass('btn btn-default');

             $(this).removeClass('btn-default').addClass('btn-primary');

             $('#btnUpdate').removeClass('disabled');
             $('#btnRoute').removeClass('disabled');
             
             // document.getElementById(groupname).style.color = "#ff0000";

             if(groupname=='showall')
             {
                getAllLatLong();
                $('#groubtrans').val(''); 
             }
             else{
                getTransaction(groupname,groupid);
                $.ajax({
                    method: "POST",
                    url: "/jlogistic/selectgroup",
                    dataType:'json',
                    data: {
                        'groupname':groupname,
                        'groupid':groupid
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
                                    
                                    listLocation.push([value.latitude,value.longitude,value.transaction_id,address]);
                                });
                                //initMapgroup
                                initMap(listLocation[0][0],listLocation[0][1],listLocation,'googleMapContainerAll');
                               // ;

                              document.getElementById('btnPrint').disabled=false;

                    }
                });

             }

        });

        function getTransaction(groupname,groupid){
            var transact   =[];
            $.ajax({
                method: "POST",
                url: "/jlogistic/getgrouptrans",
                dataType:'json',
                data: {
                    'groupname':groupname,
                    'groupid':groupid
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


        $('body').on('click', '#btnShowall', function(){ 
            $('#groubtrans').val('showall'); 
            $('#btnUpdate').addClass('disabled');
            $('#btnRoute').addClass('disabled');
            


             getAllLatLong();
        });


        $('body').on('click', '#btnMake', function(){ 
             var transactionIDs=$('#groubtrans').val().trim();
             // document.getElementById('groubtrans').value='';
             $('#btnUpdate').addClass('disabled');

             if (transactionIDs=='' || transactionIDs=='showall')
             {
                bootbox.alert("Please select transactions", function(e){
                                        parent.$.fn.colorbox.close();
                                    });
                // alert('Please select transactions');
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
                                bootbox.alert("Successfully created group.", function(e){
                                        parent.$.fn.colorbox.close();
                                    });
                                // alert('Successfully created group.');
                        }
                        else
                        {
                            $('#groubtrans').val('');  
                                bootbox.alert("Sorry, Group already Exists", function(e){
                                        parent.$.fn.colorbox.close();
                                    });
                                // alert('Sorry, Group already Exists');
                        }
                        // alert(<?php //print_r($locations);?>);
                        // var b = JSON.stringify(<?php //print_r($locations);?>);
                        //    alert(b);

                        // $('#hidData').val(JSON.stringify(<?php //print_r($locations);?>));
                        getAllLatLong();

                        listg='<button class="btn btn-default select-group"   data-groupname="showall" value="Show all" id="showall" name="showall" type="button" title="Show Ungrouped">Show Ungrouped</button><br><hr>';
                       if (data.count==1) {
                        listg=listg+'<button class="btn btn-default select-group"   data-groupname="'+data.groupname+'"  value="'+data.groupname+'" id="'+data.groupname+'" name="'+data.groupname+'" type="button" title="Group">'+data.groupname+'</button>';
                        // console.log(listg);  
                       } else {

                            $.each(data.groupname, function (index, value) {
                                itnar=itnar + 1; 
                                listg=listg+'<button class="btn btn-default select-group"  data-groupname="'+value+'" value="'+value+'" id="'+value+'" name="'+value+'" type="button" title="'+value+'">'+value+'</button>&nbsp;';
                                // if(itnar==13)
                                // {
                                //    listg=listg+ '<br>';
                                //    itnar=0; 
                                // }
                        }); 
                       }

                       

                        $("#table-list-group").html(listg);
                        document.getElementById('btnPrint').disabled=false;
                        
                        
                    }
                })
             }
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
                              
                                listg=listg+'<tr><td>Transaction No: '+value.transaction_id+'&nbsp; DO Number :'+value.do_no+'&nbsp;<a href={{asset('/')}}transaction/files/'+value.filenew+' target="_blank"> Print </a> </td></tr>';
                               // listg=listg+'&nbsp; <a href=edit/'+value.id+' target="_blank">Assign Driver</a> </td></tr>';
                        });

                          $("#table-list-do").html(listg);

                    }
                });
        }); 

        //Update

        $('body').on('click', '#btnUpdate', function(){ 
            var groupname       = $('#hidgroup').val();
            var hidgroupid = $('#hidgroupid').val();
            
            var transactions    = $('#groubtrans').val();
            // alert(groupname);

                $.ajax({
                    method: "POST",
                    url: "/jlogistic/updategroup",
                    dataType:'json',
                    data: {
                        'groupname':groupname,
                        'transactions':transactions,
                        'hidgroupid':hidgroupid    
                    },
                    beforeSend: function(){
                        
                    },
                    success: function(data) {
                           // alert(data.response);
                           if(data.response==1){
                            bootbox.alert("Group was successfully deleted", function(e){
                                        parent.$.fn.colorbox.close();
                                    });  
                            // alert('Group was successfully Updated');

                            $('#btnUpdate').addClass('disabled');

                            // Callshowgroup();

                           }
                           else
                           {
                            bootbox.alert("Please select group name", function(e){
                                        parent.$.fn.colorbox.close();
                                    });
                            // alert('Please select group name');
                           }
                    }
                });


        });



        //btnDelete

        $('body').on('click', '#btnDelete', function(){ 
             // getAllLatLong();
             // document.getElementById('groubtrans').value='';
             var transactionid = $('#groubtrans').val().trim();

             var hidgroup = $('#hidgroup').val().trim();
             var hidgroupid = $('#hidgroupid').val().trim();

             $('#btnUpdate').addClass('disabled');
             $('#btnRoute').addClass('disabled');

             // alert(hidgroup);
             // alert(transactionid);
             if (transactionid=='' || transactionid=='showall')
                 {
                    bootbox.alert("Please select group name", function(e){
                                            parent.$.fn.colorbox.close();
                                        });
                    // alert('Please select group name');
                 }
                 else 
                 {
                 bootbox.confirm({
                        title: "Delete Grouping",
                        message: "Are you sure you want to delete this group ?",
                        buttons: {
                                cancel: {
                                    label: '<i class="fa fa-times"></i> Cancel'
                                },
                                confirm: {
                                    label: '<i class="fa fa-check"></i> Confirm'
                                }
                            },
                        callback: function(result) {
                            if (result === true) {

                                     
                                        $.ajax({
                                            method: "POST",
                                            url: "/jlogistic/deletegroup",
                                            dataType:'json',
                                            data: {
                                                'transactionid':transactionid,
                                                'hidgroup':hidgroup,
                                                'hidgroupid':hidgroupid
                                            },
                                            beforeSend: function(){
                                                
                                            },
                                            success: function(data) {
                                                   // alert(data.response);
                                                   if(data.response==1){
                                                    bootbox.alert("Group was successfully deleted", function(e){
                                                                parent.$.fn.colorbox.close();
                                                            });  
                                                    // alert('Group was successfully deleted');

                                                    $('#groubtrans').val(''); 

                                                    Callshowgroup();

                                                   }
                                                   else
                                                   {
                                                    bootbox.alert("Please select group name", function(e){
                                                                parent.$.fn.colorbox.close();
                                                            });
                                                    // alert('Please select group name');
                                                   }
                                            }
                                        });
                                    


                            } else {
                                console.log("IGNORE");
                            }
                        } 
                    }); 
                }
        });




    });


    


 </script>
@stop