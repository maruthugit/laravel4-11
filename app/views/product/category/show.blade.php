@extends('layouts.master')

@section('content')
<div id="page-wrapper">
	<div class="bs-docs-section">
		<div class="row">
			<div class="col-lg-12">
				<div class="page-header">
	            	<h1>Product Category</h1>
	            	<h3>Viewing: {{ $category->category_name }}<a href="{{ URL::route('product.category.index') }}" class="btn btn-primary btn-sm pull-right" type="button">View All</a></h3>
	            </div>
				<div class="well bs-component">
					<form class="form-horizontal">
						<div class="form-group">
							<label class="col-sm-2 control-label">Category Name:</label>
							<div class="col-lg-10">
								<p class="form-control-static">{{ $category->category_name }}</p>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Description:</label>
							<div class="col-lg-10">
								<p class="form-control-static">{{ $category->category_desc }}</p>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Status:</label>
							<div class="col-lg-10">
								<p class="form-control-static">{{ $category->category_status }}</p>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Image Path:</label>
							<div class="col-lg-10">
								<p class="form-control-static">{{ $category->category_image }}</p>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Created:</label>
							<div class="col-lg-10">
								<p class="form-control-static">{{ $category->created_at }}</p>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Last Update:</label>
							<div class="col-lg-10">
								<p class="form-control-static">{{ $category->updated_at }}</p>
							</div>
						</div>

						<div class="form-group">
					      <div class="col-lg-10 col-lg-offset-2">
					        {{ Form::submit('Update Category', array('class' => 'btn btn-primary')) }}
					      </div>
					    </div>
					</form>
				</div>
			</div>
		</div>
	</div>
@stop