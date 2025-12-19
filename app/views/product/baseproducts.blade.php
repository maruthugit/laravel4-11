@extends('layouts.master')

@section('title') Base Products  @stop

@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Base Products
<!--                 <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}product">Favorite List</a>
                </span>-->
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            @if (Session::has('message'))
                <div class="alert alert-success">
                    <i class="fa fa-thumbs-up"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
                </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Product List</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-products">
                            <thead>
                                <tr>
                                    <th width="2%" >Product ID</th>
                                    <th width="5%" >Seller</th>
                                    <th width="5%" >SKU</th>
                                    <th width="5%" >QRCode</th>
                                    <th width="10%">Product Name </th>
                                    <th width="5%">Region </th>
                                    <th width="5%">Inventory Status </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@stop


@section('inputjs')
<script>
    $( document ).ready(function() {
    
    var timer;
   


    $('#dataTables-products').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('product/baseproducts?'.http_build_query(Input::all())) }}",
        "order" : [[ 0, 'desc' ]],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0", "className" : "text-center" },
            { "data" : "1" },
        
            { "data" : "2", "className": "" },
            { "data" : "3", "className" : "" },
            { "data" : "4", "className" : "text-center" },
            { "data" : "5", "className" : "text-center" },
            { "data" : "6", "className" : "text-center" },
//            { "data" : "7", "className" : "text-center" },

            
        ]
    });

    
    
       });
</script>
@stop

   

