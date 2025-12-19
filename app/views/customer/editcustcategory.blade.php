@extends('layouts.master')

@section('title') Edit Customer Category @stop

@section('content')

<div id="page-wrapper">
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif
    @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
     @endif


    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"></i> Customer Management</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(array('url' => array('customer/updatecustcategory/' . $cust->id) , 'class' => 'form-horizontal', 'method' => 'PUT')) }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-lock"></i> Customer Category Details</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                    {{ Form::label('username', 'Username', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        <p class="form-control-static">{{$cust->username}}</p>
                        <!-- <p class="form-control-static">{{$cust->username}}</p> -->
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('fistname', 'First name', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    <!-- {{ Form::text('firstname', $cust->firstname, ['placeholder' => 'First Name', 'class' => 'form-control']) }} -->
                    <p class="form-control-static">{{$cust->firstname}}</p>
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('lastname', 'Last name', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    <!-- {{ Form::text('lastname', $cust->lastname, ['placeholder' => 'Last Name', 'class' => 'form-control']) }} -->
                    <p class="form-control-static">{{$cust->lastname}}</p>
                    </div>
                </div>
                <hr />
                <?php

                function fetchCategoryTree($parent = 0, $spacing = '', $user_tree_array = '') {

                    if (!is_array($user_tree_array))
                        $user_tree_array = array();

                    $query = DB::table('jocom_products_category')
                                ->select('jocom_products_category.id','jocom_products_category.category_name', 'jocom_products_category.category_parent', 'jocom_products_category.status', 'jocom_products_category.permission')
                                ->orderBy('category_name', 'ASC')
                                ->where('jocom_products_category.category_parent', '=', $parent)
                                ->where('jocom_products_category.status', '!=', '2')
                                ->get();

                    if (count($query) > 0) {

                        foreach($query as $row) {
                            $user_tree_array[] = array("id" => $row->id, "name" => $spacing . $row->category_name, "status" => $row->status, "permission" => $row->permission);
                            // $user_tree_array = fetchCategoryTree($row->id, $spacing . ' -- ', $user_tree_array);
                        }
                    }

                    return $user_tree_array;
                }

                $arr_parent[]   = array("id" => '0', "name" => 'Parent', "status" => '1', "permission" => '0');
                $categoryList   = array_merge($arr_parent, fetchCategoryTree());

                ?>


                <div class="form-group @if ($errors->has('product_category')) has-error @endif">
                    {{ Form::label('product_category', 'Product Category', array('class'=> 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <select multiple="multiple" id="product_category" name="product_category[]">
                            <?php 
                                
                                foreach($categoryList as $cl) {
                                    $selected   = '';
                                    $style      = '';
                                    $private    = '';
                                    // $cat        = explode(',', $product->category);
                                    
                                    foreach($cat as $c) {
                                        if($c['category_id']== $cl['id']) $selected = 'selected';

                                    }
                                    
                                    if ($cl['status'] == 0) $style = 'color:#f0ad4e;';
                                    if ($cl['permission'] == 1) $private = ' **[Private]';
                                    // if($product->category == $cl['id']) $selected = 'selected';
                            ?>
                                <option style="<?php echo $style; ?>" value="<?php echo $cl["id"] ?>" <?php echo $selected ?>><?php echo $cl["name"] . $private; ?> [ID:{{$cl['id']}}]</option>
                            <?php } ?>
                        </select>
                        {{ $errors->first('product_category', '<p class="help-block">:message</p>') }}
                    </div>
                </div>
                <hr/>
            </div>
        </div>
    </div>
    
    
    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 8, 3, 'AND'))
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
<!-- File Input JavaScript -->
<script src="../js/fileinput.min.js"></script>  
@stop

@section('script')
   $('#product_category').multiSelect({
        selectableHeader: '<div style="color:#428bca; text-align:center;">Available Category</div>',
        selectionHeader: "<div style='color:#428bca; text-align:center;'>Selected Category</div>",
    });
@stop