@extends('layouts.master')
@section('title', 'eInvoice PracBix')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">eInvoice PracBix
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}account"><i class="fa fa-refresh"></i></a>
                </span>
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
                    <h3 class="panel-title"><i class="fa fa-list"></i> File Listing</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-pbx">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>eInvoice No</th>
                                    <th>File Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
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

<form id="complete_form" name="complete_form" action="/einvoice/pbx/complete" method="post">
    <input type="hidden" name="complete_id" id="complete_id" value="">
</form>
<script type="text/javascript">
function complete_account(complete_id) {
    if (confirm("Confirm complete the importing?")) {
        var tempid = document.getElementById("complete_id");
        tempid.value = complete_id;

        // var nameValue = document.getElementById("remove_transaction_id").value;
        // alert(nameValue);

        var tempform = document.getElementById("complete_form");
        tempform.submit();
    }
}
</script>
    
@stop

@section('script')

    $('#dataTables-pbx').dataTable({
        "autoWidth": false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('einvoice/pbx/list') }}",
        "order": [[3,'asc'], [0,'desc']],
        "columnDefs": [{
            "targets": "_all",
            "defaultContent": ""
        }],
        "columns": [
            { "data" : "id"},
            { "data" : "einv_no" },
            { "data" : "file_name" },
            { "data" : "status" },
            { "data" : "Action", "orderable" : false, "searchable" : false, "className" : "text-center" }
        ]
    });

    $('#generate-zip').click(function() {
    
        var formData = new FormData();
        formData.append('po_date', $('#po_date').val());

        $.ajax({
            url:'/purchase-order/pbx/generate-zip',
            data:formData,
            type:'post',
            processData: false,
            contentType: false,
            success:function(response) {
                if (response.status == 200) {
                    $('#dataTables-pbx').DataTable().ajax.reload();
                    alert(response.message);
                } else if (response.status == 404) {
                    alert(response.message);
                } else {console.log(response);
                    alert('Error generating zip file.');
                }

            },
        });
    });

@stop