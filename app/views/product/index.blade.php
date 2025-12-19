@extends('layouts.master')

@section('title') Products @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Products
                 <span class="pull-right">
                     <div class="btn-group" role="group" aria-label="Basic example">
                        <a href="/product" type="button" class="btn btn-secondary btn-default active">Normal Version</a>
                        <a href="/product/productinline" type="button" class="btn btn-secondary btn-default ">Quick Edit</a>
                    </div>
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}product"><i class="fa fa-refresh"></i></a>
                    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 5, 'AND'))
                    <a class="pull-right"><a class="btn btn-primary" title="" data-toggle="tooltip" href="/product/create"><i class="fa fa-plus"></i></a>
                    @endif
                </span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            @if (Session::has('message'))
                <div class="alert alert-success">
                    <i class="fa fa-thumbs-up"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
                </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-search"></i> Advanced Search</h3>
                </div>
                <div class="panel-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">Product Name</label>
                                    {{ Form::text('name', Input::get('name'), ['id' => 'name', 'class' => 'form-control', 'placeholder' => 'Product Name', 'tabindex' => 1]) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="seller">Seller</label>
                                    {{ Form::select('seller', array_merge(['any' => 'Any'], $sellers), Input::get('seller'), ['id' => 'seller', 'class' => 'form-control', 'tabindex' => 2]) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    {{ Form::text('category', Input::get('category'), ['autocomplete' => 'off', 'id' => 'category', 'class' => 'form-control', 'id' => 'category', 'placeholder' => 'Category name or ID', 'tabindex' => 3]) }}
                                    <div id="categoryAutoComplete" class="list-group autocomplete"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    {{ Form::select('status', ['any' => 'Any', 'active' => 'Active only', 'inactive' => 'Inactive only'], Input::get('status'), ['id' => 'status', 'class' => 'form-control', 'tabindex' => 4]) }}
                                </div>
                            </div>
                        </div>
                        {{ Form::submit('Search', ['class' => 'btn btn-primary', 'tabindex' => 5]) }}
                    </form>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Product List</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-products">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-1">SKU</th>
                                    <th class="col-sm-1 text-center">Image</th>
                                    <!--<th class="col-sm-2">Seller</th>-->
                                    <th class="col-sm-2">Product Name</th>
                                    <th class="col-sm-1 text-center">Actual Price</th>
                                    <th class="col-sm-1 ">Promo Price</th>
                                    <th class="col-sm-2 text-center">Category</th>
                                    <th class="col-sm-1 text-center">Status</th>
                                    <th class="col-sm-1 text-center">Priority</th>
                                    <th class="col-sm-1 text-center">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    var timer;

    $('#category').keydown(function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            if ($('#category').val()) {
                $.ajax({
                    url: '{{ url('api/categorysearch?keyword=') }}' + $('#category').val() + '&limit=5',
                    success: function (result) {
                        if ($('#category').is(":focus")) {
                            var candidates = $.parseJSON(result);
                            var size = 0;

                            $('#categoryAutoComplete').html('');

                            $.each(candidates, function (i, candidate) {
                                var categoryName = candidate.category_name;

                                if (categoryName.search($('#category').val())) {
                                    var clickAction = "$('#categoryAutoComplete').html(''); $('#category').val('" + candidate.category_name + "'); return false;";

                                    $('#categoryAutoComplete').append('<a href="#" onclick="' + clickAction + '" class="list-group-item">' + candidate.category_name + '</a>');
                                    size++;
                                }
                            });

                            if ($('#category').is(':focus') && size > 0) {
                                $('#categoryAutoComplete').show();
                            }
                        }
                    }
                });
            } else {
                $('#categoryAutoComplete').html('');
            }
        }, 200);
    });

    $('html').click(function () {
        $('#categoryAutoComplete').html('').hide();
    });

    $('#dataTables-products').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('product/products?'.http_build_query(Input::all())) }}",
        "order" : [[ 0, 'desc' ]],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0", "className" : "text-center" },
            { "data" : "1" },
            { "data" : "9"},
            <!--{ "data" : "2" },-->
            { "data" : "3" },
            { "data" : "4", "className": "text-right" },
            { "data" : "5", "className": "text-right" },
            { "data" : "6", "searchable" : false },
            { "data" : "7", "className" : "text-center" },
            { "data" : "8", "visible" : false, "searchable" : false },
            { "data" : "10", "orderable" : false, "searchable" : false, "className" : "text-center" }
        ]
    });

    $(document).on("click", "#deleteItem", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete?",
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