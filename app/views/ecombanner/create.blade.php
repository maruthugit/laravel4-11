@extends('layouts.master')

@section('title') Banner @stop

@section('content')

<div id="page-wrapper">
	@if ($errors->any())
		{{ implode('', $errors->all('<div class=\'bg-danger alert\'>:message</div>')) }}
	@endif

	<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">eCommunity Banner Management</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

	{{ Form::open(array('url' => 'ecombanner/store/' , 'class' => 'form-horizontal', 'files' => true)) }}

	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title"><i class="fa fa-pencil"></i> Banner Details</h2>
        </div>
		<div class="panel-body">
			<div class="col-lg-12 ">

				<div class='form-group'>
				{{ Form::label('qrcode', 'QR Code', array('class' => 'col-lg-1 control-label')) }}
					<div class="col-lg-6">
					{{ Form::text('qrcode', null, array('class' => 'form-control') ) }}
					<p class="text-info">* (blank): It will shows the Banner Image.<br>* Otherwise, it will shows the product based on the QR Code entered.</p>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title"><i class="fa fa-image"></i> Banner Image</h2>
        </div>
		<div class="panel-body">
			<div class="col-lg-12 table-responsive">
			    <p class="text-info">* Image size is 800x400.</p>
				<div class="fileinput fileinput-new" data-provides="fileinput">
	            	<div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 320px; line-height: 20px;"></div>
	            	<div>
				  	<div style="color:#428bca"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span><br><br></div>
				 	<span class="btn btn-default btn-file">
				 		<span class="fileinput-new"><i class="fa fa-folder-open"></i> Browse</span>
				 		<span class="fileinput-exists"><i class="fa fa-folder-open"></i> Change</span>
				 		<input type="file" name="banner_image" id="banner_image}" />
				 	</span>
				  	<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i> Remove</a>
				  </div>
				</div>
			</div>
		</div>
	</div>


	@if ( Permission::CheckAccessLevel(Session::get('role_id'), 5, 5, 'AND'))
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

@section('inputjs')
{{ HTML::script('js/fileinput.min.js') }}
<script>
</script>

@stop

