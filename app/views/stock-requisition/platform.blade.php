@extends('layouts.master')

@section('title') Stock Requisition Platforms @stop

@section('content')

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>
<div id="page-wrapper">
    @if ($errors->any())
        {{ implode('', $errors->all('<div class=\'bg-danger alert\'>:message</div>')) }}
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Stock Requisition Platform 
            <span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}stock-requisition/createplatform"><i class="fa fa-plus"></i></a>
                <a class="pull-right"></a>
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}stock-requisition/platforms"><i class="fa fa-refresh"></i></a>
              </span>
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
     @if (Session::has('success'))
                <div class="alert alert-success">
                    <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
                </div>
            @endif
@if (Session::has('message'))
                <div class="alert alert-danger">
                    <i class="fa fa-thumbs-up"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
                </div>
            @endif
    <div class="panel panel-default">
      <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Platform Listing </h2>
        </div>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: none">
                <table class="table table-striped table-bordered table-hover" id="table_id" >
                    <thead>
                        <tr>
                            <th class="col-sm-1">ID</th>
                            <th class="col-sm-1">Platform Name</th>
                            <th class="col-sm-1">Status</th>
                            <th class="col-sm-1 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php foreach ($list as $key => $value) { ?>
                        <tr>
                            <td><?php echo $value->id; ?></td>
                            <td><?php echo $value->platform_name; ?></td>
                            <td><?php if ($value->status == 1) {
                                echo '<button title="Active" alt="Active" class="btn btn-success">Active</button>';
                            }else{ 
                                echo '<button title="Inactive" alt="Inactive" class="btn btn-danger">Inactive</button>';
                            } ?></td>
                            <td><a class="btn btn-primary" title="" data-toggle="tooltip" href="/stock-requisition/platformedit/<?php echo $value->id; ?>"><i class="fa fa-pencil"></i></a>
                              
                              <a id="deleteBan" class="btn btn-danger" title="" data-toggle="tooltip" data-value="<?php echo $value->id; ?>" href="/stock-requisition/deleteplatform/<?php echo $value->id; ?>"><i class="fa fa-times"></i></a>
                            </td>
                            </tr>
                       <?php } ?>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>


@stop

@section('script')
   
$(document).on("click", "#deleteBan", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete this - " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete banner id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
@stop