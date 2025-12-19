@extends('layouts.master')
@section('title', 'Feedback')
@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Feedback Management
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}feedback"><i class="fa fa-refresh"></i></a>
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
                    <h3 class="panel-title"><i class="fa fa-list"></i> Feedback Listing</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-listing">
                            <thead>
                                <tr>
                                    <th>Insert Date</th>
                                    <th>Feedback ID</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Comment</th>
                                    <th>Type</th>
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
        <div class="modal fade" id="trslphotos" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">              
                  <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <img src="" class="imagepreview" style="width: 100%;" >
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('script')
$('#dataTables-listing').dataTable({
    "autoWidth": false,
    "processing": true,
    "serverSide": true,
    "ajax": "{{ URL::to('feedback/listing?'.http_build_query(Input::all())) }}",
    "order": [[0,'desc']],
    "columnDefs": [{
        "targets": "_all",
        "defaultContent": ""
    }],
    "columns": [
    { "data" : "insert_date"},
    { "data" : "id"},
    { "data" : "full_name"},
    { "data" : "email"},
    { "data" : "comment"},
    { "data" : "type" },
    { "data" : "Action" },
    ]
});

$(function() {
    $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
        format: 'YYYY-MM-DD'
    });
});
@stop

@section('inputjs')
<script>
    $('#trslphotos').on('shown.bs.modal', function (a, b,c) {
 var clickedImageUrl = a.relatedTarget.childNodes[0].src;
  displayPhotos(clickedImageUrl);
})

function displayPhotos(url) {
 console.log(url);
 $('.modal-body img').attr('src',url);
 $('#trslphotos').modal();
}
</script>
@stop

