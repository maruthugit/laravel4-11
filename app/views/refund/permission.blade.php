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
        <hr>
        <div class="panel-body">
            <div class="col-lg-12">
                @if (in_array(Session::get('username'), array('nadzri','kean', 'cocoyeo', 'melissa', 'maruthu'), true ))
                {{ Form::open(array('url'=>'refund/createpermission', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true)) }}
                    <div class="form-group">
                        {{ Form::label('user', 'User', array('class'=> 'col-lg-2 control-label')) }}
                        <input type="hidden" id="user_id" name="user_id" value="{{Input::old('user_id')}}">
                        <input type="hidden" id="user_email" name="user_email" value="{{Input::old('user_email')}}">
                        <div class="col-lg-3">
                            <div class="input-group">
                            <input type="text" id="username" name="username" class="form-control" value="{{Input::old('username')}}" readonly>
                            <span class="input-group-btn">
                                <button class="btn btn-primary selectUserBtn" id="selectUserBtn"  type="button" href="/refund/user-list"><i class="fa fa-plus"></i> User</button>
                            </span>
                            </div><!-- /input-group -->
                        </div><!-- /.col-lg-6 -->
                    </div>
                    <div class="form-group">
                        {{ Form::label('actions', 'Actions', array('class'=> 'col-lg-2 control-label')) }}
                        <div class="col-lg-3">
                            {{ Form::select('actions', ['0' => 'Developer', '1' => 'Customer Service', '2' => 'Operations', 
                                    '3' => 'Interns','4' => 'Operations Level-2', '5' => 'Finance', '6' => 'Admin'], 
                                    0, ['class' => 'form-control', 'tabindex' => 1,]) }}       
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                        <div class="col-lg-10">                                
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </div>
                {{ Form::close() }}
                @endif
            </div>
        </div>
        <hr>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: none;" >
                {{-- {{ Form::open(['url' => 'refund/bulkconfirm']) }} --}}
                <table class="table table-bordered table-striped table-hover" id="dataTables-permission">
                    <thead>
                        <tr>
                            <th class="col-sm-1">ID</th>
                            <th class="text-center col-sm-1">Username</th>
                            <th class="text-center col-sm-1">Email</th>
                            <th class="text-center col-sm-1">Role</th>
                            <th class="text-center col-sm-1">Status</th>
                            <th class="text-center col-sm-1">Created By</th>  
                            @if (Refund::permission(Session::get("username"), "0,4") )    
                            <th class="text-center col-sm-1">Action</th>
                            @endif
                        </tr>
                    </thead>
                </table>
                {{-- <button class="btn btn-primary" type="submit" id="confirmRefund"><i class="fa fa-check"></i> Confirm Refund</button> --}}
                {{-- {{ Form::close() }} --}}
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
            { "data" : "5", "class" : "text-center", "orderable" : false },
            @if (Refund::permission(Session::get("username"), "0,4") )
            { "data" : "6", "orderable" : false, "searchable" : false, "className" : "text-center"},   
            @endif  
            
        ]
    });

    $(document).on("click", "#deletePermission", function(e) {
        var link = $(this).attr("href");
        console.log(link);
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete refund permission for this user - ID: " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete refund permission id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 

@stop

<!-- 18/04/2022 - New file for refund's permission -->