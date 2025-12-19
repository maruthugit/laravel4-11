@extends('layouts.master')

@section('title') Create SP Customer @stop

@section('content')

<div id="page-wrapper">
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Special Price Customer</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(['role' => 'form', 'url' => '/special_price/customer/store', 'class' => 'form-horizontal']) }}
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-lock"></i> Add Special Price Customer Details</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class="form-group @if ($errors->has('cid')) has-error @endif">
                    <label class="col-lg-2 control-label" for="price_option">Customer</label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <div class="input-group-btn">
                                <span class="pull-left"><button id="addCustBtn" name="addCustBtn" class="btn btn-primary addCustBtn" data-toggle="tooltip" href="../ajaxcustomer"><i class="fa fa-plus"></i> Add Customer</span>
                            </div>
                        </div>
                        <br />
                    <div class="clearfix">{{ $errors->first('cid', '<p class="help-block">:message</p>') }}</div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="col-sm-1 text-center">ID</th>
                                    <th class="hidden-xs hidden-sm col-sm-2">Username</th>
                                    <th class="hidden-xs hidden-sm col-sm-2 text-center">Firstname</th>
                                    <th class="hidden-xs hidden-sm col-sm-2 text-center">Lastname</th>
                                    <th class="cell-small col-sm-3">Email</th>
                                    <th class="cell-small text-center col-sm-1">Action</th>
                                </tr>
                            </thead>
                            <tbody id="ctb">
                                <tr id="emptycustomer">
                                    <td colspan="6">No customer is added.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="form-group @if ($errors->has('gid')) has-error @endif">
                    <label class="col-lg-2 control-label">Special Price Group</label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <div class="input-group-btn">
                                <span class="pull-left"><button id="addGroupBtn" name="addGroupBtn" class="btn btn-primary addGroupBtn" data-toggle="tooltip" href="../ajaxgroup"><i class="fa fa-plus"></i> Add Group</span>
                            </div>
                        </div>
                        <br />
                    <div class="clearfix">{{ $errors->first('gid', '<p class="help-block">:message</p>') }}</div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="col-sm-1 text-center">ID</th>
                                    <th class="hidden-xs hidden-sm col-sm-3 text-center">Group Name</th>
                                    <th class="hidden-xs hidden-sm col-sm-3 text-center">Company Name</th>
                                    <th class="cell-small text-center col-sm-1">Action</th>
                                </tr>
                            </thead>
                            <tbody id="gtb">
                                <tr id="emptygroup">
                                    <td colspan="6">No group is added.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                
            </div>
        </div>
    </div>

    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 8, 5, 'AND'))
    <div class='form-group'>
        <div class="col-lg-10">
            {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
        </div>
    @endif

    {{ Form::close() }}

</div>

@stop


@section('script')
    $(document).ready(function(){
        localStorage.clear()
    });

    $('#addCustBtn').colorbox({
        iframe:true, width:"80%", height:"80%",
    });

    $(document).on("click", "#deleteCust", function(e) {
        e.preventDefault();

        if($('#ctb').length > 0) {
            $(this).closest("tr").remove();
            //$('#ptb tr').each(function(index){
            //    $(this).children().first().html(index + 1);
            //});
        } 
        //else {
        //    bootbox.alert({
        //        title: "Error",
        //        message: "Please insert at least one user.",
        //    });
        //}
    });

    $('#addGroupBtn').colorbox({
        iframe:true, width:"80%", height:"80%",
    });

    $(document).on("click", "#deleteGroup", function(e) {
        e.preventDefault();

        if($('#gtb').length > 0) {
            $(this).closest("tr").remove();
        } 
        else {
            bootbox.alert({
                title: "Error",
                message: "Please insert at least one group.",
            });
        }
    });
@stop
