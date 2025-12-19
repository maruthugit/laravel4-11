@extends('layouts.default')

@section('content')
	<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Categories <span class="pull-right"><a class="btn btn-default" title="" data-toggle="tooltip" href="{{ URL::route('product.category.index') }}"><i class="fa fa-reply"></i></a></span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
		        	<h3 class="panel-title"><i class="fa fa-pencil"></i> Edit Category</h3>
		      	</div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('route' => array('product.category.update', $category->id), 'method'=> 'PUT', 'class' => 'form-horizontal')) }}

                        	<div class="form-group required @if ($errors->has('category_name')) has-error @endif">
							{{ Form::label('category_name', 'Category Name *', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-10">
									{{ Form::text('category_name', $category->category_name, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
									{{ $errors->first('category_name', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<div class="form-group @if ($errors->has('category_desc')) has-error @endif">
							{{ Form::label('category_desc', 'Description', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-10">
									{{ Form::textarea('category_descriptions', $category->category_descriptions, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
									{{ $errors->first('category_desc', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('category_parent', 'Parent', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-10">
									{{ Form::select('category_parent', array('0' => 'None') + $category_options, $category->category_parent, array('class'=> 'form-control')) }}
									
								</div>
							</div>

							<div class="form-group">
						     	<div class="col-lg-10 col-lg-offset-2">
						     		<a class="btn btn-default" href="{{ URL::route('product.category.index') }}"><i class="fa fa-reply"></i> Cancel</a>
						     		<button type="submit" value="Save" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
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