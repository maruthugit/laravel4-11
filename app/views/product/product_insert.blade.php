@extends('layouts.master')

@section('title', 'Import Product')

@section('content')
<?php 
$tempcount = 1;
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Import Products</h1>
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
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Import Details</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(['url' => 'product_insert/store', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true]) }}
                        <div class="form-group">
                            {{ Form::label('region_id', 'Region', ['class' => 'col-lg-2 control-label']) }}
                                <div class="col-lg-3">
                                    <select class="form-control" name="region_id" id="region_id">
                                        <?php foreach($regions as $region){?>
                                            <option value="<?php echo $region->id; ?>"><?php echo ucwords($region->region); ?></option>
                                        <?php } ?>
                                    </select>         
                                </div>
                        </div>
                        <div class="form-group @if ($errors->has('seller_id')) has-error @endif">
                            {{ Form::label('seller_id', 'Seller Name', ['class' => 'col-lg-2 control-label']) }}
                            <div class="col-lg-3">
                                {{ Form::select('seller_id', ['' => 'Select Seller Name'] + $sellersOptions, 107, ['class' => 'form-control']) }}
                                <!-- {{ Form::select('seller_id', $sellersOptions, null, ['class' => 'form-control']) }} -->
                                <!-- {{ $errors->first('seller_id', '<p class="help-block">:message</p>') }} -->
                                {{ $errors->first('seller_id', '<p class="help-block">The Seller is required</p>') }}
                            </div>
                        </div>
                        <div class="form-group @if ($errors->has('product_category')) has-error @endif">
                            {{ Form::label('product_category', 'Category', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                              
                                {{ Form::select('product_category', ['' => 'Select Category'] + $categoriesMainOptions, 729, ['class' => 'form-control']) }}
                                {{ $errors->first('product_category', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                        <div class="form-group @if ($errors->has('status')) has-error @endif">
                            {{ Form::label('status', 'Product Status', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                {{ Form::select('status', ['0' => 'Inactive', '1' => 'Active'], 0, ['class'=> 'form-control']) }}
                                {{ $errors->first('status', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                        <hr>
                        <div class="form-group @if ($errors->has('delivery_time')) has-error @endif">
                            {{ Form::label('delivery_time', 'Delivery Time', ['class'=> 'col-lg-2 control-label']) }}
                            <div class="col-lg-3">
                                {{ Form::select('delivery_time', [
                                    '24 hours'          => '24 hours',
                                    '1-2 business days' => '1-2 business days',
                                    '2-3 business days' => '2-3 business days',
                                    '3-7 business days' => '3-7 business days',
                                    '14 business days'  => '14 business days',
                                ], '2-3 business days', ['class' => 'form-control']) }}
                                {{ $errors->first('delivery_time', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                        <div class="form-group @if ($errors->has('zone_id')) has-error @endif">
                            {{ Form::label('delivery_fee', 'Delivery Fee', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                <select id="delivery_fee" class="form-control">
                                    <option value="0">Select Zone</option>
                                    @foreach ($zoneOptions as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                                {{ $errors->first('zone_id', '<p class="help-block">The delivery zone is required</p>') }}
                            </div>
                            <div class="col-lg-8">
                                <button type="button" id="add_zone" class="btn btn-primary" disabled><i class="fa fa-plus"></i> Add Zone</button>
                            </div>
                        </div>
                        <div id="zone_div"></div>
                        <hr>
                        <div class="form-group @if ($errors->has('csv')) has-error @endif">
                            {{ Form::label('file', 'Upload file', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-6">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">Select file</span>
                                        <span class="fileinput-exists">Change</span>
                                        <input type="file" name="csv" id="csv">
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                </div>
                                {{ $errors->first('csv', '<p class="help-block">The file is required</p>') }}
                                <p class="text-danger">* Save the file as <b>"Windows Comma Seperated (.csv)"</b> before import the CSV.</p>
                            </div>
                        </div>
                        <div class="form-group @if ($errors->has('status')) has-error @endif">
                            {{ Form::label('template', 'Template File', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <p class="form-control-static">{{ HTML::link(asset('/').'product_insert/files/template.csv', "template.csv", array('target'=>'_blank')) }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group @if ($errors->has('img')) has-error @endif">
                            {{ Form::label('file', 'Upload Image', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-6">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">Select file</span>
                                        <span class="fileinput-exists">Change</span>
                                        <input type="file" name="img" id="img">
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                </div>
                                {{ $errors->first('img', '<p class="help-block">The file is required</p>') }}
                                <p class="text-danger">* Image in a <b>Zip</b> file.</p>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2">
                                <!-- {{ Form::submit('Import CSV', ['class' => 'btn btn-large btn-primary btn-success']) }} -->
                                <input class="btn btn-default" data-toggle="tooltip" type="reset" value="Reset">
                                @if (Session::get('role_id') == '1')
                                    {{ Form::submit('Import CSV', ['class' => 'btn btn-large btn-primary btn-success']) }}
                                    <!-- <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save</button> -->
                                @endif
                            </div>
                        </div>
                    {{ Form::close() }}
                        <div <?php if(count($job) <= 0) { ?>style="display:none"<?php } ?> >
                            <hr>
                            <div class="form-group">
                                {{ Form::label('list_job', 'List of Job in Queue', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-10">
                                    <div class="dataTable_wrapper">
                                        <table class="table table-striped table-bordered table-hover" id="dataTables-details">
                                            <thead>
                                               <tr>
                                                    <th>#</th>
                                                    <th>ID</th>
                                                    <th>Uploaded File</th>
                                                    <th>Queuing File</th>
                                                    <th>Remark</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $fullpath = "files/".$file_log;
                                                echo " <a href=$fullpath>$logfile</a> ";
                                                ?>
                                                @foreach($job as $jobrow)
                                                    <tr class="odd gradeX">
                                                        <td>{{$tempcount++}}</td>
                                                        <td>{{$jobrow->id}}</td>
                                                        <td>{{ HTML::link(asset('/').'product_insert/files/'.$jobrow->in_file, $jobrow->in_file, array('target'=>'_blank')) }}</td>
                                                        <td>{{ HTML::link(asset('/').'product_insert/files/original_'.$jobrow->in_file, 'original_'.$jobrow->in_file, array('target'=>'_blank')) }}</td>
                                                        <td>{{$jobrow->remark}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.table-responsive -->           
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-10 col-lg-offset-2">
                                    @if (Session::get('role_id') == '1')
                                    {{ Form::open(['url' => 'product_insert/importnewproduct', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true,  'target' => '_blank']) }}
                                    {{ Form::submit('Process Job in Queue', ['class' => 'btn btn-large btn-primary']) }}
                                    {{ Form::close() }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')


// Select zone
$('select#delivery_fee').change(function () {
    var zone_id     = $('select#delivery_fee option').filter(':selected').val();

    if (zone_id > 0) {
        $('#add_zone').prop('disabled', false);
    } else {
        $('#add_zone').prop('disabled', true);
    }
});

// Add zone
var zone_index  = {{ $zoneCounter + 1 }};

$('#add_zone').click(function () {
    var zone_id     = $('select#delivery_fee option').filter(':selected').val();
    var zone_name   = $('select#delivery_fee option').filter(':selected').text();

    if (zone_id > 0) {
        $('#zone_div').append('<div id="zone_row_' + zone_index + '" class="form-group"><div class="col-lg-10 col-lg-offset-2"><input type="hidden" value="' + zone_id + '" name="zone_id[' + zone_index + ']"><div class="row"><div class="col-lg-3"><div class="input-group"><span class="input-group-addon"><i class="fa fa-globe fa-fw"></i></span><input type="text" name="zone_name[' + zone_index + ']" class="form-control" value="' + zone_name + '" disabled></div></div><div class="col-lg-2"><div class="input-group"><span class="input-group-addon">{{Config::get("constants.CURRENCY")}}</span><input type="text" name="zone_price[' + zone_index + ']" class="form-control text-right" placeholder="Delivery Fee"></div></div><div class="col-lg-2"><button type="button" class="btn btn-danger delete-zone" data-zone="' + zone_index + '"><i class="fa fa-minus"></i> Remove Zone</button></div></div></div></div>');
        $('select#delivery_fee option[value="' + zone_id + '"]').remove();
        $('#add_zone').prop('disabled', true);
        $('select#delivery_fee').val(0);
    }

    zone_index++;
});

// Delete zone
$(document).on('click', '.delete-zone', function() {
    var zone_index  = $(this).data('zone');
    var zone_id     = $('input[name="zone_id[' + zone_index + ']"]').val();
    var zone_name   = $('input[name="zone_name[' + zone_index + ']"]').val();

    $('#delivery_fee').append('<option value="' + zone_id + '">' + zone_name + '</option>');
    $('#zone_row_' + zone_index).remove();
});

// jQuery plugin to prevent double submission of forms
// URL: http://technoesis.net/prevent-double-form-submission-using-jquery/
jQuery.fn.preventDoubleSubmission = function() {
  $(this).on('submit',function(e){
    var $form = $(this);

    if ($form.data('submitted') === true) {
      // Previously submitted - don't submit again
      e.preventDefault();
    } else {
      // Mark it so that the next submit can be ignored
      $form.data('submitted', true);
    }
  });

  // Keep chainability
  return this;
};

$('form').preventDoubleSubmission();


@stop
