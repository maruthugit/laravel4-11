@extends('layouts.master')

@section('title', 'Stock Transfer Listing')

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Stock Transfer Listing
                 <span class="pull-right">
                    <a class="btn btn-primary" href="{{ url('stock') }}"><i class="fa fa-refresh"></i></a>
                   
                        <a class="btn btn-primary" href="{{ url('stock/create') }}"><i class="fa fa-plus"></i></a>
                 
                </span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            @if (Session::has('success'))
                <div class="alert alert-success">
                    <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">&times;</button>
                </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Stock Transfer Listing</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTableStockListing">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-2">ST No</th>
                                     <th class="col-sm-2">DeliverFrom</th>
                                    <th class="col-sm-2">DeliverTo</th>
                                    <th class="col-sm-2">Expired Date</th>
                                      
                                     <th class="col-sm-1">uploadedfile</th>
                                     <th class="col-sm-1">Status</th>
                          
                                      
                                       
                                           <th class="col-sm-2">Sendby</th>
                                             <th class="col-sm-1">Recieved By</th>
                                                         <th class="col-sm-1">Approved By</th>
                                    <th class="text-center col-sm-1">Action</th>
                                     </tr>
                                   
                                   
                               
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deactivate" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="_method" value="DELETE">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Deactivate Brand</h4>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="submit">Deactivate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('script')
$('#dataTableStockListing').dataTable({
    'autoWidth': false,
    'processing': true,
    'serverSide': true,
    'ajax': '{{ URL::to('stock/stocks') }}',
    'order': [[0,'desc']],
    'columnDefs': [{
        'targets': '_all',
        'defaultContent': ''
    }],
    'columns': [
        {'data': '0'},
        {'data': '1'},
        {'data': '2'},
        {'data': '3'},
        {'data': '4'},
        {'data': '5'},
        {'data': '6'},
        {'data': '7'},
        {'data': '8'},
        {'data': '9', "className" : "text-center" },
        {'data': '10', "orderable" : false, "searchable" : false, "className" : "text-center"},
   
       
         ]
         });
        
   


 $(document).on("click", "#delete", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete stock id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
@stop