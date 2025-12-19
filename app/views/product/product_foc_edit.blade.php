@extends('layouts.master')

@section('title') FOC Product List @stop

@section('content')
<style>
    .alert-box{
        background-color: #fbfbfb;
        border: solid 1px #fff;
    }
</style>
<div id="page-wrapper" >
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Create FOC 
                <span class="pull-right">
               
                </span>
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row" id="foc">
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
                    <h3 class="panel-title"><i class="fa fa-plus"></i> Create New Form</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                
                    <form method="post" action="/product/savefoc"> 
                    
                        <div class="row">
                            <div class="col-md-8">
                            <div v-if="isSaving" class="ui segment">
                                <div class="ui active inverted dimmer">
                                    <div class="ui large text loader">Saving</div>
                                </div>
                                <p></p>
                                <p></p>
                                <p></p>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="page-header">
                                            <h4>General Settings</h4>
                                        </div>
                                        <form>
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label for="">From Date</label>
                                                        <div class="input-group" id="datetimepicker_from">
                                                            <input id="transaction_from" class="form-control" tabindex="1" name="start_date" type="text" v-model="transaction_from">
                                                            <span class="input-group-btn">
                                                                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label for="">To Date</label>
                                                        <div class="input-group" id="datetimepicker_to">
                                                            <input id="transaction_to" class="form-control" tabindex="1" name="end_date" type="text" v-model="transaction_to">
                                                            <span class="input-group-btn">
                                                                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label for="">Rule Code</label>
                                                        <select name="rule_code" id="rule_code" class="form-control" v-model="rule">
                                                            <option>TMT</option>
                                                            <option>PDO</option>
                                                            <option>ALL</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                <input type="checkbox" id="isActive" value="1" name="isActive" v-model="isActive"> Activate
                                                </label>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="page-header">
                                            <h4>Product & Conditions Settings</h4>
                                        </div>
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label for="">FOC Product ID</label>
                                                    <input  v-on:keyup="handleProduct(1,focProductID)" v-model="focProductID" id="foc_product_id" class="form-control" tabindex="1" name="foc_product_id" type="text">
                                                    <p class="help-block"><i class="fa fa-exclamation-circle"></i> Product that will given as free item.</p>

                                                    <label for="">Quantity</label>
                                                    <input   v-model="focProductQty" id="foc_product_qty" class="form-control" tabindex="1" name="foc_product_qty" type="text">
                                                    <p class="help-block"><i class="fa fa-exclamation-circle"></i> Quantity for FOC item.</p>
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="">Quantity Allocation</label>
                                                    <input v-model="focLimit" id="foc_limit_qty" class="form-control" tabindex="1" name="foc_limit_qty" type="text">
                                                    <p class="help-block"><i class="fa fa-exclamation-circle"></i> Total set.</p>
                                                </div>
                                            </div>
                                            <div class="row" v-if="ViewFOCProductID !== ''">
                                                <div class="col-md-10">
                                                    <div class="well" style="font-size: 12px;">@{{ViewFOCProductID}}</div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label for="">Only For Product ID</label>
                                                    <input v-on:keyup="handleProduct(2,focDependProductID)" v-model="focDependProductID" id="depend_product_id"  class="form-control" tabindex="1" name="depend_product_id" type="text">
                                                    <p class="help-block"><i class="fa fa-exclamation-circle"></i> FREE item will be given for selected product id</p>
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="">Only For Sales Amount</label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon" id="basic-addon1">RM</span>
                                                        <input v-model="focSalesAmount" type="text" class="form-control" placeholder="" id="sales_amount" name="sales_amount" aria-describedby="basic-addon1">
                                                    </div>
                                                    <p class="help-block"><i class="fa fa-exclamation-circle"></i> FOC only applicable for more than set sales amount </p>
                                                </div>
                                            </div>
                                            <div class="row" v-if="ViewFocDependProductID !== ''">
                                                <div class="col-md-10">
                                                    <div class="well" style="font-size: 12px;">@{{ViewFocDependProductID}}</div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label for="">Only For Seller</label>
                                                    <select class="form-control" name="seller" id="seller" v-on:change="handleSeller(SellerList)"  v-model="selectedSeller">
                                                        <option value="" >- Select Seller - </option>
                                                        <option v-for="seller in listSellers" v-bind:value="seller.id" >@{{seller.company_name}}</option>
                                                    </select>
                                                    <p class="help-block"><i class="fa fa-exclamation-circle"></i> FOC only applicable for selected seller's products</p>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="page-header">
                                            <h4>Region Settings</h4>
                                        </div>
                                       
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label for="">Country</label>
                                                    <select class="form-control">
                                                        <option value="458">Malaysia</option>
                                                    </select>
                                                    <p class="help-block"><i class="fa fa-exclamation-circle"></i> FOC only applicable for order from selected states</p>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label for="">State</label>
                                                        <div>
                                                            <div class="btn-group" style="display:grid ;">
                                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    Select States <span class="caret"></span>
                                                                </button>
                                                                <ul class="dropdown-menu" style="width: 100%;">
                                                                    <li v-for="states in listStates" v-on:click="addState(states.id,states.name)"><a>@{{states.name}}</a> </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <div class="well" style="font-size: 12px;">
                                                        <ul>
                                                            <li v-for="selected in listSelectedStates">@{{selected.state}} <a v-on:click="removeState(selected.id)">Remove <i class="fa fa-trash"></i></a> </li>
                                                        </ul>               
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <button type="button" v-on:click="updateFOC()" class="btn btn-default">Submit</button>
                                       
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-box" role="alert">
                                <div class="page-header">
                                    <h4><i class="fa fa-bell"></i> Guidelines</h4>
                                </div>
                                        <div class="media"> 

                                            <div class="media-body"> 
                                                <h4 class="media-heading">Rules Codes</h4> 
                                                <p>
                                                    All FOC item must has 1 rule of code to define flow and situation how FOC item will insert in Delivery Order.
                                                    <ul>
                                                        <li>TMT - More than sales amount</li>
                                                        <li>PDO - Only per Delivery Order</li>
                                                        <li>ALL - All transactios where match with conditions</li>
                                                    </ul>
                                                </p>
                                            </div> 
                                        </div> 
                                        <div class="media"> 
                                         
                                            <div class="media-body"> 
                                                <h4 class="media-heading">Product & Conditions</h4> 
                                                <p>
                                                    All FOC item must has 1 rule of code to define flow and situation how FOC item will insert in Delivery Order.
                                                    <ul>
                                                        <li>FOC Product ID - The product id that will be set as FOC item</li>
                                                        <li>Quantity Allocation - Allocation for campaign such as 500 Unit </li>
                                                        <li>Quantity  - Quantity that will be given as FOC for each purchase</li>
                                                        <li>Only For Product ID - FOC only will be given if this product exist in customer purchase</li>
                                                        <li>Only For Sales Amount - FOC only will be given customer purchase exceed the specific amount</li>
                                                        <li>Only For Seller - FOC only will be given only for selected seller's product been purchase</li>
                                                    </ul>
                                                </p>
                                            </div> 
                                        </div> 
                                        <div class="media"> 
                                           
                                            <div class="media-body"> 
                                                <h4 class="media-heading">Regions</h4> 
                                                <p>
                                                    All FOC item must has at least 1 selected states
                                                </p>
                                            </div> 
                                        </div> 

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    </div>


@stop


@section('script')

$(function() {

    // Save

    $(document).ready(function(){
    

    var nM = new Vue({
	el: '#foc',
	data: {
            updateID : 0,
            listCountries : [], 
            listStates : [],
            listSelectedStates : [],
            listSelectedStateID : [],
            listSellers : [],
            listRules : [],
            focProductQty : 1,
            focProductID : '',
            focProductAllocation :0,
            focLimit : '',
            focDependProductID : '',
            focSalesAmount : 0,
            ViewFOCProductID : '',
            ViewFocDependProductID : '',
            ViewSeller: '',
            SellerList : '',
            isSaving : false,
            selectedSeller : '',
            rule:'',
            transaction_from:'',
            transaction_to:'',
            isActive: false
	},
        mounted:function() {
            this.getList();
            this.getListStates();
            this.getFOCPageReady();
        },
        watch: {
           
        },
        methods: {
            getFOCPageReady: function(){

                var app = this;
                var currentUrl = window.location.pathname;
                var ar = currentUrl.split('/');
                var id = parseInt(ar[ar.length - 1]);

                $.ajax({
                    type: 'GET',
                    url: '/product/focdetails/'+id,
                    data: {},
                    dataType: "json",
                    success: function(result) { 

                        app.listSelectedStates = result.states;
                        app.focProductID = result.info.product_id;
                        app.focProductQty = result.info.reward_quantity;
                        app.focLimit = result.info.quantity;
                        app.focSalesAmount = result.info.base_reference;
                        app.focDependProductID = result.info.target_product_id;
                        app.handleProduct(1,app.focProductID);
                        app.handleProduct(2,app.focDependProductID);
                        app.selectedSeller = result.info.seller_id;
                        app.transaction_from = result.info.start_date;
                        app.transaction_to = result.info.end_date;
                        app.isActive = result.info.activation == 1 ? true : false;
                        app.rule = result.info.rule;
                        for(var x=0; x < app.listSelectedStates.length; x++){
                            app.listSelectedStateID.push(app.listSelectedStates[x].id);
                        }  
                        app.updateID = id;
                    }
                });
            },
            addState: function(id,state_name){
               this.stateSort(id,state_name);
            },
            getListStates: function(){
                
                var app = this;
                $.ajax({
                    type: 'GET',
                    url: '/product/states',
                    data: {},
                    dataType: "json",
                    success: function(result) { 
                        app.listStates = result;
                    }
                });
            },
            getList: function(){
                
                var app = this;
                $.ajax({
                    type: 'GET',
                    url: '/product/seller',
                    data: {},
                    dataType: "json",
                    success: function(result) { 
                        app.listSellers = result;
                    }
                });
            },
            stateSort: function(id,state_name){
                if(this.checkExistState(id)){
                    this.listSelectedStates.push({"id":id,"state":state_name});
                    this.listSelectedStateID.push(id);
                }
            },
            removeState: function(id){
                var index = this.listSelectedStateID.indexOf(id);
                if (index !== -1) this.listSelectedStateID.splice(index, 1);this.listSelectedStates.splice(index, 1);
            },
            handleProduct: function(elm,id){
                var app = this;
                console.log(id);
                 if( id !== null){
                    $.ajax({
                        type: 'POST',
                        url: '/product/info',
                        data: {'id':id},
                        dataType: "json",
                        success: function(result) { 
                            if(result.product_name) {
                                if(elm == 1) app.ViewFOCProductID = result.product_name;
                                if(elm == 2) app.ViewFocDependProductID = result.product_name;
                            }else{
                                if(elm == 1) app.ViewFOCProductID = '';
                                if(elm == 2) app.ViewFocDependProductID = '';
                            }
                        }
                    }); 
                }else{
                    if(elm == 1)  app.ViewFOCProductID = '';
                    if(elm == 2)  app.ViewFocDependProductID = '';
                }
            },
            handleSeller: function(SellerName){
                this.ViewSeller = SellerName;
            },
            checkExistState(id){
                if(this.listSelectedStateID.includes(id)){
                    return false;
                }
                return true;
            },
            updateFOC: function(){
                var app = this;

                var data = this.dataPrepare();
                app.isSaving = true;
                $.ajax({
                    type: 'POST',
                    url: '/product/updatefoc',
                    data: data,
                    dataType: "json",
                    success: function(result) { 
                        app.isSaving = false;
                        alert(result.message);
                        if(!result.isError)  location.reload();
                      
                    }
                });

            },
            dataPrepare: function(){
                
                var datas = {
                    'id': this.updateID,
                    'start_date':$('#transaction_from').val(),
                    'end_date':$('#transaction_to').val(),
                    'rule_code': $('#rule_code').val(),
                    'foc_product_id':$('#foc_product_id').val(),
                    'foc_qty':$('#foc_product_qty').val(),
                    'foc_limit_qty':$('#foc_limit_qty').val(),
                    'depend_product_id':$('#depend_product_id').val(),
                    'sales_amount':$('#sales_amount').val(),
                    'seller':$('#seller').val(),
                    'states': this.listSelectedStateID.join(),
                    'isActive': $("#isActive").is(':checked') ? 1:0
                };
        
               return datas;
            }

        },
    });
    
    $('body').on('click', '#noti-ico', function(){ 
        
        nM.getList();
    });
    
});

    $('#datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });
        $(function() {
        var date = $('#datepicker').datepicker({ dateFormat: 'yy-mm-dd'}).val();
    });

    $(function() {
        $('#transaction_to, #transaction_from').datetimepicker({
        format: 'YYYY-MM-DD'
        });
    });

    $(function() {
        $('#datetimepicker_from, #transaction_from').datetimepicker({
        format: 'YYYY-MM-DD'
        });
    });

});

@stop



