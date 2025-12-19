@extends('layouts.basic')

@section('content')


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
                    <h3 class="panel-title"><i class="fa fa-list"></i> Seller Listing</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-couponseller">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Company Name</th>
                                    <th>Username</th>
                                    <th>Select</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>                            
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    

<script type="text/javascript">
    function sendUserToParent(id) {
        window.opener.getUserFromChild(id);
        window.close();
        return false;
         // alert("ksjdhfs");
    }
</script>
@stop

@section('script')
    $('#dataTables-couponseller').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('coupon/listingseller') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
        "targets" : "_all",
        "defaultContent" : ""
        }],
        "columns" : [
        { "data" : "id"},
        { "data" : "company_name"},
        { "data" : "username" },
        { "data" : "Action", "orderable" : false, "searchable" : false, "className" : "text-center" }
        ]
    });
@stop

