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
        $( "#datepicker, #datepicker2" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
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
            <h1 class="page-header">Consignment Report </h1>
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
                        {{ Form::open(array('url'=>'report/consignmentreport', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true)) }}
                        <div class="form-group @if ($errors->has('region_id')) has-error @endif">
                            {{ Form::label('transaction_region', 'Region', ['class' => 'col-lg-2 control-label']) }}
                            <div class="col-lg-3">
                                <select class="form-control" id="region_country_id" name="region_country_id" readonly>
                                <?php foreach ($countries as $key => $value) { ?>
                                    <option value="<?php echo $value->id; ?>" <?php if((int)$value->id == 458){ echo "selected";} ?>><?php echo $value->name;?></option>
                                <?php } ?>
                            </select>
                            <select class="form-control" id="region_id" name="region_id" style="margin-top: 10px;">
                                <?php if(Session::get('branch_access') != 1){?>
                                <option value="">All region</option>
                                <?php } ?>
                                <?php foreach ($regions as $key => $value)  { ?>
                                    <option value="<?php echo $value->id; ?>" <?php if((int)$value->id == 1) { echo "selected";} ?>><?php echo $value->region; ?></option>
                                <?php  } ?>
                            </select>
                            </div>
                                                        
                        </div>
                        <div class="form-group @if ($errors->has('email')) has-error @endif">
                        {{ Form::label('email', 'Email To', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('email', '', array('required'=>'required', 'placeholder' => 'abc@jocom.my, 123@jocom.my', 'class'=>'form-control'))}}
                                <p class="help-block" for="inputError">{{$errors->first('email')}}</p>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group @if ($errors->has('seller')) has-error @endif">
                        {{ Form::label('seller_name', 'Seller', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-4">
                                {{ Form::text('seller', Input::get('seller'), ['autocomplete' => 'off', 'class' => 'form-control', 'id' => 'seller', 'placeholder' => 'Seller username (Not company Name) or ID', 'required' => 'required']) }}
                                <div id="sellerAutoComplete" class="list-group autocomplete"></div>
                                <p class="help-block" for="inputError">{{$errors->first('seller')}}</p>
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('amount')) has-error @endif">
                        {{ Form::label('amount', '[Optional] Amount Range', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                {{ Form::select('amount', array('0' => 'Not Applicable', '1' => 'From '.Config::get("constants.CURRENCY").' to '.Config::get("constants.CURRENCY")), "0", ['class' => 'form-control']) }}
                            </div>
                            <div class="col-lg-1">
                                {{Form::text('amount_from', '0', array('required'=>'required', 'placeholder' => '0', 'class'=>'form-control'))}}
                            </div>
                            <div class="col-lg-1">
                                {{Form::text('amount_to', '100', array('required'=>'required', 'placeholder' => '100', 'class'=>'form-control'))}}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('created')) has-error @endif">
                            {{ Form::label('created', '[Optional] Date', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                {{ Form::select('created', array('0' => 'Not Applicable', '1' => 'From Date to Date Ordered', '2' => 'From Date to Date Inserted','3' => 'From Date to Date Invoiced'), "0", ['class' => 'form-control']) }}
                            </div>
                            <div class="col-lg-2">
                                {{Form::text('created_from', date('Y-m-d', strtotime("yesterday")), array('id'=>'datepicker', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
                            </div>
                            <div class="col-lg-2">
                                 {{Form::text('created_to', date('Y-m-d'), ['id'=>'datepicker2', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'])}}
                            </div>
                            <p class="help-block">Date Inserted: Date input to system.</p>
                        </div>

                        <div class="form-group">
                            {{ Form::label('grn_date', 'GRN Date', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                {{ Form::select('grn_date', ['1' => 'Purchase Order Date', '2' => 'Delivery Date', '3' => 'From Date to Date Inserted'], "1", ['class' => 'form-control']) }}
                            </div>
                            <p class="help-block">Date Inserted: Date input to system.</p>
                        </div>
                        
                        <div class="form-group">
                            {{ Form::label('', '', ['class' => 'col-lg-2 control-label']) }}
                            <div class="col-lg-10">                                
                                <button class="btn btn-primary" type="submit">Generate</button>
                            </div>
                            {{ Form::input('hidden', 'generate', 'true') }}
                        </div>

                        @if ($query['filename'] != NULL)
                        <hr />
                        
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
                                                            <a class="btn btn-large btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}process/report/{{$job->id}}/?type=consignment">Process Now</a>
                                                            <a class="btn btn-danger" title="" data-toggle="tooltip" href="{{asset('/')}}process/cancel/{{$job->id}}/?type=trans">Cancel</a>
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
    var timer;

    $('#customer').keydown(function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            if ($('#customer').val()) {
                $.ajax({
                    url: '{{ url('report/customersearch?keyword=') }}' + $('#customer').val() + '&limit=10',
                    success: function (result) {
                        if ($('#customer').is(":focus")) {
                            var candidates = $.parseJSON(result);
                            var size = 0;

                            $('#customerAutoComplete').html('');

                            $.each(candidates, function (i, candidate) {
                                var customerName = candidate.full_name;

                                if (customerName.search($('#customer').val())) {
                                    var clickAction = "$('#customerAutoComplete').html(''); $('#customer').val('" + candidate.username + "'); return false;";

                                    $('#customerAutoComplete').append('<a href="#" onclick="' + clickAction + '" class="list-group-item">' + candidate.full_name + ' ('  + candidate.username + ')' + '</a>');
                                    size++;
                                }
                            });

                            if ($('#customer').is(':focus') && size > 0) {
                                $('#customerAutoComplete').show();
                            }
                        }
                    }
                });
            } else {
                $('#customerAutoComplete').html('');
            }
        }, 200);
    });

    $('html').click(function () {
        $('#customerAutoComplete').html('').hide();
    });

    $('#seller').keydown(function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            if ($('#seller').val()) {
                $.ajax({
                    url: '{{ url('report/sellersearch?keyword=') }}' + $('#seller').val() + '&limit=10',
                    success: function (result) {
                        if ($('#seller').is(":focus")) {
                            var candidates = $.parseJSON(result);
                            var size = 0;

                            $('#sellerAutoComplete').html('');

                            $.each(candidates, function (i, candidate) {
                                var sellerName = candidate.company_name;

                                if (sellerName.search($('#seller').val())) {
                                    var clickAction = "$('#sellerAutoComplete').html(''); $('#seller').val('" + candidate.username + "'); return false;";

                                    $('#sellerAutoComplete').append('<a href="#" onclick="' + clickAction + '" class="list-group-item">' + candidate.company_name + ' ('  + candidate.username + ')' + '</a>');
                                    size++;
                                }
                            });

                            if ($('#seller').is(':focus') && size > 0) {
                                $('#sellerAutoComplete').show();
                            }
                        }
                    }
                });
            } else {
                $('#sellerAutoComplete').html('');
            }
        }, 200);
    });

    $('html').click(function () {
        $('#sellerAutoComplete').html('').hide();
    });

    loadOptionRegion($('#region_country_id').val());
    $('body').on('change', '#region_country_id', function() {
        loadOptionRegion($(this).val());
    });

    function loadOptionRegion(countryID){
        
        $.ajax({
                method: "POST",
                url: "/region/country",
                dataType:'json',
                data: {
                    'country_id':countryID
                },
                beforeSend: function(){
                },
                success: function(data) {
                    console.log(data.data.region);
                    var regionList = data.data.region;
                    var str = '<option value="0">All Region</option>';
                    $.each(regionList, function (index, value) {
                        str = str + "<option value='"+value.id+"'>"+value.region+"</option>";
                       console.log(str);
                    });
                    $("#region_id").html(str);
                    
                }
          })
        
    }
    
@stop