@extends('layouts.master')

@section('title') Category @stop

@section('content')
<head>

<style>

ul, #myUL {
  list-style-type: none;
}

#myUL {
  margin: 0;
  padding: 0;
} 

.caret-category {
  border-top: none;
  cursor: pointer;
  user-select: none;
}

.caret-category::before {
  content: "\25B6";
  color: black;
  margin-right: 6px;
  display: inline-block;
}

.caret-category-down::before {
  -ms-transform: rotate(90deg); /* IE 9 */
  -webkit-transform: rotate(90deg); /* Safari */'
  transform: rotate(90deg);  
}
.dot {    
	height: 13px;
    width: 12px;
	background-color: white;
	border: solid 2px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 6px;
}

.nested {
  display: none;
}

.active {
  display: block;
}

</style>
</head>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Categories <span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}product/category"><i class="fa fa-refresh"></i></a>
                @if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 5, 'AND'))
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="/product/category/create"><i class="fa fa-plus"></i></a>
                @endif
                </span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
	        @if (Session::has('message'))
	            <div class="alert alert-success">
	                <i class="fa fa-thumbs-up"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">&times;</button>
	            </div>
	        @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Category List</h3>
                </div>
                <div class="panel-body" id='div-cat'>
					<ul id="myUL" >
						<?php $parent = [0 => 0];?>
						@foreach ($categoriesOptions as $key => $category)
							<?php
								if ($category['category_parent'] == 0){
									echo isset($category['children']) ? '<li><span class="caret-category"></span>' : '<li><span class="dot"></span>';
									echo '<a href='. url('product/category/edit/'.$category['id']) .'>'.$category['category_name'].' [ID:'.$category['id'].']</a>';
									echo $category['permission'] == 1 ? '<span class="label label-success">Private</span>' : '';
									echo $category['status'] == 0 ? '&nbsp;<span class="label label-warning">Inactive</span>' : '';
									if (isset($category['children'])) {
										echo '<ul class="nested">';
										foreach ($category['children'] as $childrens){
											echo isset($childrens['children']) ? '<li><span class="caret-category"></span>' : '<li><span class="dot"></span>';
											echo '<a href='. url('product/category/edit/'.$childrens['id']) .'>'.$childrens['category_name'].' [ID:'.$childrens['id'].']</a>';
											echo $childrens['permission'] == 1 ? '<span class="label label-success">Private</span>' : '';
											echo $childrens['status'] == 0 ? '&nbsp;<span class="label label-warning">Inactive</span>' : '';
											if (isset($childrens['children'])) {
												echo '<ul class="nested">';
												foreach ($childrens['children'] as $children){
													echo isset($children['children']) ? '<li><span class="caret-category"></span>' : '<li><span class="dot"></span>';
													echo '<a href='. url('product/category/edit/'.$children['id']) .'>'.$children['category_name'].' [ID:'.$children['id'].']</a>';
													echo $children['permission'] == 1 ? '<span class="label label-success">Private</span>' : '';
													echo $children['status'] == 0 ? '&nbsp;<span class="label label-warning">Inactive</span>' : '';
													if (isset($children['children'])) {
														echo '<ul class="nested">';
														foreach ($children['children'] as $child){
															echo isset($child['children']) ? '<li><span class="caret-category"></span>' : '<li><span class="dot"></span>';
															echo '<a href='. url('product/category/edit/'.$child['id']) .'>'.$child['category_name'].' [ID:'.$child['id'].']</a>';
															echo $child['permission'] == 1 ? '<span class="label label-success">Private</span>' : '';
															echo $child['status'] == 0 ? '&nbsp;<span class="label label-warning">Inactive</span>' : '';
														}
														echo '</ul>';
													}
												}
												echo '</ul>';
											}
											echo '</li>';
										}
										echo '</ul>';
									}
								}
								echo '</li>';				
							?>
						@endforeach
					</ul>
				</div>
            </div>
        </div>
    </div>
</div>
<script>

	var toggler = document.getElementsByClassName("caret-category");
	var i;
	console.log(toggler);

	for (i = 0; i < toggler.length; i++) {
		toggler[i].addEventListener("click", function() {
			this.parentElement.querySelector(".nested").classList.toggle("active");
			this.classList.toggle("caret-category-down");
		});
	}
	
</script>

@stop

