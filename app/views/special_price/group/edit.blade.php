@extends('layouts.master')

@section('title') Edit SP Group @stop

@section('content')

<div id="page-wrapper">
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif

    @if ($message = Session::get('message'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-thumbs-up"></i> {{ $message }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Special Price Group</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(['role' => 'form', 'url' => '/special_price/group/update/' . $group->id, 'class' => 'form-horizontal']) }}
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-lock"></i> Special Price Group</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class="form-group">
                    {{ Form::label('id_number', 'ID Number', array('class'=> 'col-lg-2 control-label')) }}
                        <div class="col-lg-3">
                            <p class="form-control-static">{{$group->id}}</p>
                        </div>
                </div>

                <div class="form-group @if ($errors->has('seller_name')) has-error @endif">
                    {{ Form::label('group_name', 'Group name', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        {{ Form::text('group_name', $group->name, ['placeholder' => 'Group name', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class="form-group @if ($errors->has('seller_name')) has-error @endif">
                    {{ Form::label('min_purchase', 'Minimum Purchase ('.Config::get("constants.CURRENCY").')', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        {{ Form::text('min_purchase', $group->min_purchase, ['placeholder' => 'Minimum Purchase', 'class' => 'form-control']) }}
                    </div>
                </div>
                <div class="form-group @if ($errors->has('seller_name')) has-error @endif">
                    {{ Form::label('min_purchase_qty', 'Minimum Purchase QTY ', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        {{ Form::text('min_qty_purchase', $group->min_qty_purchase, ['placeholder' => 'Minimum Purchase QTY', 'class' => 'form-control']) }}
                    </div>
                </div>  
                <div class="form-group @if ($errors->has('seller_name')) has-error @endif">
                    {{ Form::label('is_free_delivery', 'Free Delivery  ', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        <input type="checkbox"  value="1" name="is_free_delivery_min_qty" id="is_free_delivery_min_qty" <?php if($group->is_free_delivery_min_qty == 1) {  echo "checked"; } ?> > (Free delivery if exceed min purchase quantity)
                    </div>
                </div> 
            </div>
        </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-lock"></i> Customer Listing</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class="form-group @if ($errors->has('cid')) has-error @endif">
                    <label class="col-lg-2 control-label" for="price_option">Customer</label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <div class="input-group-btn">
                                <span class="pull-left"><button id="addCustBtn" name="addCustBtn" class="btn btn-primary addCustBtn" data-toggle="tooltip" href="/special_price/ajaxcustomer"><i class="fa fa-plus"></i> Add Customer</span>
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
                            <tbody id="gctb">
                                @foreach($customers as $customer)
                                <tr class="custOption">
                                    <input type="hidden" value="{{$customer->id}}" name="cid[]" id="cid[]">
                                    <input type="hidden" value="{{$customer->id}}" name="cust" id="cust[]">
                                    <td class="col-sm-1 text-center"> {{ $customer->id }} </td>
                                    <td> {{ $customer->username }} </td>
                                    <td class="col-sm-1 text-center"> {{ $customer->firstname }} </td>
                                    <td class="col-sm-1 text-center"> {{ $customer->lastname }} </td>
                                    <td> {{ $customer->email }} </td>
                                    <td id="tdAction" class="text-center col-xs-1">
                                        <div id="deleteDiv" class="btn-group">
                                            <a class="btn btn-xs btn-danger inline" id="deleteCust" data-toggle="tooltip" href="#inline_content" data-original-title="Delete">
                                            <i class="fa fa-times"></i> Remove</a>
                                        </div>
                                    </td>
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
            {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
            {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
        </div>
    </div>
    @endif

    {{ Form::close() }}

</div>

@stop

@section('script')
    $(document).ready(function(){
        localStorage.clear()

        var rowCount = $('#gctb tr').length;
        var cust = document.getElementsByName("cust");

        for (var i=0; i < cust.length; i++) {
            localStorage.setItem("cid"+ i, cust[i].value);
        }
    });

    $('#addCustBtn').colorbox({
        iframe:true, width:"80%", height:"80%",
    });

    $(document).on("click", "#deleteCust", function(e) {
        e.preventDefault();

        if($('#gctb').length > 0) {
            $(this).closest("tr").remove();
            //$('#ptb tr').each(function(index){
            //    $(this).children().first().html(index + 1);
            //});
        } 
    });

    
@stop

