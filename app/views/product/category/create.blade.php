@extends('layouts.master')

@section('title') Category @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Categories</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Add Category</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url' => 'product/category/store', 'class' => 'form-horizontal', 'files'=> true)) }}
                        	<div class="form-group required @if ($errors->has('category_name')) has-error @endif">
							{{ Form::label('category_name', 'Category Name *', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-10">
									{{ Form::text('category_name', null, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
									{{ $errors->first('category_name', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<div class="form-group required @if ($errors->has('category_name_cn')) has-error @endif">
							{{ Form::label('category_name_cn', 'Category Name (Chinese)', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-10">
									{{ Form::text('category_name_cn', null, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
									{{ $errors->first('category_name_cn', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<div class="form-group required @if ($errors->has('category_name_my')) has-error @endif">
							{{ Form::label('category_name_my', 'Category Name (Bahasa)', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-10">
									{{ Form::text('category_name_my', null, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
									{{ $errors->first('category_name_my', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<hr />

							<div class="form-group @if ($errors->has('category_desc')) has-error @endif">
							{{ Form::label('category_desc', 'Description', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-10">
									{{ Form::textarea('category_descriptions', null, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
									{{ $errors->first('category_desc', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<hr />

							<div class="form-group @if ($errors->has('category_parent')) has-error @endif">
								{{ Form::label('category_parent', 'Parent', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-3">
									<select name="category_parent" id="category_parent" class="form-control">
										<option value="">Parent</option>
									<?php

										foreach($categoriesOptions as $cl) {
									?>
										<option value="<?php echo $cl["id"] ?>"><?php echo $cl["category_name"].' [ID: '.$cl["id"].']'; ?></option>
									<?php } ?>
									</select>
									{{ $errors->first('category_parent', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<hr />

							<div class="form-group @if ($errors->has('image')) has-error @endif">
								{{ Form::label('image_path', 'Image *', array('class'=> 'col-lg-2 control-label' )) }}
								<div class="col-lg-4">
									<input id="image" type="file" name="image" class="form-control">
									{{ $errors->first('image', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<hr />

							<div class="form-group @if ($errors->has('status')) has-error @endif">
							{{ Form::label('status', 'Category Status', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-3">
									{{ Form::select('status', array('0' => 'Inactive', '1' => 'Active'), null, array('class'=> 'form-control')) }}
									{{ $errors->first('status', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<hr />

							<div class="form-group @if ($errors->has('permission')) has-error @endif">
							{{ Form::label('permission', 'Category Permission', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-3">
									{{ Form::select('permission', $permissions, null, array('class'=> 'form-control')) }}
									{{ $errors->first('permission', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<hr />

							<div class="form-group @if ($errors->has('permission')) has-error @endif">
							    {{ Form::label('weight', 'Category Weight', array('class' => 'col-lg-2 control-label')) }}
								<div class="col-lg-3">
									{{ Form::select('weight', range(0, 10), 0, array('class'=> 'form-control')) }}
									{{ $errors->first('weight', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<hr />

							<div class="form-group @if ($errors->has('charity')) has-error @endif">
							{{ Form::label('charity', 'Charity Parent', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-3">
									{{ Form::select('charity', $charity, null, array('class'=> 'form-control')) }}
									{{ $errors->first('charity', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<hr />

							<div class="form-group">
						     	<div class="col-lg-10 col-lg-offset-2">
    								<input class="btn btn-default" data-toggle="tooltip" type="reset" value="Reset">
						     		@if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 5, 'AND'))
						     		<button type="submit" value="Save" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
						      		@endif
						      	</div>
						    </div>
                        {{ Form::close() }}
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
@stop

@section('inputjs')
<!-- File Input JavaScript -->
<script src="../../../js/fileinput.min.js"></script>
@stop

@section('script')
	$("#image").fileinput({
		allowedFileExtensions: ["jpg", "png"],
		maxFileSize: 500,
		showUpload: false
	});

	$('#image').on('fileerror', function(event, file, previewId, index, reader) {
		$('#buttonSave').attr('disabled', 'disabled')
	});

	$('#image').on('filebrowse', function(event) {
		$('#buttonSave').removeAttr('disabled');
	});
@stop
