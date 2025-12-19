@extends('layouts.master')
@section('title', 'Account')
@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Accounting Management
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}account"><i class="fa fa-refresh"></i></a>
                    <!-- @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 5, 'AND'))
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}transaction/add"><i class="fa fa-plus"></i></a>
                    @endif -->
                </span>
            </h1>
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
                    <h2 class="panel-title"><i class="fa fa-image"></i> Import Accounting File Guides</h2>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        <h3><u>Important Note</u></h3>
                        <p class="text-danger">* If there is any action is Contact IT Dept, please STOP downloading or import any file to Accouting Software. Instead, contact IT Dept to resolve the issue.</p>
                        <br>
                        <p class="text-default">* <b>Sequence: It is important to download and import the file according to sequence, which sorting already in place with;</b><br>&nbsp;&nbsp;&nbsp;1) Date<br>&nbsp;&nbsp;&nbsp;2) Type(Category, Creditor, Debtor, Product, Invoice)<br>&nbsp;&nbsp;&nbsp;3) New then Update</p>
                        <p class="text-default">* <b>How: What to do next?</b><br>&nbsp;&nbsp;&nbsp;1) Click the BLUE button to download the file<br>&nbsp;&nbsp;&nbsp;2) Upload to accounting software<br>&nbsp;&nbsp;&nbsp;3) Click the GREEN button to mark Completed/Imported</p>
                        <br>
                        <p class="text-danger">* If there is any action is Contact IT Dept, please STOP downloading or import any file to Accouting Software. Instead, contact IT Dept to resolve the issue.</p>
                    </div>  
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Accounting File Listing</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-account">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Report Name</th>
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

<form id="complete_form" name="complete_form" action="{{asset('/')}}account/complete" method="post">
    <input type="hidden" name="complete_account_id" id="complete_account_id" value="">
</form>

<script type="text/javascript">
function complete_account(account_id) {
    if (confirm("Confirm complete importing to Accounting System?")) {
        var tempid = document.getElementById("complete_account_id");
        tempid.value = account_id;

        // var nameValue = document.getElementById("remove_transaction_id").value;
        // alert(nameValue);

        var tempform = document.getElementById("complete_form");
        tempform.submit();
    }
}
</script>
@stop

@section('script')
$('#dataTables-account').dataTable({
    "autoWidth": false,
    "processing": true,
    "serverSide": true,
    "ajax": "{{ URL::to('account/listing') }}",
    "order": [[3,'asc'], [0,'asc']],
    "columnDefs": [{
        "targets": "_all",
        "defaultContent": ""
    }],
    "columns": [
    { "data" : "id"},
    { "data" : "report_name"},
    { "data" : "file_name" },
    { "data" : "status" },
    { "data" : "Action", "orderable" : false, "searchable" : false, "className" : "text-center" }
    ]
});

$(function() {
    $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss'
    });
});
@stop
