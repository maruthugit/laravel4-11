@extends('layouts.master')

@section('title') Warehouse Checklist @stop

@section('content')
<style type="text/css">
    .multiselect {
  width: 200px;
}

.selectBox {
  position: relative;
}

.selectBox select {
  width: 100%;
  font-weight: bold;
}

.overSelect {
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
}

#checkboxes {
  display: none;
  border: 1px #dadada solid;
}

#checkboxes label {
  display: block;
}

#checkboxes label:hover {
  background-color: #1e90ff;
}
.loader {
    background-color: rgb(54 50 50 / 95%);
    height: 100%;
    width: 100%;
    position: fixed;
    z-index: 1000;
    margin-top: 0px;
    top: 0px;
}
.loader-centered {
    position: absolute;
    left: 50%;
    top: 50%;
    height: 200px;
    width: 200px;
    margin-top: -100px;
    margin-left: -132px;
}
.object {
    width: 50px;
    height: 50px;
    background-color: rgba(255, 255, 255, 0);
    margin-right: auto;
    margin-left: auto;
    border: 4px solid #fff;
    left: 73px;
    top: 73px;
    position: absolute;
}

.square-one {
    -webkit-animation: first_object_animate 1s infinite ease-in-out;
    animation: first_object_animate 1s infinite ease-in-out;
}
.square-two {
    -webkit-animation: second_object 1s forwards, second_object_animate 1s infinite ease-in-out;
    animation: second_object 1s forwards, second_object_animate 1s infinite ease-in-out;
}
.square-three {
    -webkit-animation: third_object 1s forwards, third_object_animate 1s infinite ease-in-out;
    animation: third_object 1s forwards, third_object_animate 1s infinite ease-in-out;
}

@-webkit-keyframes second_object {
    100% {
        width: 100px;
        height: 100px;
        left: 48px;
        top: 48px;
    }
}
@keyframes second_object {
    100% {
        width: 100px;
        height: 100px;
        left: 48px;
        top: 48px;
    }
}
@-webkit-keyframes third_object {
    100% {
        width: 150px;
        height: 150px;
        left: 23px;
        top: 23px;
    }
}
@keyframes third_object {
    100% {
        width: 150px;
        height: 150px;
        left: 23px;
        top: 23px;
    }
}

@-webkit-keyframes first_object_animate {
    0% {
        -webkit-transform: perspective(100px);
    }
    50% {
        -webkit-transform: perspective(100px) rotateY(-180deg);
    }
    100% {
        -webkit-transform: perspective(100px) rotateY(-180deg) rotateX(-180deg);
    }
}

@keyframes first_object_animate {
    0% {
        transform: perspective(100px) rotateX(0deg) rotateY(0deg);
        -webkit-transform: perspective(100px) rotateX(0deg) rotateY(0deg);
    }
    50% {
        transform: perspective(100px) rotateX(-180deg) rotateY(0deg);
        -webkit-transform: perspective(100px) rotateX(-180deg) rotateY(0deg);
    }
    100% {
        transform: perspective(100px) rotateX(-180deg) rotateY(-180deg);
        -webkit-transform: perspective(100px) rotateX(-180deg) rotateY(-180deg);
    }
}

@-webkit-keyframes second_object_animate {
    0% {
        -webkit-transform: perspective(200px);
    }
    50% {
        -webkit-transform: perspective(200px) rotateY(180deg);
    }
    100% {
        -webkit-transform: perspective(200px) rotateY(180deg) rotateX(180deg);
    }
}

@keyframes second_object_animate {
    0% {
        transform: perspective(200px) rotateX(0deg) rotateY(0deg);
        -webkit-transform: perspective(200px) rotateX(0deg) rotateY(0deg);
    }
    50% {
        transform: perspective(200px) rotateX(180deg) rotateY(0deg);
        -webkit-transform: perspective(200px) rotateX(180deg) rotateY(0deg);
    }
    100% {
        transform: perspective(200px) rotateX(180deg) rotateY(180deg);
        -webkit-transform: perspective(200px) rotateX(180deg) rotateY(180deg);
    }
}

@-webkit-keyframes third_object_animate {
    0% {
        -webkit-transform: perspective(300px);
    }
    50% {
        -webkit-transform: perspective(300px) rotateY(-180deg);
    }
    100% {
        -webkit-transform: perspective(300px) rotateY(-180deg) rotateX(-180deg);
    }
}

@keyframes third_object_animate {
    0% {
        transform: perspective(300px) rotateX(0deg) rotateY(0deg);
        -webkit-transform: perspective(300px) rotateX(0deg) rotateY(0deg);
    }
    50% {
        transform: perspective(300px) rotateX(-180deg) rotateY(0deg);
        -webkit-transform: perspective(300px) rotateX(-180deg) rotateY(0deg);
    }
    100% {
        transform: perspective(300px) rotateX(-180deg) rotateY(-180deg);
        -webkit-transform: perspective(300px) rotateX(-180deg) rotateY(-180deg);
    }
}
</style>
<div class="loader" style="display: none;">

    <div class="loader-centered">
    <h1 style="color:white;margin-top: 200px !important;width: 127%;">Please Wait!!!</h1>

        <div class="object square-one"></div>
        <div class="object square-two"></div>
        <div class="object square-three"></div>
    </div>
</div>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"> Warehouse Checklist
            <span class="pull-right">
            <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}jlogistic/checklist"><i class="fa fa-refresh"></i></a>
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
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
                    <h3 class="panel-title"><i class="fa fa-list"></i> Warehouse Checklist Assigned & PreScan</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                       <div class="col-lg-12">
                        {{ Form::open(array('url'=>'jlogistic/checklistreport', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true)) }}
                                                <div class="form-group">
                            {{ Form::label('Generated From', 'Generated From', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                <select name="generatedfrom" class="form-control">
                                    <option value="assigned"> Assigned</option>
                                   <option value="prescan">PreScan</option>
                                    
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('Driver', 'Driver', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                <div class="multiselect">
                                <div class="selectBox" onclick="showCheckboxes()">
                                <select class="form-control" >
                                    <option>Select Driver</option>
                                </select>
                                <div class="overSelect"></div>
                            </div>
                            <div id="checkboxes" style="min-height:auto; overflow: auto; max-height: 200px;"> 
                                <input type="text" value="" name="searchColumn" id="searchColumn" style="width:181px" />
                                <label class="checkbow">
                                      <input type="checkbox" id="checkAll" style="margin-left:2px !important" data-name="all drivers"> All Drivers</label>
                            <?php 
                                 $driver_count=count($driver);
                               foreach ($driver as $key => $value) { 

                                  $small=strtolower($value->name);?>
                            <label class="checkbow">
                            <input type="checkbox" name="driver[]" value="{{$value->id}}" style="margin-left:2px !important" data-name="{{$small}}"/> {{$value->name}}</label>
                            <?php } ?>
                             <input type="hidden" name="driver_count" value="{{$driver_count}}">
                            </div>
                            </div>
                            </div>
                            </div>
                        <div class="form-group @if ($errors->has('created')) has-error @endif">
                        {{ Form::label('created', 'Range Date', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <div class="input-group" id="datetimepicker_from">
                                    {{ Form::text('transaction_from', Input::get('transaction_from'), ['id' => 'transaction_from', 'class' => 'form-control', 'tabindex' => 1]) }}
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-group" id="datetimepicker_to">
                                    {{ Form::text('transaction_to', Input::get('transaction_to'), ['id' => 'transaction_to', 'class' => 'form-control', 'tabindex' => 1]) }}
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                        {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">                                
                                <button class="btn btn-primary export" type="submit">Export</button>
                            </div>
                        </div>
                    </div>                     
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    </div>


@stop
@section('inputjs')

<script>
    var checkboxDivs = document.querySelectorAll('.checkbow');

document.querySelector('#searchColumn').addEventListener("input", function(e) {
    var inputValue = e.srcElement.value;
  for (var i = 0; i < checkboxDivs.length; i++) {
    var checkbox = checkboxDivs[i].children[0];
       var attr=$(checkbox).attr("data-name");
    if (attr.includes(inputValue)) {
        checkboxDivs[i].style.display = 'block';
    }   else {
        checkboxDivs[i].style.display = 'none';
    }
  }
}); 
   var expanded = false;

function showCheckboxes() {
  var checkboxes = document.getElementById("checkboxes");
  if (!expanded) {
    checkboxes.style.display = "block";
    expanded = true;
  } else {
    checkboxes.style.display = "none";
    expanded = false;
  }
}

$(document).ready(function() {
    
   $('#datetimepicker_from').datetimepicker({
        format: 'YYYY-MM-DD 00:00:00'
    });

   $('#datetimepicker_to').datetimepicker({
        format: 'YYYY-MM-DD 23:59:59'
    });
  $("#checkAll").click(function () {
     $('input:checkbox').not(this).prop('checked', this.checked);
 });
   
});
$(function(){
    $('.export').click(function() {
     ShowDownloadMessage(); 

 }); 
})

function ShowDownloadMessage()
{
   $(".loader").show();  
setTimeout(function(){ 
          $(".loader").hide();   
        }, 3000);    
    
}
</script>
@stop