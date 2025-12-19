@extends('layouts.master')

@section('title') Import Product @stop

@section('content')
<style media="screen">
    #load{
    width:100%;
    height:100%;
    position:fixed;
    z-index:9999;
    background-color:white;
    background:url("https://uat.all.jocom.com.my/img/spdfload.gif") no-repeat center center rgba(0,0,0,0.25)
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
            <h1 class="page-header">Product Import</h1>
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
                <h3><u>Import Product CSV file</u></h3>

                <!--<p class="text-warning">* Yellow highlights: For referrence purpose.</p>-->
                <!--<p class="text-danger">* Red highlights&nbsp;&nbsp;&nbsp; : Important columns, CANNOT edit.</p>-->
                <!--<p class="text-success">* Green highlights: CAN edit</p>-->
                <!--<div>-->
                <!--    {{ HTML::image('media/guide6.png', 'Guide6', array('class' => 'img-responsive')) }}-->
                    <!-- <img src="" alt="Mountain View" style="width:304px;height:228px"> -->
                <!--</div>-->
                
                <br>
                <h4 class="text-danger">Important Columns</h4>
                <p class="text-default">* <b>REGION COUNTRY ID  </b>&nbsp;&nbsp;&nbsp;EXAMPLE: 458(ID) - Malaysia</p>
                <p class="text-default">* <b>REGION ID</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 0(ID) : All Region</p>
                <p class="text-default">* <b>SELLER ID</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 69(ID) :Jocom eThirtySeven Sdn. Bhd. </p>
                <p class="text-default">* <b>PRODUCT NAME</b>&nbsp;&nbsp;&nbsp;EXAMPLE (Atleast 5 characters must): Sample Product </p>
                <p class="text-default">* <b>PRODUCT DESCRIPTION</b>&nbsp;&nbsp;&nbsp;EXAMPLE: Peak performance is driven by two things </p>
                <p class="text-default">* <b>PRIMARY CATEGORY</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 789(ID) :Beverages </p>
                <p class="text-default">* <b>SECONDARY CATEGORY</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 790(ID) :Tea  (For Multiple Secondary category: add with comma(,) separator example:789,780)</p>  
                <p class="text-default">* <b>PRICE LABEL</b>&nbsp;&nbsp;&nbsp;EXAMPLE: ( 24 x 500ml) X 1 Carton </p>
                <p class="text-default">* <b>ACTUAL PRICE</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 35 </p>
                <p class="text-default">* <b>PROMO PRICE</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 33 </p>
                <p class="text-default">* <b>COST PRICE</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 30 </p>
                <p class="text-default">* <b>FOREIGN MARKET ENABLE</b>&nbsp;&nbsp;&nbsp;Option1: 0(Not Enable) Option2: 1(Enable) </p>
                <h4 class="text-danger">IF FOREIGN MARKET NOT ENABLED   -- (FOREIGN ACTUAL PRICE AND FOREIGN PROMO PRICE COLUMN WILL LEAVE IT BLANK)</h4>  
                <p class="text-default">* <b>FOREIGN ACTUAL PRICE(USD)</b>&nbsp;&nbsp;&nbsp; </p>
                <p class="text-default">* <b>FOREIGN PROMO PRICE(USD)</b>&nbsp;&nbsp;&nbsp;</p>
                <p class="text-default">* <b>QUANTITY</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 30 </p>
                <p class="text-default">* <b>ACTUAL STOCK</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 30 </p>
                <p class="text-default">* <b>STOCK UNIT</b>&nbsp;&nbsp;&nbsp;EXAMPLE: gram </p>
                <p class="text-default">* <b>BASE SKU</b>&nbsp;&nbsp;&nbsp;EXAMPLE: JC-0000000000585  (For Multiple Base SKU: add with comma(,) separator example:JC-0000000000585,JC-0000000000364)</p>
                <p class="text-default">* <b>BASE SKU QUANTITY</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 1  (Base sku quantity must be in same order of Base SKU)-(For Multiple Base SKU Quantity: add with comma(,) separator example:1,2)</p>
                <p class="text-default">* <b>DELIVERY ZONE ID</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 9(ID) :West Malaysia </p>
                <p class="text-default">* <b>STATUS</b>&nbsp;&nbsp;&nbsp;Option1: 1(Active) Option2: 0(In Active) </p>


                                 <h4 class="text-danger">IF FOREIGN MARKET ENABLED   -- (ACTUAL PRICE AND PROMO PRICE COLUMN WILL LEAVE IT BLANK) BELOW TWO COLUMN MUST BE WITH DATA</h4>  



                <p class="text-default">* <b>FOREIGN ACTUAL PRICE(USD)</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 34 </p>
                <p class="text-default">* <b>FOREIGN PROMO PRICE(USD)</b>&nbsp;&nbsp;&nbsp;EXAMPLE: 33 </p>



                <br>
                <h3><u>Download Sample CSV file</u></h3>

                <p class="text-danger"><b>Important Notice: </b> CSV file<b class="text-success">  Header Column and Data Must Be in same order like sample CSV file </b>Check before import the CSV.</p>
                <a href="/media/csv/importproduct/sample.csv" class="text-default" download><b>Dowload Sample CSV</b></a>

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
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Product Import</h3>
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
       alert("File too big! upload below 1MB file");
       return false;
    }
  $.ajax({
   url:"importproduct",
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
    	urls = "{{URL::to('product')}}";
      	window.location = urls;
    }
    if(data.status=='failed'){
    	urls = "{{URL::to('imports/createimport')}}";
      	window.location = urls;
    }
    if(data.status=='fails'){
    	urls = "{{URL::to('imports/createimport')}}";
      	window.location = urls;
    }
   }
  })
 });
 });

@stop
