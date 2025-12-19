@extends('layouts.master')

@section('title') Supplier Invoicer @stop
<script src="/js/angular_modified.js"></script>
@section('content')

<style>
    
    .suggestion-opt-box{
        position: absolute;
    background-color: #fff;
    z-index: 100;
    margin-right: 15px !important;
    margin-left: 15px !important;
    font-size: 13px;
    margin-top: 35px;
    }
    
    .sel-product-base{
        margin-bottom: 5px;
    }
    
    .attach-base{
/*        padding-left: 15px;
        padding-right: 15px;*/
    }
    
    .suggestion-opt{
        border: solid 1px #ddd;padding: 10px;top:-1px;border-top: 0px;
    }
    
    .suggestion-opt:hover{
        background-color: #f3f3f3;
        cursor: pointer;
    }
    
    
    #manual-process{
        display: none;
    }
    
</style>
<div id="page-wrapper" ng-app="jocom" ng-controller="jocomProduct">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"> Supplier Invoicer
            <span class="pull-right">
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
                    <h3 class="panel-title"><i class="fa fa-list"></i> Supplier Invoicer</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                       <div class="col-lg-12">
                        {{ Form::open(array('url'=>'account/saveinvoice', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true)) }}
                        <!-- <div class="form-group">
                            {{ Form::label('', 'Invoice Date', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <div class="input-group" id="datetimepicker_from">
                                    <input id="invoice_date" class="form-control" tabindex="1" name="invoice_date" type="text">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                    </span>
                                </div>
                            </div>
                        </div> -->
                        <!-- <div class="form-group">
                            {{ Form::label('', 'Supplier', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <select class="form-control" id="supplier" name="supplier">
                                    <?php foreach ($seller as $key => $value) { ?>
                                   <option value="<?php echo $value->id; ?>" ><?php echo $value->company_name; ?></option>
                                   <?php } ?>
                                </select>
                            </div>
                        </div> -->
                        <!-- <div class="form-group">
                            {{ Form::label('', 'Invoice Number', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <input type="text" class="form-control" id="invoice_number" name="invoice_number" placeholder="" aria-describedby="basic-addon2" style="text-transform: uppercase;">
                            </div>
                        </div> -->
                        <!-- <div class="form-group">
                            {{ Form::label('', 'Price Reduce Margin', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="" id="margin" name="margin" aria-describedby="basic-addon2">
                                    <span class="input-group-addon" id="basic-addon2">%</span>
                                </div>
                            </div>
                        </div> -->
                        <div class="form-group">
                            {{ Form::label('', 'Process Type', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <input type="radio" id="opt-tid" name="process_type" value="1" checked> From Transaction ID<br>
                                <!--<input type="radio" id="opt-man" name="process_type" value="2" > Manual<br>-->
                            </div>
                        </div>
                        <hr>
                        <section id="auto-process">
                            <div class="alert alert-info" role="alert">
                                <span><i class="fa fa-bullhorn"></i> Put transaction ID</span>
                            </div>
                            <div class="form-group">
                                {{ Form::label('', 'Transaction ID', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <input id="transaction_id" name="transaction_id" type="text" class="form-control">
                                </div>
                            </div>
                        </section>
                        <section id="manual-process">
                            <div class="alert alert-info" role="alert">
                                <span><i class="fa fa-bullhorn"></i> Add Product</span>
                            </div>
                            <div class="form-group">
                                {{ Form::label('', 'Choose Product', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-lg-12">
                                          <div class="input-group">
                                            <input type="text" class="form-control" id="targetSearch" placeholder="Search for..." ng-keyup="PFindProduct()">
                                            <span class="input-group-btn" </span>
                                              <button class="btn btn-default" type="button">Choose!</button>
                                            </span>
                                          </div><!-- /input-group -->
                                        </div><!-- /.col-lg-6 -->
                                        <div class="row suggestion-opt-box" id="suggestion-opt-box" style="">
                                            <div ng-repeat="x in suggestion_opt" class="col-md-12 suggestion-opt" >
                                                <div style="float:right;"><a class="btn btn-default" ng-click="SelectProduct(x.sku)"><i class="fa fa-plus"></i></a></div>
                                                <b>[[x.sku]]</b>
                                                <br>[[x.name]] 
                                            </div>
                                        </div>
                                    </div><!-- /.row -->
                                </div>
                            </div>
                        </section>
                        <hr />
                        <div class="form-group">
                        {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">                                
                                <button class="btn btn-primary" type="submit">Generate Invoice</button>
                            </div>
                        </div>
                       
                    </div>                     
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
            <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> Invoices </h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: hidden;font-size: 12px;" >
                <table class="table table-bordered table-striped table-hover" id="dataTables-invoice">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="20%">Invoice Number</th>            
                            <th width="25%">Transaction ID</th>
                            <th width="10%">Created On</th>
                            <!--<th width="25%">Email</th>-->
                            <th width="8%">Margin (%)</th>
                            <th width="5%">Download</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 15px !important;"></tbody>
                </table>
            </div>
        </div>
    </div>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    </div>


@stop
@section('inputjs')


<script>

var app = angular.module('jocom', []);
    
app.config(['$httpProvider', function($httpProvider) {
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
}]);

app.controller("jocomProduct", function($scope,$http,$timeout,$filter,$compile) {
    
    $scope.suggestion_opt = [];
    
    $scope.PFindProduct = function(){
        
        var suggestionOpt = '';
        $scope.suggestion_opt = [];
        //console.log("Search Value: " + $("#"+targetSearch).val());
        var product = $("#targetSearch").val();
        //console.log(product);
        if(product.length > 1){
           var request = $http({
                method: "post",
                url: "/api/searchproduct",
                data: {keyword:product }
            }).then(function successCallback(response) {
                console.log(response.data);
                $scope.suggestion_opt = response.data.search;
            
//                
//                angular.forEach(response.data.search, function(value, key) {
//                   // console.log(value);
//                    suggestionOpt = suggestionOpt + '<div class="col-md-12 suggestion-opt" ><div style="float:right;"><a class="btn btn-default" ng-click="AddProductBase('+"'"+value.sku+"','"+value.name.replace("'",'')+"','"+position+"'"+')"><i class="fa fa-plus"></i></a></div><b>'+value.sku+'</b><br>'+value.name+'</div>';
//    
//                });
//                
//                //"+"'"+value.sku+"','"+value.name+"','"+suggestionBox+"'
//                var temp = $compile(suggestionOpt)($scope);
//                $("#"+suggestionBox).html(temp)
//                $("#"+suggestionBox).show()
               // $compile(suggestionOpt)($scope);
                
                //console.log(suggestionOpt);
                
                // this callback will be called asynchronously
                // when the response is available
            }, function errorCallback(response) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });
           
       }else{
            $scope.suggestion_opt = [];
           // $(".suggestion-opt-box").css("display", "none")
       }
         

    }
    
    $scope.SelectProduct = function(){
        
        
        alert("Asasa");
    }
    
});


$(document).ready(function() {

$('#dataTables-invoice').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('account/listsupplierinvoice') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { data: 'id', name: 'id' },
            { data: 'invoice_number', name: 'invoice_number' },
            { data: 'transaction_id', name: 'transaction_id' },
            { data: 'created_at', name: 'created_at' },
            { data: 'margin', name: 'margin' },
            {
                data: function ( row, type, val, meta ) {
              
                    var content =  '<a class="btn btn-primary" title="Download Invoice" data-toggle="tooltip" href="/account/downloadsupplierinvoice/'+row.id+'"><i class="fa fa-file-text-o"></i></a>';
                        return content;
                   
                },
                className: "center"
            }
            ]
    });
    
    
    $("#opt-tid").click(function(){
        $("#manual-process").hide();
        $("#auto-process").show();
    });
    
    $("#opt-man").click(function(){
        $("#manual-process").show();
        $("#auto-process").hide();
    });
    
    $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    
});
</script>

@stop





