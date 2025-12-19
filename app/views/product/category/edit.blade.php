@extends('layouts.master')

@section('title') Category @stop

@section('content')
<div id="page-wrapper">
	<style>
		.preview_img{max-width: 100%; height: 100%; display: block;}
		.p_img{display: block;}
		.p_img .wrap-img {position: relative;}
		.p_img .wrap-img .btn-clear{ position: absolute; top: 0px; right: 0px; display: none; }
		.p_img .wrap-img .btn-clear.active{display: block;}
	</style>
	<h1 class="page-header">Categories</h1>
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
					<h3 class="panel-title"><i class="fa fa-pencil"></i> Edit Category</h3>
				</div>
				<div class="panel-body">
					<div class="col-lg-12">
						{{ Form::open(array('url' => array('product/category/update', $category->id), 'method'=> 'PUT', 'class' => 'form-horizontal', 'files'=> true)) }}
							<div class="form-group">
							{{ Form::label('id_number', 'ID Number', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-3">
									<p class="form-control-static">{{$category->id}}</p>
								</div>
							</div>

							<div class="form-group required @if ($errors->has('category_name')) has-error @endif">
							{{ Form::label('category_name', 'Category Name *', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-10">
									{{ Form::text('category_name', $category->category_name, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
									{{ $errors->first('category_name', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<div class="form-group required @if ($errors->has('category_name_cn')) has-error @endif">
							{{ Form::label('category_name_cn', 'Category Name (Chinese)', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-10">
									{{ Form::text('category_name_cn', $category->category_name_cn, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
									{{ $errors->first('category_name_cn', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<div class="form-group required @if ($errors->has('category_name_my')) has-error @endif">
							{{ Form::label('category_name_my', 'Category Name (Bahasa)', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-10">
									{{ Form::text('category_name_my', $category->category_name_my, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
									{{ $errors->first('category_name_my', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<hr />

							<div class="form-group @if ($errors->has('category_desc')) has-error @endif">
								{{ Form::label('category_desc', 'Description', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-10">
									{{ Form::textarea('category_descriptions', $category->category_descriptions, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
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
										<option value="<?php echo $cl["id"] ?>" @if ($category->category_parent == $cl["id"]) selected @endif><?php echo $cl["category_name"].' [ID: '.$cl["id"].']'; ?></option>
									<?php } ?>
									</select>
									{{ $errors->first('category_parent', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<hr />

							<div class="form-group @if ($errors->has('image')) has-error @endif">
								{{ Form::label('image_path', 'Image Thumb *', ['class'=> 'col-lg-2 control-label']) }}
								<div class="col-lg-4">
									<label class="p_img">
										<img class="preview_img" data-bkpsrc="/images/{{ $category->category_img ? 'category/thumbs/' . $category->category_img : 'data/thumbs/noimage.png' }}">
										<div class="wrap-img">
											<input id="image-input" type="file" name="image" class="form-control" onchange="checkFileDetails(this, 'thumb')" accept=".png, .jpg, .jpeg">
											<div class="btn btn-default btn-clear{{ $product->{'img_' . $i} != '' ? ' active' : '' }}"><i class="glyphicon glyphicon-trash"></i> Remove</div>
										</div>
										{{ ($category->category_img ? Form::hidden('current_image', $category->category_img, ['id' => 'current_image']) : '') }}
									</label>
									{{ $errors->first('image', '<p class="help-block">:message</p>') }}
									<span class="help-block">Dimension: 320(W) x 320(H), Size: 100KB</span>
								</div>

								{{ Form::label('image_path', 'Image Banner *', ['class'=> 'col-lg-2 control-label']) }}
								<div class="col-lg-4">
									<label class="p_img">
										<img class="preview_img" data-bkpsrc="/images/{{ $category->category_img_banner ? 'category/' . $category->category_img_banner : 'data/thumbs/noimage.png' }}">
										<div class="wrap-img">
											<input id="image-input-banner" type="file" name="image_banner" class="form-control" onchange="checkFileDetails(this, 'mobile_banner')" accept=".png, .jpg, .jpeg">
											<div class="btn btn-default btn-clear{{ $product->{'img_' . $i} != '' ? ' active' : '' }}"><i class="glyphicon glyphicon-trash"></i> Remove</div>
										</div>
										{{ ($category->category_img_banner ? Form::hidden('current_image_banner', $category->category_img_banner, ['id' => 'current_image_banner']) : '') }}
									</label>
									{{ $errors->first('image_banner', '<p class="help-block">:message</p>') }}
									<span class="help-block">Dimension: 645(W) x 405(H), Size: 100KB</span>
								</div>

								{{ Form::label('image_path', 'Web Banner *', ['class'=> 'col-lg-2 control-label']) }}
								<div class="col-lg-4">
									<label class="p_img">
										<img class="preview_img" data-bkpsrc="/images/{{ $category->category_web_banner ? 'category/' . $category->category_web_banner : 'data/thumbs/noimage.png' }}">
										<div class="wrap-img">
											<input id="image-input-banner" type="file" name="web_banner" class="form-control" onchange="checkFileDetails(this, 'web_banner')" accept=".png, .jpg, .jpeg">
											<div class="btn btn-default btn-clear{{ $product->{'img_' . $i} != '' ? ' active' : '' }}"><i class="glyphicon glyphicon-trash"></i> Remove</div>
										</div>
										{{ ($category->category_web_banner ? Form::hidden('current_image_banner', $category->category_web_banner, ['id' => 'current_image_banner']) : '') }}
									</label>
									{{ $errors->first('image_banner', '<p class="help-block">:message</p>') }}
									<span class="help-block">Dimension: 1200(W) x 500(H), Size: 200KB</span>
								</div>
							</div>

							<hr />

							<div class="form-group @if ($errors->has('status')) has-error @endif">
								{{ Form::label('status', 'Category Status', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-3">
									@if (in_array($category->status, [0, 1]))
										{{ Form::select('status', array('0' => 'Inactive', '1' => 'Active'), $category->status, array('class'=> 'form-control')) }}
										{{ $errors->first('status', '<p class="help-block">:message</p>') }}
									@else
										{{ Form::select('status', array('2' => 'Delete'), $category->status, array('class'=> 'form-control')) }}
										{{ $errors->first('status', '<p class="help-block">:message</p>') }}
									@endif
								</div>
								@if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 15, 'AND'))
								<div class="col-lg-3">
									@if ($category->status == 2)
										<a class="btn btn-danger" style="display: none;" href="{{ asset('/') . 'product/category/deletecategory/' . $category->id }}">Delete</a>
									@else
										<a class="btn btn-danger" href="{{ asset('/') . 'product/category/deletecategory/'.$category->id }}" onclick="return confirm('Are you sure to delete?')">Delete</a>
									@endif
								</div>
								@endif
							</div>

							<hr />

							<div class="form-group @if ($errors->has('permission')) has-error @endif">
								{{ Form::label('permission', 'Category Permission', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-3">
									{{ Form::select('permission', $permissions, $category->permission, array('class'=> 'form-control')) }}
									{{ $errors->first('permission', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<hr />

							<div class="form-group @if ($errors->has('permission')) has-error @endif">
								{{ Form::label('weight', 'Category Weight', array('class' => 'col-lg-2 control-label')) }}
								<div class="col-lg-3">
									{{ Form::text('weight', $category->weight, array('class'=> 'form-control')) }}
									{{ $errors->first('weight', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<hr />

							<div class="form-group @if ($errors->has('charity')) has-error @endif">
							{{ Form::label('charity', 'Charity Parent', array('class'=> 'col-lg-2 control-label')) }}
								<div class="col-lg-3">
									{{ Form::select('charity', $charity, $category->charity, array('class'=> 'form-control')) }}
									{{ $errors->first('charity', '<p class="help-block">:message</p>') }}
								</div>
							</div>

							<hr />

							<div class="form-group">
								<div class="col-lg-10 col-lg-offset-2">
									<input class="btn btn-default" data-toggle="tooltip" type="reset" value="Reset">
									@foreach ($categoryChilds as $categoryChild)
										{{ Form::hidden('sublist[]', $categoryChild)}}
									@endforeach
									@if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 3, 'AND'))
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
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-pencil"></i> Edit Charity Info</h3>
				</div>
				<div class="panel-body">
					<div class="col-lg-12">
						{{ Form::open(array('url' => array('product/category/charityinfo', $category->id), 'method'=> 'PUT', 'class' => 'form-horizontal', 'files'=> true)) }}
							<div class='form-group'>
								{{ Form::label('id', 'ID', array('class' => 'col-lg-2 control-label')) }}			                    
								{{Form::input('hidden', 'id', $charityInfo->id)}}
								<div class="col-lg-3">
									<p class="form-control-static">{{$charityInfo->id}}</p>
								</div>
							</div>

							<div class='form-group'>
								{{ Form::label('name', 'Name *', array('class' => 'col-lg-2 control-label')) }}
								<div class="col-lg-3">
									{{ Form::text('name', $charityInfo->name, ['required'=>'required', 'placeholder' => 'Name', 'class' => 'form-control']) }}
								</div>
							</div>

							<div class='form-group'>
								{{ Form::label('contactno', 'Contact Number *', array('class' => 'col-lg-2 control-label')) }}
								<div class="col-lg-3">
									{{ Form::text('contactno', $charityInfo->contactno, ['required'=>'required', 'placeholder' => 'Contact Number', 'class' => 'form-control']) }}
								</div>
											<div class="col-lg-1">
												<p class="help-block" ><a style="cursor:pointer;"><span id="charity-toggler" class="showmore">view more <i class="fa fa-caret-down" ></i></span></a></p>
							</div>
							</div>

						<section id="full-charity-form" style="display: none;">
							<div class='form-group'>
								{{ Form::label('address', 'Delivery Address *', array('class'=> 'col-lg-2 control-label')) }}
								 <div class="col-lg-4">
									{{ Form::text('address1', $charityInfo->address1, array('required'=>'required', 'class'=> 'form-control')) }}<p></p>
									{{ Form::text('address2', $charityInfo->address2, array('class'=> 'form-control')) }}
								</div>
							</div>

							<div class='form-group'>
								{{ Form::label('postcode', 'Postcode *', array('class' => 'col-lg-2 control-label')) }}
								<div class="col-lg-3">
									{{ Form::text('postcode', $charityInfo->postcode, ['required'=>'required', 'class' => 'form-control']) }}
								</div>
							</div>

							<div class='form-group'>
								{{ Form::label('country', 'Country *', array('class' => 'col-lg-2 control-label')) }}
								<div class="col-lg-3">
									<select class='form-control' name='country' id='country'>
										<option value=""> - </option>
										@foreach ($countries as $country)
											<option value='{{ $country->id }}' <?php if ($charityInfo->country == $country->id) echo "selected"; ?> >{{ $country->name }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class='form-group'>
								{{ Form::label('state', 'State *', array('class' => 'col-lg-2 control-label')) }}
								<div class="col-sm-3">
									<select class='form-control' name='state' id='state'>
										<option value=""> - </option>
										@foreach ($states as $state)
											<option value='{{ $state->id }}' <?php if ($charityInfo->state == $state->id) echo "selected"; ?> >{{ $state->name }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class='form-group'>
								{{ Form::label('city', 'City/Town *', array('class' => 'col-lg-2 control-label')) }}
								<div class="col-sm-3">
									<select class='form-control' name='city' id='city'>
										<option value=""> - </option>
										@foreach ($cities as $city)
											<option value='{{ $city->id }}' <?php if ($charityInfo->city == $city->id) echo "selected"; ?> >{{ $city->name }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class='form-group'>
								{{ Form::label('specialmsg', 'Special Message', array('class' => 'col-lg-2 control-label')) }}
								<div class="col-lg-4">
									{{ Form::textarea('specialmsg', $charityInfo->specialmsg, array('placeholder' => 'e.g. Send after 12pm', 'class'=> 'form-control')) }}
								</div>
							</div>

							<hr />

							<div class="form-group">
								{{ Form::label('image_path2', 'Image Phone *', array('class'=> 'col-lg-2 control-label' )) }}
								{{ Form::hidden('img_phone', $charityInfo->img_phone, array('id' => 'img_phone')) }}
								{{ Form::hidden('del_phone', $charityInfo->img_phone, array('id' => 'del_phone')) }}
								<div class="col-lg-4">
									<input id="image-input2" type="file" name="image2" class="form-control">
								</div>
								<p class="help-block">For best display result, width:640 and height:400</p>
							</div>

							<div class="form-group">
								{{ Form::label('image_path3', 'Image Tablet *', array('class'=> 'col-lg-2 control-label' )) }}
								{{ Form::hidden('img_tablet', $charityInfo->img_tablet, array('id' => 'img_tablet')) }}
								{{ Form::hidden('del_tablet', $charityInfo->img_tablet, array('id' => 'del_tablet')) }}
								<div class="col-lg-4">
									<input id="image-input3" type="file" name="image3" class="form-control">
								</div>
								<p class="help-block">For best display result, width:1024 and height:358</p>
							</div>

							<hr />

							<div class="form-group">
								<div class="col-lg-10 col-lg-offset-2">
									<input class="btn btn-default" data-toggle="tooltip" type="reset" value="Reset">
									@if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 3, 'AND'))
										<button type="submit" value="Save" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
										@if ($charityInfo->id != NULL)
										{{ HTML::link(asset('/').'product/category/deletecharity/'.$charityInfo->id, "Delete", array('class'=>'btn btn-danger')) }}
										{{ HTML::link(asset('/').'product/category/charityproduct/'.$charityInfo->id, "+ Add Product", array('class'=>'btn btn-success')) }}
										@endif
									@endif
								</div>
							</div>
							</section>
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
	<script>
		// YH: check image size before submit
		function resetInput(target, preview_img, btn_clear, type){
			preview_img.src = preview_img.dataset.bkpsrc;
			btn_clear.className = 'btn btn-default btn-clear';

			var newInput = document.createElement("input");
			newInput.type = "file"; 
			newInput.id = target.id;
			newInput.name = target.name; 
			newInput.className = target.className; 
			newInput.style.cssText = target.style.cssText;
			newInput.setAttribute("onchange", 'checkFileDetails(this, \'' + type + '\')');
			newInput.setAttribute("accept", '.png, .jpg, .jpeg');
			target.parentNode.replaceChild(newInput, target);
		}

		function readImageFile(target, file, type) {
			var reader = new FileReader(); // CREATE AN NEW INSTANCE.

			reader.onload = function (e) {
				var img = new Image();
				img.src = e.target.result;

				img.onload = function () {
					var w = this.width;
					var h = this.height;
					var size_KBs = Math.round((file.size / 1024));

					var btn_clear = target.parentElement.parentElement.querySelector('.btn-clear');
					var preview_img = target.parentElement.parentElement.querySelector('img.preview_img');
					btn_clear.className = 'btn btn-default btn-clear active';
					preview_img.src = URL.createObjectURL(file);

					if(type === 'mobile_banner'){
						if(w != 645 || h != 405 || size_KBs > 100){
							bootbox.alert('Image file does not meet requirement, Pls upload image file size under 100KB and image dimensions exactly 645px width and 405px height.');
							resetInput(target, preview_img, btn_clear, type);
							return false; // stop futher proceed
						}
					}

					if(type === 'web_banner'){
						if(w != 1200 || h != 500 || size_KBs > 200){
							bootbox.alert('Image file does not meet requirement, Pls upload image file size under 200KB and image dimensions exactly 1200px width and 500px height.');
							resetInput(target, preview_img, btn_clear, type);
							return false; // stop futher proceed
						}
					}

					if(type === 'thumb'){
						console.log(w, h, size_KBs);
						if(w != 320 || h != 320 || size_KBs > 100){
							bootbox.alert('Image file does not meet requirement, Pls upload image file size under 100KB and image dimensions exactly 320px width and 320px height.');
							resetInput(target, preview_img, btn_clear, type);
							return false; // stop futher proceed
						}
					}
				}
			};
			reader.readAsDataURL(file);
		}

		function checkFileDetails(target, type) {
			if (target.files.length > 0) { // JS prefer do length check, 
				for (var i = 0; i <= target.files.length - 1; i++) {
					var fileName, fileExtension;

					// FILE NAME AND EXTENSION.
					fileName = target.files.item(i).name;
					fileExtension = fileName.replace(/^.*\./, '');

					if (['png', 'jpg', 'jpeg'].includes(fileExtension)) { // IF IS IMAGE FILE DO CHECK
						readImageFile(target, target.files.item(i), type);
					} else { // PRMOPT ERROR
						bootbox.alert('Only image file (.jpg, .jpeg, .png) allow to upload.');
						resetInput(target, target.parentElement.parentElement.querySelector('img.preview_img'), target.parentElement.parentElement.querySelector('.btn-clear'), type);
					}
				}
			}
		}

		$(document).on("click", ".btn-clear", function(e) {
			e.preventDefault();
			bootbox.confirm({
				title: "Remove Product Image",
				message: "Are you sure to remove these upload image?",
				callback: function(result) {
					if (result === true) {
						var target = e.target.parentElement.querySelectorAll('input[type="file"]')[0];
						resetInput(target, target.parentElement.parentElement.querySelector('img.preview_img'), e.target);
					}
				}
			});
		});

		$('img.preview_img').each(function(index) {
			$(this).attr('src', $(this).data('bkpsrc'));
		});
	</script>
@stop

@section('script')

	// Handle Toggle Charity form 

	$( "#charity-toggler" ).click(function() {
		if($(this).hasClass('showmore')){
			$("#full-charity-form").show();
			$(this).removeClass('showmore');
			$(this).addClass('showless');
			$(this).html('view less <i class="fa fa-caret-up" ></i>');
		}else{
			$("#full-charity-form").hide();
			$(this).removeClass('showless');
			$(this).addClass('showmore');
			$(this).html('view more <i class="fa fa-caret-down" ></i>');
		}
	});

	$("#image-input2").fileinput({
		initialPreview: [
			<?php
			// $msg = "FILE NOT EXISTS!";
			if(file_exists('./images/charity/' . $charityInfo->img_phone) && $charityInfo->img_phone != '') {
				// $msg 	= "FILE EXISTS!";
				$image2 	= $charityInfo->img_phone;
			} else $image2 = 'noimage.png';
			?>
			'<img src="/images/charity/{{ $image2 }}" class="file-preview-image">'
		],
		allowedFileExtensions: ["jpg", "png"],
		maxFileSize: 500,
		showUpload: false
	});

	$('#image-input2').on('filecleared', function(event) {
		$("#del_phone").val("");
	});

	$("#image-input3").fileinput({
		initialPreview: [
			<?php
			// $msg = "FILE NOT EXISTS!";
			if(file_exists('./images/charity/' . $charityInfo->img_tablet) && $charityInfo->img_tablet != '') {
				// $msg 	= "FILE EXISTS!";
				$image3 	= $charityInfo->img_tablet;
			} else $image3 = 'noimage.png';
			?>
			'<img src="/images/charity/{{ $image3 }}" class="file-preview-image">'
		],
		allowedFileExtensions: ["jpg", "png"],
		maxFileSize: 500,
		showUpload: false
	});

	$('#image-input3').on('filecleared', function(event) {
		$("#del_tablet").val("");
	});

	$(document).on("click", "#deleteItem", function(e) {
		var link = $(this).attr("href");
		e.preventDefault();
		bootbox.confirm({
			title: "Are you sure you want to delete the record?",
			message: '{{'
				<p>All their child subcategories will also be deleted.</p>
				<div class="panel panel-default">
					<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-sitemap"></i> Category List</h3></div>
					<div class="panel-body">' . $subCat . '</div>
				</div>
			'}}',
			callback: function(result) {
				if (result === true) {
					console.log("Delete product id");
					window.location = link;
				} else {
					console.log("IGNORE");
				}
			}
		});
	});
@stop