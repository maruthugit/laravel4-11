@extends('layouts.master')

@section('title') Edit SP Customer @stop

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

    {{ Form::open(['role' => 'form', 'url' => '/special_price/customer/update/'.$sp_id, 'class' => 'form-horizontal']) }}
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-lock"></i> Edit Special Price Customer Details - {{ $sp_id }}</h2>
        </div>
        <div class="panel-body"> 
            <div class="col-lg-12">
                <div class="form-group @if ($errors->has('cid')) has-error @endif">
                    <label class="col-lg-2 control-label" for="price_option">Customer</label>
                    <div class="col-sm-10">
                    <div class="clearfix">{{ $errors->first('cid', '<p class="help-block">:message</p>') }}</div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="col-sm-1 text-center">ID</th>
                                    <th class="hidden-xs hidden-sm col-sm-2">Username</th>
                                    <th class="hidden-xs hidden-sm col-sm-2 text-center">Firstname</th>
                                    <th class="hidden-xs hidden-sm col-sm-2 text-center">Lastname</th>
                                    <th class="cell-small col-sm-3">Email</th>
                                    <!-- <th class="cell-small text-center col-sm-1">Action</th> -->
                                </tr>
                            </thead>

                            <tbody id="ectb">
                                <tr class="custOption">
                                    <input type="hidden" value="{{ $customer->id }}" name="cid" id="cid">
                                    <td class="col-sm-1 text-center"> {{ $customer->id }} </td>
                                    <td> {{ $customer->username }} </td>
                                    <td> {{ $customer->firstname }} </td>
                                    <td> {{ $customer->lastname }} </td>
                                    <td> {{ $customer->email }} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="form-group @if ($errors->has('gid')) has-error @endif">
                    <label class="col-lg-2 control-label">Special Price Group</label>
                    <div class="col-sm-10">
                    <div class="clearfix">{{ $errors->first('gid', '<p class="help-block">:message</p>') }}</div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="col-sm-1 text-center">ID</th>
                                    <th class="hidden-xs hidden-sm col-sm-3 text-center">Group Name</th>
                                    <th class="hidden-xs hidden-sm col-sm-3 text-center">Company Name</th>
                                    <!-- <th class="cell-small text-center col-sm-1">Action</th> -->
                                </tr>
                            </thead>
                            <tbody id="gtb">
                                @foreach($groups as $group)
                                <tr class="groupOption">
                                    <input type="hidden" value="{{$group->id}}" name="group" id="group[]">
                                    <input type="hidden" value="{{$group->id}}" name="gid[]" id="gid[]">
                                    <td class="col-sm-1 text-center"> {{ $group->id }} </td>
                                    <td> {{ $group->name }} </td>
                                    <td> {{ $group->company_name }} </td>
                                </tr>
                                @endforeach
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
            <a class="btn btn-default" title="" href="/special_price/customer/">Back </a>
        </div>
    </div>
    @endif

    {{ Form::close() }}

</div>

@stop


@section('script')
    $(document).ready(function(){
        localStorage.clear()

        var rowCount = $('#cetb tr').length;
        var cust = document.getElementById("cid").value;
        localStorage.setItem("cust", cust);

        //alert('rowCount: '+ rowCount + ' - cust: '+cust);
        var group = document.getElementsByName("group");

        //alert('rowCount: '+rowCount + ' [group] ' + group.length);
        for (var i=0; i < group.length; i++) {
            //alert('rowCount: '+rowCount+ ' - ' +group.length);
            localStorage.setItem("gid"+ i, group[i].value);
            //alert('rowCount: '+i + ' [group] ' + group[i].value);

        }
    });

    $('#addCustBtn').colorbox({
        iframe:true, width:"80%", height:"80%",
    });

    $(document).on("click", "#deleteCust", function(e) {
        e.preventDefault();

        if($('#ectb').length > 0) {
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
