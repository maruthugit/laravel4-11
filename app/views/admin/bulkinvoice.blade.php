@extends('layouts.master')

@section('title') Bulk Invoice @stop

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
            <h1 class="page-header">Generate Bulk Invoice</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="row">
        <div class="col-lg-12">
 @if (Session::has('pdf'))
 <input type="hidden" id="pdf" value="{{ Session::get('pdf') }}">
 @else
 <input type="hidden" id="pdf" value="">
 @endif
 @if (Session::has('filename'))
 <input type="hidden" id="filename" value="{{ Session::get('filename') }}">
 @else
 <input type="hidden" id="filename" value="">
 @endif
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
                    <h3 class="panel-title"><i class="fa fa-pencil"></i>Bulk Invoice Download </h3>
                </div>

                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url' => 'transaction/bulkinvoice','class' => 'form-horizontal','id'=>'invoice_request')) }}

                            <div class="form-group @if ($errors->has('email')) has-error @endif">
                            {{ Form::label('Invoice Type', 'Invoice Type*', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <select name="type" class="form-control" autofocus="autofocus" required id="invoice_type">
                                         <option value="1">Normal Invoice</option>
                                    </select>
                                   
                                    {{ $errors->first('type', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                             <div class="form-group @if ($errors->has('email_id')) has-error @endif">
                            {{ Form::label('Email ID', 'Email ID *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <input class="form-control" autofocus="autofocus" name="email_id" required>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('import')) has-error @endif">
                            {{ Form::label('Transaction ID', 'Transaction ID (Max 75 Transactions)*', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <textarea class="form-control" autofocus="autofocus" name="transaction_id" cols="50" rows="10" id="transaction_id" required></textarea>
                                </div>
                            </div>
               
                            <div class="form-group">
                                <div class="col-lg-10 col-lg-offset-2">
                                    <!-- <a class="btn btn-default" href="/product"><i class="fa fa-reply"></i> Cancel</a> -->
                                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                    <button id="buttonSave" type="submit" value="Save" class="btn btn-primary"><i class="fa fa-download"></i> Genarate</button>
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
window.onload = function() {
 var pdf=document.getElementById("pdf").value;
  var filename=document.getElementById("filename").value;

 var originalUrl = window.location.origin;

 if(pdf==""){
 }else{
   var a = document.createElement("a");
   a.href =originalUrl+"/"+pdf;
    a.download =filename;
    a.click();
 }

   
  };
 $('#invoice_type').on('change', function(){
 var invoice_type=document.getElementById("invoice_type").value;

 if(invoice_type=="1"){
 var link="{{url('transaction/bulkinvoice')}}";
 }else{
 var link="{{url('transaction/bulkagroinvoice')}}";
 }
 $('#invoice_request').attr('action',link)
 });
 
  });

@stop
