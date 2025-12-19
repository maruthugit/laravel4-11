@extends('layouts.master')

@section('title') Latest News @stop

@section('content')

<div id="page-wrapper">
	@if ($errors->any())
		{{ implode('', $errors->all('<div class=\'bg-danger alert\'>:message</div>')) }}
	@endif

	@if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ $message }}
        </div>
    @endif

	<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Latest News Management</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

	{{ Form::open(array('url' => 'latestnews/update/' . $news->id , 'class' => 'form-horizontal', 'files' => true)) }}

	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title"><i class="fa fa-pencil"></i> Edit Latest News Details</h2>
        </div>
		<div class="panel-body">
			<div class="col-lg-12 ">
				<div class='form-group'>
				{{ Form::label('id', 'ID', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-6">
					{{ Form::text('id', $news->id, array('class' => 'form-control', 'disabled') ) }}
					</div>
				</div>
				<div class='form-group'>
				{{ Form::label('insert_date', 'Created at', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-6">
					{{ Form::text('insert_date', $news->insert_date, array('class' => 'form-control', 'disabled') ) }}
					</div>
				</div>
				<div class='form-group'>
				{{ Form::label('qrcode', 'QR Code', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-6">
					{{ Form::text('qrcode', $news->qrcode, array('placeholder' => 'e.g. JC2110 or JC1000, JC2000, JC3000', 'class' => 'form-control') ) }}
					<p class="text-info">* (blank): It will shows the Latest News Image. <br>* Otherwise, it will shows the list of products based on the numbers of QR Code entered.</p>
					</div>
				</div>
				<div class='form-group'>
				{{ Form::label('title', 'Title', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-6">
					{{ Form::text('title', $news->title, array('class'=> 'form-control')) }}
					</div>
				</div>
				<div class='form-group'>
				{{ Form::label('description', 'Description', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-6">
					{{ Form::textarea('description', $news->description, array('class'=> 'form-control')) }}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title"><i class="fa fa-image"></i> Phone Images</h2>
        </div>
		<div class="panel-body">
			<div class="col-lg-12 table-responsive">
				<table class="table table-hover" style="text-align:center;">
					<thead>
                        <tr>
                            <th>Languages</th>
                            <th style="text-align:center;">Actual Images</th>
                            <th style="text-align:center;">Thumbnails</th>
                        </tr>
                    </thead>
	                <tbody>
	                	<?php $count = count($jocom_latest_news_images); ?>
	                	@foreach ($languages as $code => $name)
	                		<?php $loaded = 0; ?>
	                		@if($loaded == 0)
			                	@foreach ($jocom_latest_news_images as $news)
				                	@if ($news->language == $code && $news->device == "phone")
				                		<?php $loaded = 1; ?>
					                	<tr>
					                		<td>{{ Form::label('latestnews', $name, array('class' => 'col-lg-2 control-label')) }}</td>
					                		<td>
									            <div class="fileinput fileinput-new" data-provides="fileinput">
									            	<div class="fileinput-preview thumbnail" style="max-width: 200px; max-height: 320px; line-height: 20px;">
									            		{{ $name }} -  {{ $news -> file_name }} <br>
									            		@if(file_exists('./images/latest_news/' . $code .'/' .$news->file_name))
															{{ HTML::image('images/latest_news/' . $code. '/' . $news->file_name) }}
														@else
															file not found!
															{{ HTML::image('media/no_images.jpg') }}
														@endif
									            	</div>
									            	<div>
												  	<div style="color:#428bca"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span><br><br></div>
												 	<span class="btn btn-default btn-file">
												 		<span class="fileinput-new"><i class="fa fa-folder-open"></i> Browse</span>
												 		<span class="fileinput-exists"><i class="fa fa-folder-open"></i> Change</span>
												 		<input type="file" name="news_{{$code}}" id="news_{{$code}}" />
												 	</span>
												  	<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i> Remove</a>
												  </div>
												</div>
									        </td>
					                		<td>
					                			<div class="fileinput fileinput-new" data-provides="fileinput">
													<div class="fileinput-preview thumbnail" style="max-width: 200px; max-height: 150px;">
														{{ $name }} -  {{ $news -> thumb_name }} <br>
														@if(isset($news->thumb_name))
															@if(file_exists('./images/latest_news/thumbs/'. $code . '/' . $news->thumb_name))
																{{ HTML::image('images/latest_news/thumbs/'. $code . '/' . $news->thumb_name) }}
															@else
																file not found! 
																{{ HTML::image('media/no_images.jpg') }}
															@endif
														@else
															{{ HTML::image('media/no_images.jpg') }}
														@endif
													</div>
											            <div>
											            	<div style="color:#428bca"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span><br><br>
											            		
											            	</div>

											                <span class="btn btn-default btn-file">
											                    <span class="fileinput-new"><i class="fa fa-folder-open"></i> Browse</span>
											                    <span class="fileinput-exists"><i class="fa fa-folder-open"></i> Change</span>
											                    <input type="file" name="news_thumb_{{$code}}" id="news_thumb_{{$code}}" />
											                </span>
											                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i> Remove</a>
											            </div>
													</div>
													
												</div>
					                		</td>
					                	</tr>
			                		@endif
			                	@endforeach
		                	@endif
	                		@if ($loaded == 0)
							<tr>
		                		<td>{{ Form::label('latestnews', $name, array('class' => 'col-lg-2 control-label')) }}</td>
		                		<td>
						            <div class="fileinput fileinput-new" data-provides="fileinput">
						            	<div class="fileinput-preview thumbnail" style="max-width: 200px; max-height: 320px; line-height: 20px;">
						            		{{ HTML::image('media/no_images.jpg') }}
						            	</div>

						            	<div>
										  	<div style="color:#428bca"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span><br><br></div>
										 	<span class="btn btn-default btn-file">
										 		<span class="fileinput-new"><i class="fa fa-folder-open"></i> Browse</span>
										 		<span class="fileinput-exists"><i class="fa fa-folder-open"></i> Change</span>
										 		<input type="file" name="news_{{$code}}" id="news_{{$code}}" />
										 	</span>
										  	<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i> Remove</a>
									  	</div>
									</div>
						        </td>
		                		<td>
		                			<div class="fileinput fileinput-new" data-provides="fileinput">
										<div class="fileinput-preview thumbnail" style="max-width: 200px; max-height: 150px;">
											{{ HTML::image('media/no_images.jpg') }}
										</div>
							            <div>
							            	<div style="color:#428bca"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span><br><br></div>
							                <span class="btn btn-default btn-file" onchange="get_file_info(this)">
							                    <span class="fileinput-new"><i class="fa fa-folder-open"></i> Browse</span>
							                    <span class="fileinput-exists"><i class="fa fa-folder-open"></i> Change</span>
							                    <input type="file" name="news_thumb_{{$code}}" id="news_thumb_{{$code}}" />
							                </span>
							                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i> Remove</a>
							            </div>
									</div>
		                		</td>
		                	</tr>
		                	
							@endif	                	
	                	@endforeach	
	                </tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title"><i class="fa fa-image"></i> Tablet Images</h2>
        </div>
		<div class="panel-body">
			<div class="col-lg-12 table-responsive">
				<table class="table table-hover" style="text-align:center;">
					<thead>
                        <tr>
                            <th>Languages</th>
                            <th style="text-align:center;">Actual Images</th>
                            <th style="text-align:center;">Thumbnails</th>
                        </tr>
                    </thead>
	                <tbody>
	                	<?php $count = count($jocom_latest_news_images); ?>
	                	@foreach ($languages as $code => $name)
	                		<?php $loaded = 0; ?>
	                		@if($loaded == 0)
			                	@foreach ($jocom_latest_news_images as $news)
				                	@if ($news->language == $code && $news->device == "tablet")
				                		<?php $loaded = 1; ?>
					                	<tr>
					                		<td>{{ Form::label('latestnews', $name, array('class' => 'col-lg-2 control-label')) }}</td>
					                		<td>
									            <div class="fileinput fileinput-new" data-provides="fileinput">
									            	<div class="fileinput-preview thumbnail" style="max-width: 200px; max-height: 320px; line-height: 20px;">
									            		{{ $name }} -  {{ $news -> file_name }} <br>
									            		@if(file_exists('./images/latest_news_tab/' . $code .'/' .$news->file_name))
															{{ HTML::image('images/latest_news_tab/' . $code. '/' . $news->file_name) }}
														@else
															file not found!
															{{ HTML::image('media/no_images.jpg') }}
														@endif
									            	</div>
									            	<div>
												  	<div style="color:#428bca"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span><br><br></div>
												 	<span class="btn btn-default btn-file">
												 		<span class="fileinput-new"><i class="fa fa-folder-open"></i> Browse</span>
												 		<span class="fileinput-exists"><i class="fa fa-folder-open"></i> Change</span>
												 		<input type="file" name="news_tab_{{$code}}" id="news_tab_{{$code}}" />
												 	</span>
												  	<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i> Remove</a>
												  </div>
												</div>
									        </td>
					                		<td>
					                			<div class="fileinput fileinput-new" data-provides="fileinput">
													<div class="fileinput-preview thumbnail" style="max-width: 200px; max-height: 150px;">
														{{ $name }} -  {{ $news -> thumb_name }} <br>
														@if(isset($news->thumb_name))
															@if(file_exists('./images/latest_news_tab/thumbs/'. $code . '/' . $news->thumb_name))
																{{ HTML::image('images/latest_news_tab/thumbs/'. $code . '/' . $news->thumb_name) }}
															@else
																file not found! 
																{{ HTML::image('media/no_images.jpg') }}
															@endif
														@else
															{{ HTML::image('media/no_images.jpg') }}
														@endif
													</div>
											            <div>
											            	<div style="color:#428bca"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span><br><br>
											            		
											            	</div>

											                <span class="btn btn-default btn-file">
											                    <span class="fileinput-new"><i class="fa fa-folder-open"></i> Browse</span>
											                    <span class="fileinput-exists"><i class="fa fa-folder-open"></i> Change</span>
											                    <input type="file" name="news_tab_thumb_{{$code}}" id="news_tab_thumb_{{$code}}" />
											                </span>
											                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i> Remove</a>
											            </div>
													</div>
													
												</div>
					                		</td>
					                	</tr>
			                		@endif
			                	@endforeach
		                	@endif
	                		@if ($loaded == 0)
							<tr>
		                		<td>{{ Form::label('latestnews', $name, array('class' => 'col-lg-2 control-label')) }}</td>
		                		<td>
						            <div class="fileinput fileinput-new" data-provides="fileinput">
						            	<div class="fileinput-preview thumbnail" style="max-width: 200px; max-height: 320px; line-height: 20px;">
						            		{{ HTML::image('media/no_images.jpg') }}
						            	</div>

						            	<div>
										  	<div style="color:#428bca"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span><br><br></div>
										 	<span class="btn btn-default btn-file">
										 		<span class="fileinput-new"><i class="fa fa-folder-open"></i> Browse</span>
										 		<span class="fileinput-exists"><i class="fa fa-folder-open"></i> Change</span>
										 		<input type="file" name="news_tab_{{$code}}" id="news_tab_{{$code}}" />
										 	</span>
										  	<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i> Remove</a>
									  	</div>
									</div>
						        </td>
		                		<td>
		                			<div class="fileinput fileinput-new" data-provides="fileinput">
										<div class="fileinput-preview thumbnail" style="max-width: 200px; max-height: 150px;">
											{{ HTML::image('media/no_images.jpg') }}
										</div>
							            <div>
							            	<div style="color:#428bca"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span><br><br></div>
							                <span class="btn btn-default btn-file" onchange="get_file_info(this)">
							                    <span class="fileinput-new"><i class="fa fa-folder-open"></i> Browse</span>
							                    <span class="fileinput-exists"><i class="fa fa-folder-open"></i> Change</span>
							                    <input type="file" name="news_tab_thumb_{{$code}}" id="news_tab_thumb_{{$code}}" />
							                </span>
							                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i> Remove</a>
							            </div>
									</div>
		                		</td>
		                	</tr>
		                	
							@endif	                	
	                	@endforeach	
	                </tbody>
				</table>
			</div>
		</div>
	</div>

	<div class='form-group'>
		<div class="col-lg-10">
			{{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
			{{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
		</div>
	</div>

	{{ Form::close() }}

</div>

@stop


