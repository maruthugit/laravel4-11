@extends('layouts.master')
@section('title', 'Report')
@section('content')

<?php

$tempcount = 1;

?>

<!-- For datepicker in create new report -->
<script src="//code.jquery.com/jquery-1.10.2.js"></script>

<script>
    $(function() {
        $( "#datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
        $( "#datepicker2" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
    });
</script>

<style>
#refer {
    display: none;
} 
</style>


<div id="page-wrapper">
<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Product Report </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">            
        @if (Session::has('message') OR $message)
            <div class="alert alert-danger">
                <i class="fa fa-exclamation"></i> {{ Session::get('message') }} {{ $message }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
        @if (Session::has('success') OR $success)
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }} {{ $success }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Generate Report</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url'=>'report/product', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true)) }}
                        <div class="form-group @if ($errors->has('email')) has-error @endif">
                        {{ Form::label('email', 'Email To', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('email', '', array('required'=>'required', 'placeholder' => 'abc@jocom.my, 123@jocom.my', 'class'=>'form-control'))}}
                                <p class="help-block" for="inputError">{{$errors->first('email')}}</p>
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('status')) has-error @endif">
                        {{ Form::label('status', 'Status', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-10">
                                <div class="col-lg-2">
                                    <label class="checkbox-inline">
                                    {{ Form::checkbox('active', '1', true) }} {{ Active }}
                                    </label>
                                </div>
                                <div class="col-lg-2">
                                    <label class="checkbox-inline">
                                    {{ Form::checkbox('inactive', '1') }} {{ Inactive }}
                                    </label>
                                </div>    
                                <div class="col-lg-2">
                                    <label class="checkbox-inline">
                                    {{ Form::checkbox('delete', '1') }} {{ Delete }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('group_label')) has-error @endif">
                        {{ Form::label('group_label', 'Price Label', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                {{ Form::select('group_label', array('0' => 'Group Price Label', '1' => 'Display Multi Price Label'), "0", ['class' => 'form-control']) }}                                
                            </div>
                            <p class="help-block">Display Multi Price Label will list out multiple row for product with more than 1 price label.</p>
                        </div>

                        <hr>

                        <div class="form-group @if ($errors->has('seller')) has-error @endif">
                        {{ Form::label('seller_name', '[Optional] Seller', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{ Form::select('seller', ['all' => 'All'] + $sellersOptions, "all", ['class' => 'form-control']) }}
                                <p class="help-block" for="inputError">{{$errors->first('seller')}}</p>
                            </div>

                        </div>

                        <div class="form-group @if ($errors->has('categories')) has-error @endif">
                            {{ Form::label('categories', '[Optional] Category', ['class' => 'col-lg-2 control-label']) }}
                            <div class="col-lg-4">
                                <label class="label-category">Available Category</label>
                                <ul id="available_category" class="form-control categories-container">
                                    @foreach ($categoriesOptions as $category)
                                        <li id="available_{{ $category['id'] }}" @if($category['status'] == 0) class="inactive" @endif>
                                        @if ( ! empty($category['category_name']))
                                            {{ $category['category_name'] }} @if ($category['permission']) **[Private] @endif [ID: {{ $category['id'] }}]
                                        @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-lg-1 img-switch"></div>
                            <div class="col-lg-4">
                                <label class="label-category">Selected Category</label>
                                <ul id="selected_category" class="form-control categories-container">
                                    @foreach ($categoriesOptions as $category)
                                        <li id="selected_{{ $category['id'] }}" class="hide @if($category['status'] == 0) inactive @endif">
                                        @if ( ! empty($category['category_name']))
                                            {{ $category['category_name'] }} @if ($category['permission']) **[Private] @endif [ID: {{ $category['id'] }}]
                                        @endif
                                        </li>
                                    @endforeach
                                </ul>
                                <div id="categories">
                                    @if (Session::has('_old_input') && ! empty(Input::old('categories')))
                                        <input id="old-main-category" type="hidden" value="{{ Input::old('main_category') }}">
                                        @foreach (Input::old('categories') as $category)
                                            <input class="old-categories" type="hidden" value="{{ $category }}">
                                        @endforeach
                                    @endif
                                    {{-- Hidden inputs will be generated on submit by JavaScript --}}
                                </div>
                            </div>
                            <div class="col-lg-10 col-lg-offset-2">
                                {{ $errors->first('categories', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('quantity')) has-error @endif">
                        {{ Form::label('quantity', '[Optional] Quantity', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                {{ Form::select('quantity', array('0' => 'Not Applicable', '1' => 'From 0 to 999999'), "0", ['class' => 'form-control']) }}
                            </div>
                            <div class="col-lg-1">
                                {{Form::text('quantity_from', '0', array('required'=>'required', 'placeholder' => '0', 'class'=>'form-control'))}}
                            </div>
                            <div class="col-lg-1">
                                {{Form::text('quantity_to', '10', array('required'=>'required', 'placeholder' => '10', 'class'=>'form-control'))}}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('stock')) has-error @endif">
                        {{ Form::label('stock', '[Optional] Stock', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                {{ Form::select('stock', array('0' => 'Not Applicable', '1' => 'From 0 to 999999'), "0", ['class' => 'form-control']) }}
                            </div>
                            <div class="col-lg-1">
                                {{Form::text('stock_from', '0', array('required'=>'required', 'placeholder' => '0', 'class'=>'form-control'))}}
                            </div>
                            <div class="col-lg-1">
                                {{Form::text('stock_to', '10', array('required'=>'required', 'placeholder' => '10', 'class'=>'form-control'))}}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('price')) has-error @endif">
                        {{ Form::label('price', '[Optional] Price Range', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                {{ Form::select('price', array('0' => 'Not Applicable', '1' => 'From '.Config::get("constants.CURRENCY").' to '.Config::get("constants.CURRENCY")), "0", ['class' => 'form-control']) }}
                            </div>
                            <div class="col-lg-1">
                                {{Form::text('price_from', '0', array('required'=>'required', 'placeholder' => '0', 'class'=>'form-control'))}}
                            </div>
                            <div class="col-lg-1">
                                {{Form::text('price_to', '100', array('required'=>'required', 'placeholder' => '100', 'class'=>'form-control'))}}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('referral')) has-error @endif">
                        {{ Form::label('referral', '[Optional] Referral Fees', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                {{ Form::select('referral', array('0' => 'Not Applicable', '1' => 'From Nett to Nett', '2' => 'From % to %'), "0", ['class' => 'form-control']) }}
                            </div>
                            <div class="col-lg-1">
                                {{Form::text('referral_from', '0', array('required'=>'required', 'placeholder' => '0', 'class'=>'form-control'))}}
                            </div>
                            <div class="col-lg-1">
                                {{Form::text('referral_to', '100', array('required'=>'required', 'placeholder' => '100', 'class'=>'form-control'))}}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('created')) has-error @endif">
                        {{ Form::label('created', '[Optional] Date', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                {{ Form::select('created', array('0' => 'Not Applicable', '1' => 'From Date to Date Created', '2' => 'From Date to Date Modified'), "0", ['class' => 'form-control']) }}
                            </div>
                            <div class="col-lg-2">
                                {{Form::text('created_from', date('Y-m-d', strtotime("yesterday")), array('id'=>'datepicker', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
                            </div>
                            <div class="col-lg-2">
                                 {{Form::text('created_to', date('Y-m-d'), array('id'=>'datepicker2', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-2">
                                <!-- {{ Form::button('Generate', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}} -->
                                <button class="btn btn-primary" type="submit"> Generate</button>
                            </div>
                        {{Form::input('hidden', 'generate', 'true')}}
                        </div>

                        <hr />

                        @if ($query['filename'] != NULL)
                        <div class="form-group" id="refer">
                        {{ Form::label('filename', 'File Name', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">
                                <p class="form-control-static">{{ $query['filename'] }}</p>
                            </div>
                        </div>

                        <div class="form-group" id="refer">
                        {{ Form::label('emailto', 'Email To', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">
                                <p class="form-control-static">{{ $query['email'] }}</p>
                            </div>
                        </div>

                        <div class="form-group" id="refer">
                        {{ Form::label('Count', 'Total Record', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">
                                <p class="form-control-static">{{ $query['count'] }}</p>
                            </div>
                        </div>

                        <div class="form-group" id="refer">
                        {{ Form::label('SQL', 'SQL Statement', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">
                                <p class="form-control-static">{{ $query['statement'] }}</p>
                            </div>
                        {{ Form::close() }}
                        </div>

                        <hr />
                        @endif

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Report Listing
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>File Name</th>
                                                        <th>Status</th>
                                                        <th>Request Date</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if (count($row)>0)
                                                    {
                                                    ?>
                                                    @foreach($row as $job)
                                                    <tr>
                                                        <td>{{$tempcount++}}</td>
                                                        <td>{{$job->in_file}}</td>
                                                        <td>
                                                            @if($job->status == 0)
                                                                In Queue
                                                            @elseif($job->status == 1)
                                                                In Process
                                                            @endif
                                                        </td>
                                                        <td>{{$job->request_at}}</td>
                                                        <td>
                                                            @if($job->status == 0)
                                                            <a class="btn btn-large btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}process/report/{{$job->id}}/?type=product">Process Now</a>
                                                            <a class="btn btn-danger" title="" data-toggle="tooltip" href="{{asset('/')}}process/cancel/{{$job->id}}/?type=product">Cancel</a>
                                                            <!-- {{ Form::open(['url' => 'abc/def', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true]) }}
                                                            {{ Form::submit('Process Now', ['class' => 'btn btn-large btn-primary']) }}
                                                            {{ Form::close() }} -->
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- /.table-responsive -->
                                    </div>
                                    <!-- /.panel-body -->
                                </div>
                                <!-- /.panel -->
                            </div>
                            <!-- /.col-lg-6 -->
                        </div>
                        <!-- /.row -->
                        
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    </div>
 
@stop


@section('script')
// Update selected category on submit bounced back
if ($('.old-categories').length > 0) {
    $('#selected_category li').addClass('hide');
    $('.old-categories').each(function () {
        $('#available_' + $(this).val()).addClass('hide');

        if ($(this).val() == $('#old-main-category').val()) {
            $('#main_' + $(this).val()).addClass('hide');
            $('#selected_' + $(this).val()).removeClass('hide').addClass('main');
        } else {
            $('#selected_' + $(this).val()).removeClass('hide');
        }
    });
}

// Main category
$('#main_category li').click(function () {
    var prefix = 'main_';
    var id = this.id.substr(prefix.length);
    $('#available_' + id).addClass('hide');

    $('#main_category li').each(function () {
        $(this).removeClass('hide');
    });

    $('#selected_category li.main').each(function () {
        $(this).addClass('hide').removeClass('main');
        $('#available_' + this.id.substr(('selected_').length)).removeClass('hide');
    });

    $('#main_' + id).addClass('hide');
    $('#selected_' + id).removeClass('hide').addClass('main');
});

// Available category
$('#available_category li').click(function () {
    var prefix = 'available_';
    var id = this.id.substr(prefix.length);

    $(this).addClass('hide');
    $('#selected_' + id).removeClass('hide');
});

// Selected category
$('#selected_category li').click(function () {
    var prefix = 'selected_';
    var id = this.id.substr(prefix.length);

    if ( ! $('#selected_' + id).hasClass('main'))
    {
        $(this).addClass('hide');
        $('#available_' + id).removeClass('hide');
    }
});

// Submit
$('#add').submit(function(event) {
    $('#selected_category li').not('.hide').each(function () {
        var prefix = 'selected_';
        var id = this.id.substr(prefix.length);

        $('#categories').append('<input type="hidden" name="categories[]" value="' + id + '">');
    });
});

@stop

