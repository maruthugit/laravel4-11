@extends('layouts.master')
@section('title') Product link to Inventory @stop
@section('content')

<div id="page-wrapper">
	<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Link Product to Inventory
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
                                    <th width="2%">ID</th>
                                    <th width="5%">SKU</th>
                                    <th width="15%">Product Name</th>
                                    <th width="2%" class="text-center">QR Code</th>
                                    <th width="5%" class="text-center">Delivery Time</th>
                                    <th width="5%" class="text-center">Root Stock ID</th>
                                    <th width="2%" class="text-center" >Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>

             </div>

    	</div>
    </div>

</div>
@stop
@section('inputjs')
<script>

    $(document).ready(function(){


			$('#dataTables-products').dataTable({
		            "autoWidth" : false,
		            "processing": true,
		            "serverSide": true,
		            "ajax": "{{ URL::to('warehouse/productlist?'.http_build_query(Input::all())) }}",
		            "order" : [[ 0, 'desc' ]],
		            "columnDefs" : [{
		                "targets" : "_all",
		                "defaultContent" : "",
		                "orderable": false, "targets": [2,3],
		            }],
		            "columns" : [
		                { "data" : "0", "className" : "text-center" },
		                { "data" : "1" },
		                { "data" : "2" },
		                { "data" : "3" },
		                { "data" : "4", "className" : "text-center" },
		                { "data" : "5", "className" : "text-center" },
		                { "data" : "6", "className" : "text-center" }
		                // { data: function ( row, type, val, meta ) {
		                //     return '<button style="text-align:center;"  class="btn btn-default triggerAdd" data-transaction-id="'+row[0]+'" type="button" title="Add to Inventory">Add to Inventory <i class="fa fa-angle-double-right"></i> </button>';
		                //     }
		                // }
		            ]
		        });

	});
</script>
@stop
