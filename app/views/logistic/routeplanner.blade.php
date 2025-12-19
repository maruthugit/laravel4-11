@extends('layouts.master')
@section('title', 'Logistic Transaction')



@section('content')



      


    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
<div id="page-wrapper">
<div lass="container">

<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Advance Route Planner 
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}jlogistic/locationlist"><span class="glyphicon glyphicon-backward"></span></a>
                    <!-- @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 5, 'AND'))
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}transaction/add"><i class="fa fa-plus"></i></a>
                    @endif -->
                </span>
            </h1>
        </div>
    </div>

<div>
    
<div class="row" >
        
<div class="col-lg-12">
        
    <div id="message"></div>
    
<div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title">Search</h3>
    </div>


    <div class="panel-body">
        <form class="form-inline" role="form" id="multiple-destination">
          <div class="form-group">
              <label class="sr-only" for="start">Starting Location :</label>
              <input type="text" class="form-control" id="start" placeholder="Enter Starting Location">
          </div>
            
          <div class="form-group">
              <button type="button" class="btn btn-success" onClick="_ZNRPL_Swap();" style='margin-top:5px;'><span class="glyphicon glyphicon-transfer" aria-hidden="true"></span></button>
          </div>
          
          <div class="form-group">
              <label class="sr-only" for="end">Destination Location :</label>
              <input type="text" class="form-control" id="end" placeholder="Enter Destination Location">
          </div>
          
          <div class="form-group">
              <select class="form-control" id="mode" name="mode">
                  <option value="DRIVING">Driving</option>
                  <option value="WALKING">Walking</option>
                  <option value="BICYCLING">Bicycling</option>
                  <option value="TRANSIT">Transit</option>
              </select>
          </div>
            
            <div class="form-group">
          <select class="form-control" id="distance_unit" name="distance_unit">
          <option value="KM">KM</option>
              <option value="Miles">Miles</option>
        </select> 
          </div>
          
          <button type="button" class="btn btn-success" onClick="_ZNRPL_Add_Element();"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
            
        </form>
     <!--  <script type="text/javascript">
        
        var add1 = "7, Jalan Kerinchi, Pantai Dalam, 59200 Kuala Lumpur, Wilayah Persekutuan Kuala Lumpur, Malaysia";
        var add2 = "Bukit Bintang Kuala Lumpur Federal Territory of Kuala Lumpur Malaysia";

        function calcRoute1()
        {
          calcRoute(add1,add2);
        } 


      </script> -->
          <input type="hidden" name="hidData" id="hidData" value='<?php print($gtransactions);?>'>
          <button type="button" class="btn btn-success" id="btnRoute" name="btnRoute" nClick="calcRoute();" style='margin-top:5px;'>Get Shortest Route</button>
          <button type="button" class="btn btn-primary" id="TrafficToggle" style='margin-top:5px;'>Show/Hide Traffic</button>

    </div>

</div>
 
        
<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">Total Fuel Cost: </h3>
  </div>
    <div class="panel-body">

         <form class="form-inline" role="form" id="fuel-calculator">
             
             <div class="form-group">
                <select class="form-control" id="fuel_type" name="fuel_type">
                    <option value="Diesel">Diesel</option>
                    <option value="Gasoline">Gasoline</option>
                    <option value="Liquefied Petroleum">Liquefied Petroleum</option>
                    <option value="Compressed Natural Gas">Compressed Natural Gas</option>
                    <option value="Ethanol">Ethanol</option>
                    <option value="Bio-diesel">Bio-diesel</option>
                </select>
              </div>
              
              <div class="form-group">
                <label class="sr-only" for="start">Fuel Per Liter Cost :</label>
                <input type="text" class="form-control" id="fuel_rate" placeholder="Enter Fuel Per Liter Cost">
              </div>
              
              <div class="form-group">
                <label class="sr-only" for="end">Per Liter Car Mileage :</label>
                <input type="text" class="form-control" id="mileage" placeholder="Enter Per Liter Car Mileage">
              </div>
              <button type="button" class="btn btn-success" onClick="_ZNRPL_Fuel_Calculator();"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Fuel Cost to Trip Cost</button>
                
        </form> 

      </div>
</div>
    
    
<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="text-left" style="float:left;padding-top:10px;"><h3 class="panel-title">Google Map</h3></div>
          <div class="text-right">
            <a href="#" class="btn btn-success text-right" onClick="window.print()"><i class="glyphicon glyphicon-print"></i></a><span id="share"></span>
          </div>
    </div>
    <div class="panel-body map_padding">
       <!--  <input id="pac-input" class="controls" type="text" placeholder="Search Nearby Places"> -->
          <div id="map-canvas" style="height:600px; width:100%;" ></div>
    </div>
</div>
        
    
<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">Total Trip: </h3>
  </div>
  <div class="panel-body">
    <div id="trip_cost"></div>
    <div id="fuel_cost"></div>
  </div>
</div>
        
    
<div class="panel panel-primary">
  <div class="panel-heading">
     <h3 class="panel-title">Total Distance: <span id="total"></span>
     &nbsp;&nbsp;Total Duration: <span id="duration"></span></h3>
  </div>
  <div class="panel-body">
    <div id="directionsPanel"></div>
  </div>
</div>
    
  </div>
    
</div>
    
    </div> <!-- /container -->
</div>


</div>
@stop

{{ HTML::style('routeplanner/css/bootstrap.min.css') }}
{{ HTML::style('routeplanner/css/bootstrap-theme.min.css') }}
{{ HTML::style('routeplanner/css/style.css') }}
{{ HTML::style('routeplanner/css/accordion.css') }}


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
{{ HTML::script('routeplanner/js/accordion.js') }}
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDcTN4TPOfZRUmCF_7S_4w3sFlxaGEr3f4&libraries=places"></script>
{{ HTML::script('routeplanner/js/routeplannerapi.js') }}


@section('inputjs')

<script>

    $(document).ready(function() {


        $('#map-canvas').addClass('scrolloff');

        var startloc =  "36A, Jalan Ipoh Batu 8 1/2, Kompleks Selayang, 68100 Batu Caves, Selangor Darul Ehsan.";
        var endloc   =  "";

          

          fnTransaction();
          // calcRoute();

        $('body').on('click','#map-canvas', function(){
            $('#map-canvas').removeClass('scrolloff');
        });   

        $('.map_padding').click(function () {
            $('.map_padding div').css("pointer-events", "auto");
        });

        $( ".map_padding" ).mouseleave(function() {
          $('.map_padding div').css("pointer-events", "none"); 
        });


        $('body').on('click','#btnRoute', function(){
            calcRoute();
        });

      function fnTransaction(){

        var trans = $('#hidData').val();
        var transcount = 0;
        var skiprow = 0;

        var list ="";
        // var intTextBox = 0;
        
        if(trans!=""){
          
          $.ajax({
                  method: "POST",
                  url: "/jlogistic/manageroute",
                  dataType:'json',
                  data: {
                      'translist':trans,
                  },
                  beforeSend: function(){ 
                      
                  },
                  success: function(data) {

                        // alert(data[1].transactionid);
                        transcount = data.length-1;

                        endloc=data[transcount].address;
                        // alert(endloc);
                        calcRoute(startloc,endloc);

                        $.each(data, function (index, value) {

                        if(skiprow != transcount)
                        {
                          var contentID = document.getElementById('multiple-destination');
                          var newTBDiv = document.createElement('div');
            
                          intTextBox = intTextBox + 1;
                          
                          newTBDiv.setAttribute(
                          'id', 'strText' + intTextBox);

                          // alert(value.address);
                          
                          list="<div style='margin-top:5px;margin-bottom:5px;'><div class='form-group'><button type='button' class='btn btn-primary'";
                          list=list+"onClick='_ZNRPL_Sort_Element(" + intTextBox + ",0);'><span class='glyphicon glyphicon-arrow-up' aria-hidden='true'></span></button>";
                          list=list+"<button type='button' class='btn btn-primary' onClick='_ZNRPL_Sort_Element(" + intTextBox + ",1);'style='margin-right:5px;margin-left:5px;'>";
                          list=list+"<span class='glyphicon glyphicon-arrow-down' aria-hidden='true'></span></button><label class='sr-only' for='start'>Next Location :</label>";
                          list=list+"<input type='text' class='form-control' value='" + value.address + "' id='start" + intTextBox + "' placeholder='Enter Via Location'></div>";
                          list=list+"<button type='button' class='btn btn-success' onClick='_ZNRPL_Add_Element();'style='margin-right:5px;margin-left:5px;'>";
                          list=list+"<span class='glyphicon glyphicon-plus' aria-hidden='true'></span></button>";
                          list=list+"<button type='button' class='btn btn-danger' onClick='_ZNRPL_Remove_Element();'><span class='glyphicon glyphicon-minus' aria-hidden='true'></span></button></div>";
                          // alert(list);
                          newTBDiv.innerHTML =list;
                         
                          contentID.appendChild(newTBDiv);
                              
                          for (i = 0; i < intTextBox; i++) 
                          { 
                            j = i +1;
                            var input = document.getElementById('start'+j);
                            var searchBox = new google.maps.places.Autocomplete(input);
                              
                          }
                          skiprow = skiprow +1;

                          list = "";
                        }

                       });
                      // calcRoute(startloc,endloc);  
                      calcRoute();
                      arp_initialize();
                        
                  }


              });
              // calcRoute();
        }
        else{

            bootbox.alert('Please select group name', function(){
                window.open('{{asset('/')}}jlogistic/locationlist', '_self');
            });
            
        }

          
      }


    });

  </script>


@stop
