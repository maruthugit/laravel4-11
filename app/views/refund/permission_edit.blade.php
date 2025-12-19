@extends('layouts.master')

@section('title') Permission for Refund @stop

@section('content')

<style>
    .specialadjust.form-group{marginx; margin-bottom: 15px;}
    .specialadjust.form-group .max-width-adj{max-width: 500px;}
    .inlineblock-middle{display: inline-block; vertical-align: top;}
    #clone-base{display: none;}

    .title-cat {
        color: #2daf73;
    }

    .box-con {
        padding:20px;
        border: solid 1px #ddd;
        cursor: pointer;
    }

    .box-con:hover {
        padding:20px;
        background-color: #f3f3f3;
        border: solid 1px #ddd;
    }

    .child {
    display: inline-block;
    }
        
    a.overall, a.byDate, a.overallSendingPlaceRegion, a.overallstrAllProductSold {
        cursor: pointer;
        font-weight: bold;
        color: #989898;
    }
</style>

<div id="page-wrapper">
    <div class="row">
         <div class="col-lg-12">
            <h1 class="page-header">Permission for Refund Management<span class="pull-right">
                 {{-- <input type="hidden" name="import" id="import">
                        {{ Form::submit('Import CSV', ['class' => 'btn btn-large btn-primary btn-success']) }} --}}
            
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}refund"><i class="fa fa-refresh"></i></a>
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/refund/create"><i class="fa fa-plus"></i></a>
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="panel panel-default">
    	@if (Session::has('message'))
            <div class="alert alert-danger">
                <i class="fa fa-exclamation"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">�</button>
            </div>
        @endif
        @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">�</button>
            </div>
        @endif
        
        <div class="panel-heading">
            <h3 class="panel-title">Permission for Refund Listing </h3>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                {{ Form::open(array('url'=>'refund/updatepermission/'.$permission->id, 'id' => 'add', 'class' => 'form-horizontal', 'files' => true)) }}
                    <div class="form-group">
                        {{ Form::label('user', 'User', array('class'=> 'col-lg-2 control-label')) }}
                        <input type="hidden" id="id" name="id" value="{{$permission->id}}">
                        <div class="col-lg-3">
                            <input type="text" id="username" name="username" class="form-control" value="{{$permission->username}}" readonly>                        
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('actions', 'Actions', array('class'=> 'col-lg-2 control-label')) }}
                        <div class="col-lg-3">
                            {{ Form::select('actions', ['0' => 'Developer', '1' => 'Customer Service', '2' => 'Operations', 
                                    '3' => 'Interns','4' => 'Operations Level-2', '5' => 'Finance', '6' => 'Admin'], 
                                    $permission->role, ['class' => 'form-control', 'tabindex' => 1,]) }}       
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('status', 'Status', array('class'=> 'col-lg-2 control-label')) }}
                        <div class="col-lg-3">
                            {{ Form::select('status', ['0' => 'Inactive', '1' => 'Active'], 
                                    $permission->status, ['class' => 'form-control', 'tabindex' => 1,]) }}       
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                        <div class="col-lg-10">                                
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>


@stop
@section('script')

    <!-- Refund permission -->
    $('#selectUserBtn').colorbox({
        iframe:true, width:"90%", height:"90%",
        onClosed: function() {
            localStorage.clear();
        }
    });
    <!-- Refund permission -->

    $('#dataTables-permission').dataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('refund/userPermission') }}",
        "order" : [[0,'desc']],
        "columnsDef" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0", "orderable" : false },
            { "data" : "1", "class" : "text-center", "orderable" : false },
            { "data" : "2", "class" : "text-center", "orderable" : false },
            { "data" : "3", "class" : "text-center", "orderable" : false },
            { "data" : "4", "class" : "text-center", "orderable" : false },
            { "data" : "5", "orderable" : false, "searchable" : false, "className" : "text-center"},     
            
        ]
    });

    $(document).on("click", "#deleteRefund", function(e) {
        var link = $(this).attr("href");
        console.log(link);
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete this refund - ID: " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete refund id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
@stop

<!-- 18/04/2022 - New file for refund's permission -->