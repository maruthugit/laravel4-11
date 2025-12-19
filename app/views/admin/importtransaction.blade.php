@extends('layouts.master')

@section('title') Import Transaction @stop

@section('content')
<style media="screen">
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
            <h1 class="page-header">Transaction Import</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
        <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-image"></i> Import CSV File Guides</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <h3><u>Import Transaction CSV file</u></h3>

                <!--<p class="text-warning">* Yellow highlights: For referrence purpose.</p>-->
                <!--<p class="text-danger">* Red highlights&nbsp;&nbsp;&nbsp; : Important columns, CANNOT edit.</p>-->
                <!--<p class="text-success">* Green highlights: CAN edit</p>-->
                <!--<div>-->
                <!--    {{ HTML::image('media/guide6.png', 'Guide6', array('class' => 'img-responsive')) }}-->
                    <!-- <img src="" alt="Mountain View" style="width:304px;height:228px"> -->
                <!--</div>-->
                
                <br>
                <h4 class="text-danger">Important Columns</h4>
                <p class="text-default">* <b>TRANSACTION DATE  </b>&nbsp;&nbsp;&nbsp;EXAMPLE: 08/30/2022(MM/DD/YYYY)</p>
                <p class="text-default">* <b>BILL TO (BUYER USERNAME)</b>&nbsp;&nbsp;&nbsp;EXAMPLE: username</p>
                <p class="text-default">* <b>SELF COLLECT</b>&nbsp;&nbsp;&nbsp;OPTIONS: 1(yes) or 0(no) </p>
                <p class="text-default">* <b>INVOICE TO ADDRESS</b>&nbsp;&nbsp;&nbsp;OPTIONS: 1(Invoice to delivery address) or 2(Invoice to buyer address)</p>
                <p class="text-default">* <b>EXTERNAL PLATFORM ORDER NUMBER</b>&nbsp;&nbsp;&nbsp;IF Data Not available Leave It Blank </p>
                <p class="text-default">* <b>PRODUCT ID</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 29195(ID)  (For Multiple Products :add with Vertical bar(|) separator : 29195|29196|29197)</p> 
                <h3>For Foreign Orders: Please Check Available Delivery region of the product Before Import </h3>
                <p class="text-default">* <b>QUANTITY</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 1(Quantity) (For Multiple Product : add with comma(,) separator example:1,2)</p>  
                <p class="text-default">* <b>STREET ADDRESS 1</b>&nbsp;&nbsp;&nbsp;EXAMPLE: address </p>
                <p class="text-default">* <b>STREET ADDRESS 2</b>&nbsp;&nbsp;&nbsp;EXAMPLE: address </p>
                <p class="text-default">* <b>POSTCODE</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 58200 </p>
                <p class="text-default">* <b>COUNTRY ID</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 458 </p>
                <p class="text-default">* <b>STATE</b>&nbsp;&nbsp;&nbsp; EXAMPLE: 458004</p>
                <p class="text-default">* <b>CITY</b>&nbsp;&nbsp;&nbsp; EXAMPLE: 4580465</p>
                <p class="text-default">* <b>DELIVERY CHARGES</b>&nbsp;&nbsp;&nbsp; IF Data Not available Leave It Blank</p>
                <p class="text-default">* <b>DELIVERY NAME</b>&nbsp;&nbsp;&nbsp;EXAMPLE: user </p>
                <p class="text-default">* <b>DELIVERY CONTACT NO</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 8987782883</p>
                <p class="text-default">* <b>SPECIAL MESSAGE</b>&nbsp;&nbsp;&nbsp;EXAMPLE: Delivery By evening </p>
                <br>
                <h3><u>Download Sample CSV file</u></h3>

                <p class="text-danger"><b>Important Notice: </b> CSV file<b class="text-success">  Header Column and Data Must Be in same order like sample CSV file </b>Check before import the CSV.</p>
                <a href="/media/csv/importtransaction/sample.csv" class="text-default" download><b>Dowload Sample CSV</b></a>
                 
                 
                 <h3><u>Please Check Mail To Know the Transaction ID And Missing Transaction and Status in the Attached CSV file</u></h3>
                 <p>Note: Attached CSV file Transaction Details in the Same order of Imported CSV file. </p>
            </div>  
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
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Transaction Import</h3>
                </div>

                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('class' => 'form-horizontal','id'=>'upload_csv', 'files'=> true)) }}

                            <div class="form-group @if ($errors->has('email')) has-error @endif">
                            {{ Form::label('Email', 'Email ID *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('email', Input::old('email'), array('class'=> 'form-control', 'autofocus' => 'autofocus',required)) }}
                                    {{ $errors->first('email', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('import')) has-error @endif">
                            {{ Form::label('import', 'Import File (csv) *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <input type="file" name="import" required="required" accept=".csv" id="import">
                                </div>
                            </div>
                <!--            <div class='form-group'>-->
                <!--    {{ Form::label('file', 'Upload file', array('class' => 'col-lg-2 control-label')) }}-->
                <!--    <div class="col-lg-6">-->
                <!--        <div class="fileinput fileinput-new input-group" data-provides="fileinput">-->
                <!--            <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>-->
                <!--            <span class="input-group-addon btn btn-default btn-file">-->
                <!--                <span class="fileinput-new">Select file</span>-->
                <!--                <span class="fileinput-exists">Change</span>-->
                <!--                <input type="file" name="import" required="required" accept=".csv">-->
                <!--            </span>-->
                <!--            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>-->
                <!--        </div>-->
                <!--        <p class="text-danger">* Save the file as <b>"Windows Comma Seperated (.csv)"</b> before import the CSV.</p>-->
                <!--    </div>-->
                <!--</div>-->

                           
                            <div class="form-group">
                                <div class="col-lg-10 col-lg-offset-2">
                                    <!-- <a class="btn btn-default" href="/product"><i class="fa fa-reply"></i> Cancel</a> -->
                                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                    <button id="buttonSave" type="submit" value="Save" class="btn btn-primary"><i class="fa fa-upload"></i> Import</button>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    
    <!-- /.row -->
@stop

@section('inputjs')
<!-- File Input JavaScript -->
<script src="../../../js/fileinput.min.js"></script>
@stop

@section('script')
    $(document).ready(function(){
        localStorage.clear()
    });
$(document).ready(function(){
 $('form#upload_csv').on('submit', function(e){
    e.preventDefault();    
   var formData = new FormData(this);
     var upl = document.getElementById("import");
    if(upl.files[0].size > 1000000)
    {
       alert("File too big!");
       return false;
    }
  $.ajax({
   url:"transactionimport",
   method:"POST",
   data:formData,
   dataType:'json',
   enctype: 'multipart/form-data',
   contentType:false,
   cache:false,
   processData:false,
   beforeSend: function() {
   $(".loader").show();  
  },          
  complete: function() {
  $(".loader").hide();
  },
   success:function(data)
   {
    if(data.status=='success'){
    	urls = "{{URL::to('transaction')}}";
      	window.location = urls;
    }
    if(data.status=='alert'){
    	alert(data.message);
    	return false;
    }
    if(data.status=='failed'){
    	urls = "{{URL::to('imports/importtransaction')}}";
      	window.location = urls;
    }
    if(data.status=='fails'){
    	urls = "{{URL::to('imports/importtransaction')}}";
      	window.location = urls;
    }
   }
  })
 });
 });

@stop
