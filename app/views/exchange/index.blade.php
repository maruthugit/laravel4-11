@extends('layouts.master')
@section('title') Exchange Rate @stop
@section('content')

<div id="page-wrapper">
    <div class="row">
         <div class="col-lg-12">
            <h1 class="page-header">Exchange Rate<span class="pull-right">
                <!--<a class="btn btn-default" title="" data-toggle="tooltip" href="/banner/popupcreate"><i class="fa fa-plus"></i> Create New</a>-->
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="panel panel-default form-panel" id="RFRR_panel">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list-alt fa-fw"></i> Update Exchange Rate</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-8  col-xs-12">
                    <form id="rfrr_form">
                        <div class="ui active inverted dimmer" id="load-rfrr"> <div class="ui medium text loader">Loading ..</div></div>
                        <div class="form-group row">
                            <label for="" class="col-md-2 col-form-label col-form-label">Currency Option</label>
                            <div class="col-md-4">
                                <select id="currency-option" name="" class="form-control">
                                    <option value="2">MYR - USD</option>
                                    <option value="3">MYR - RMB</option>
                                    <option value="4">RMB - MYR</option>
                                    <option value="5">RMB - USD</option>
                                    <option value="7">USD - RMB</option>
                                    <option value="8">USD - MYR</option>
                                </select>
                            </div>
                        </div>
                       <h4 class="ui horizontal divider header">
                            <i class="fa fa-edit"></i>
                            Exchange Rate Setting
                        </h4>
                        <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label">From Currency
                            </label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <span class="input-group-addon" id="from-currency" style="min-width:100px;"><?php echo $currency->currency_code_from; ?></span>
                                    <input type="text" disabled name="rfrr_point" value="<?php echo number_format($currency->amount_from, 2, '.', ''); ?>" id="from-currency-amount" class="form-control" placeholder="Ex: 1.00" aria-describedby="basic-addon1">
                                </div>
                                
                            </div>
                            <label for="" class="col-sm-2 col-form-label">To Currency
                            </label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <span class="input-group-addon" id="to-currency" style="min-width:100px;"><?php echo $currency->currency_code_to; ?></span>
                                    <input type="text" name="rfrr_point" value="<?php echo number_format($currency->amount_to, 2, '.', ''); ?>" id="to-currency-amount" class="form-control" placeholder="Ex: 1.00" aria-describedby="basic-addon1">
                                </div>
                                
                            </div>
                        </div>
                       <hr>
                            <a class="btn btn-primary" id="rfrr_submit">Save</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Exchange Rate Listing </h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: hidden;" >
                <table class="table  table-striped table-hover" id="dataTables-banner">
                    <thead>
                        <tr>
                            <th class="col-md-1" style="max-width: 20px;">ID</th>
                            <th class="col-sm-1" style="text-align: center;">From Currency</th>
                            <th class="col-sm-1" style="text-align: center;">Base Amount</th>
                            <th class="col-sm-1" style="text-align: center;">To Currency</th>
                            <th class="col-sm-1" style="text-align: center;">Base Amount</th>
                            <th class="col-sm-2" style="text-align: center;">Last Updated At</th>
                            <th class="col-sm-3" style="text-align: center;">Updated By</th>         
                            <th class="col-sm-1" style="text-align: center;">Activation</th>         
                            <th class="text-center col-sm-3" style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

 <!-- Modal -->
  <div class="modal fade" id="mdl-history" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content" id="log-modal">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">@{{from_currency}} <i class="fa fa-arrow-right"></i> @{{to_currency}}</h4>
        </div>
        <div class="modal-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>DateTime</th>
                        <th>Update By</th>
                        <th>@{{from_currency}} </th>
                        <th></th>
                        <th>@{{to_currency}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in ListLog" >
                        <td>@{{item.updated_at}}</td>
                        <td>@{{item.updated_by}}</td>
                        <td>@{{item.currency_from_amount | decimals}}</td>
                        <td><i class="fa fa-arrow-right"></i></td>
                        <td>@{{item.currency_to_amount}}</td>
                    </tr>
                    <tr v-if="ListLog.length <= 0" style="text-align: center;"><td colspan="5">No Log Recorded</td></tr>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
@stop
@section('inputjs')
<script>
    
$(document).ready(function(){
    
    $("#load-rfrr").hide(); 

    $("#rfrr_submit").click(function(){
        $("#load-rfrr").show(); 
        $.ajax({
            method: "POST",
            url: "/exchange/update",
            dataType:'json',
            data: {
                "id": $("#currency-option").val(),
                "amt_from": $("#from-currency-amount").val(),
                "amt_to": $("#to-currency-amount").val(),
            },
            beforeSend: function(){
                $("#load-rfrr").show();
            },
            success: function(data) {
                $("#load-rfrr").hide();
                $('#dataTables-banner').DataTable().ajax.reload();  
            }
        })
        $("#RFRR_panel").show(); 
    });

    $("#currency-option").change(function(){

        var id = $("#currency-option").val();
        $.ajax({
            method: "GET",
            url: "/exchange/"+id,
            dataType:'json',
            data: {},
            beforeSend: function(){
                $("#load-rfrr").show();
            },
            success: function(data) {
               $("#from-currency").html(data.currency_code_from);
               $("#to-currency").html(data.currency_code_to);
               $("#to-currency-amount").val(data.amount_to);
               $("#from-currency-amount").val(data.amount_from);
                $("#load-rfrr").hide();
            }
        })       

    });
        
        
    var list = $('#dataTables-banner').dataTable({
            "autoWidth" : false,
            "processing": true,
            "language": {
             "loadingRecords": "Loading...",
             "processing":    '<div class="ui active inverted dimmer"> <div class="ui medium text loader"></div></div>'
            },
            "serverSide": true,
            "ajax": "/exchange/list",
            "columnDefs" : [
            { "sClass": "td-1st", "aTargets": [ 2 ] },
            { "sClass": "td-2st", "aTargets": [ 3 ] }],
            "columns" : [
                { data: 'id', name: 'id' },
                { data: 'currency_code_from', name: 'currency_code_from' , "className" : "text-center"},
                {
                    data: function ( row, type, val, meta ) {
                        var content =  row.amount_from;
                        return content;
                    },
                    className: "text-center"
                },
                { data: 'currency_code_to', name: 'currency_code_to', "className" : "text-center" },
                {
                    data: function ( row, type, val, meta ) {
                        var content =  row.amount_to;
                        return content;
                    },
                    className: "text-center"
                },
                { data: 'updated_at', name: 'updated_at', "className" : "text-center" },
                { data: 'updated_by', name: 'updated_by', "className" : "text-center" },
                {
                    data: function ( row, type, val, meta ) {
                        if(row.activation == 1){
                            var content =  '<span class="label label-success">Active</span>';
                        }else{
                            var content =  '<span class="label label-danger">Inactive</span>';
                        }
                        return content;
                    },
                    className: "text-center"
                },
                {
                    data: function ( row, type, val, meta ) {
                        var content =  '<a  row-id="'+row.id+'" class="btn btn-default v-history"><i class="fa fa-history"></i></a>';
                        return content;
                    },
                    className: "text-center"
                },
               
                ]
        });
        
        
        // VUE //
        var vmMSG = new Vue({
	el: '#log-modal',
	data: {
            from_currency: '',
            to_currency: '',
            ListLog : []
	},
        mounted:function() {
           
        },
        filters: {
            decimals: function (value) {
                console.log(value);
                return value;
            }
          },
        methods: {
            readyUI:function(){
               
            },
            viewModal :function(){
                
            },
            reset: function(){
               
            },
            getLog: function(ExchangeID){
                
               
                var vmMSG = this;
                $.ajax({
                    type: 'POST',
                    url: '/exchange/log',
                    data: {
                        "id" :ExchangeID
                    },
                    dataType: "json",
                    success: function(resultData) { 
                        vmMSG.ListLog = resultData.log;
                        vmMSG.from_currency = resultData.from_currency;
                        vmMSG.to_currency = resultData.to_currency;
                        $("#mdl-history").modal("show"); 
                    }
               });
            }
        },
    });
    
    $('body').on('click', '.v-history', function(){ 
    
        var id = $(this).attr("row-id");
        vmMSG.getLog(id);
        
    });
});
        </script>
@stop


@stop