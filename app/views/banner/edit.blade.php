@extends('layouts.master')

@section('title') Banner @stop

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
            <h1 class="page-header">Banner Management</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

	{{ Form::open(array('url' => 'banner/update/' . $banner->id , 'class' => 'form-horizontal', 'files' => true)) }}

	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title"><i class="fa fa-pencil"></i> Edit Banner Details</h2>
        </div>
		<div class="panel-body">
			<div class="col-lg-12 ">
				<div class='form-group'>
				{{ Form::label('id', 'ID', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-6">
					{{ Form::text('id', $banner->id, array('class' => 'form-control', 'disabled') ) }}
					</div>
				</div>
				<div class='form-group'>
				{{ Form::label('insert_date', 'Created at', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-6">
					{{ Form::text('insert_date', $banner->insert_date, array('class' => 'form-control', 'disabled') ) }}
					</div>
				</div>
				<div class="form-group">
					{{ Form::label('banner_region', 'Region', ['class' => 'col-lg-2 control-label']) }}
						<div class="col-lg-3">
							<select class="form-control" id="region_country_id" name="region_country_id">
                                <?php foreach ($countries as $key => $value) { ?>
                                    <option value="<?php echo $value->id; ?>" <?php if($banner->region_country_id == $value->id){ echo "selected";} ?>><?php echo $value->name;?></option>
                                <?php } ?>
                            </select>
                            <select class="form-control" id="region_id" name="region_id" style="margin-top: 10px;">
                            	<?php if(Session::get('branch_access') != 1){?>
                            	<option value="">All region</option>
                                <?php } ?>
                                <?php foreach ($regions as $key => $value)  { ?>
                                    <option value="<?php echo $value->id; ?>" <?php if($value->id == $banner->region_id) { echo "selected";} ?>><?php echo $value->region; ?></option>
                                <?php  } ?>
                            </select>
						</div>
                        <div class="col-lg-3">
								
						</div>
				</div>
				<div class='form-group'>
				{{ Form::label('qrcode', 'QR Code', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-6">
					{{ Form::text('qrcode', $banner->qrcode, array('placeholder' => 'e.g. JC2110 or JC1000, JC2000, JC3000', 'class' => 'form-control') ) }}
					<p class="text-info">* (blank): It will shows the Banner Image. <br>* Otherwise, it will shows the list of products based on the numbers of QR Code entered.</p>
					</div>
				</div>			

				<div class="form-group">
				{{ Form::label('category_id', 'Charity Category', array('class'=> 'col-lg-2 control-label')) }}
					<div class="col-lg-3">
						{{ Form::select('category_id', $category, $banner->category_id, array('class'=> 'form-control')) }}
						<p class="text-info">* (blank): General Banner for Home Page.<br>* Otherwise, Banner in Category.</p>
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
	                	<?php $count = count($banner_images); ?>
	                	@foreach ($languages as $code => $name)
	                		<?php $loaded = 0; ?>
	                		@if($loaded == 0)
			                	@foreach ($banner_images as $banner)
				                	@if ($banner->language == $code && $banner->device == "phone")
				                		<?php $loaded = 1; ?>
					                	<tr>
					                		<td>{{ Form::label('banner', $name, array('class' => 'col-lg-2 control-label')) }}</td>
					                		<td>
									            <div class="fileinput fileinput-new" data-provides="fileinput">
									            	<div class="fileinput-preview thumbnail" style="max-width: 200px; max-height: 320px; line-height: 20px;">
									            		{{ $name }} -  {{ $banner -> file_name }} <br>
									            		@if(file_exists('./images/banner/' . $code .'/' .$banner->file_name))
															{{ HTML::image('images/banner/' . $code. '/' . $banner->file_name) }}
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
												 		<input type="file" name="banner_{{$code}}" id="banner_{{$code}}" />
												 	</span>
												  	<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i> Remove</a>
												  </div>
												</div>
									        </td>
					                		<td>
					                			<div class="fileinput fileinput-new" data-provides="fileinput">
													<div class="fileinput-preview thumbnail" style="max-width: 200px; max-height: 150px;">
														{{ $name }} -  {{ $banner -> thumb_name }} <br>
														@if(isset($banner->thumb_name))
															@if(file_exists('./images/banner/thumbs/'. $code . '/' . $banner->thumb_name))
																{{ HTML::image('images/banner/thumbs/'. $code . '/' . $banner->thumb_name) }}
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
											                    <input type="file" name="banner_thumb_{{$code}}" id="banner_thumb_{{$code}}" />
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
		                		<td>{{ Form::label('banner', $name, array('class' => 'col-lg-2 control-label')) }}</td>
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
										 		<input type="file" name="banner_{{$code}}" id="banner_{{$code}}" />
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
							                    <input type="file" name="banner_thumb_{{$code}}" id="banner_thumb_{{$code}}" />
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
	                	<?php $count = count($banner_images); ?>
	                	@foreach ($languages as $code => $name)
	                		<?php $loaded = 0; ?>
	                		@if($loaded == 0)
			                	@foreach ($banner_images as $banner)
				                	@if ($banner->language == $code && $banner->device == "tablet")
				                		<?php $loaded = 1; ?>
					                	<tr>
					                		<td>{{ Form::label('banner', $name, array('class' => 'col-lg-2 control-label')) }}</td>
					                		<td>
									            <div class="fileinput fileinput-new" data-provides="fileinput">
									            	<div class="fileinput-preview thumbnail" style="max-width: 200px; max-height: 320px; line-height: 20px;">
									            		{{ $name }} -  {{ $banner -> file_name }} <br>
									            		@if(file_exists('./images/banner_tab/' . $code .'/' .$banner->file_name))
															{{ HTML::image('images/banner_tab/' . $code. '/' . $banner->file_name.'?'.uniqid()) }}
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
												 		<input type="file" name="banner_tab_{{$code}}" id="banner_tab_{{$code}}" />
												 	</span>
												  	<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i> Remove</a>
												  </div>
												</div>
									        </td>
					                		<td>
					                			<div class="fileinput fileinput-new" data-provides="fileinput">
													<div class="fileinput-preview thumbnail" style="max-width: 200px; max-height: 150px;">
														{{ $name }} -  {{ $banner -> thumb_name }} <br>
														@if(isset($banner->thumb_name))
															@if(file_exists('./images/banner_tab/thumbs/'. $code . '/' . $banner->thumb_name))
																{{ HTML::image('images/banner_tab/thumbs/'. $code . '/' . $banner->thumb_name.'?'.uniqid()) }}
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
											                    <input type="file" name="banner_tab_thumb_{{$code}}" id="banner_tab_thumb_{{$code}}" />
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
		                		<td>{{ Form::label('banner', $name, array('class' => 'col-lg-2 control-label')) }}</td>
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
										 		<input type="file" name="banner_tab_{{$code}}" id="banner_tab_{{$code}}" />
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
							                    <input type="file" name="banner_tab_thumb_{{$code}}" id="banner_tab_thumb_{{$code}}" />
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
$(document).ready(function() {
    
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
});
</script>

@stop

