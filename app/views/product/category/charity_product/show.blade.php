@extends('layouts.master')

@section('title', 'Charity Product')

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Charity Product</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            {{ Form::open(['class' => 'form-horizontal']) }}
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-pencil"></i> Charity Details</h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-lg-12">
                            <div class="form-group">
                                {{ Form::label('', 'Charity ID', ['class' => 'col-lg-2 control-label']) }}
                                <div class="col-lg-10">
                                    <p class="form-control-static">{{ $charityCategory->id }}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('', 'Charity Name', ['class' => 'col-lg-2 control-label']) }}
                                <div class="col-lg-10">
                                    <p class="form-control-static">{{ $charityCategory->name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-pencil"></i> Product Details</h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-lg-12">
                            <div class="form-group">
                                {{ Form::label('', 'Products *', ['class' => 'col-lg-2 control-label']) }}
                                <div class="col-lg-10">
                                    <a id="addProduct" class="btn btn-primary" href="/product/category/charityproduct/add/{{ $charityCategory->id }}"><i class="fa fa-plus fa-fw"></i> Add Product</a>
                                    <br><br>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Product Name & SKU</th>
                                                    <th>Label</th>
                                                    <th>Quantity</th>
                                                    <th>Quota</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($charityProducts as $charityProduct)
                                                    <tr>
                                                        <td>
                                                            <b>{{ $charityProduct->name }}</b><br>
                                                            <i class="fa fa-tag fa-fw"></i>{{ $charityProduct->sku }}
                                                        </td>
                                                        <td>{{ $charityProduct->label }}</td>
                                                        <td>{{ $charityProduct->qty }}</td>
                                                        <td>{{ $charityProduct->quota }}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#editModal" data-id="{{ $charityProduct->id }}" data-quantity="{{ $charityProduct->qty }}" data-quota="{{ $charityProduct->quota }}"><i class="fa fa-pencil fa-fw"></i> Edit</button>
                                                            <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteModal" data-id="{{ $charityProduct->id }}"><i class="fa fa-close fa-fw"></i> Remove</button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5">No product.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<!-- /product/category/charityproduct/edit/{{ $charityCategory->id }}/{{ $charityProduct->id }} -->
<!-- /product/category/charityproduct/remove/{{ $charityCategory->id }}/{{ $charityProduct->id }} -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Quantity / Quota</h4>
            </div>
            <div class="modal-body">
                {{ Form::open(['class' => 'form-horizontal', 'method' => 'put']) }}
                    <input type="hidden" id="editCharityProductId" name="charityProductId">
                    <div class="form-group">
                        <label for="quantity" class="col-sm-2 control-label">Quantity</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Quantity" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="quota" class="col-sm-2 control-label">Quota</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="quota" name="quota" placeholder="Quota" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary pull-right">Update</button>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Remove Product</h4>
            </div>
            <div class="modal-body">
                {{ Form::open(['class' => 'form-horizontal', 'method' => 'delete']) }}
                    <input type="hidden" id="deleteCharityProductId" name="charityProductId">
                    <p>Are you sure you want to remove the product?</p>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <button type="submit" class="btn btn-danger pull-right">Remove</button>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
    $('#addProduct').colorbox({
        iframe: true,
        height: '90%',
        width: '90%'
    });

    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var charityProductId = button.data('id');
        var quantity = button.data('quantity');
        var quota = button.data('quota');

        var modal = $(this);
        modal.find('#editCharityProductId').val(charityProductId);
        modal.find('#quantity').val(quantity);
        modal.find('#quota').val(quota);
    })

    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var charityProductId = button.data('id');

        var modal = $(this);
        modal.find('#deleteCharityProductId').val(charityProductId);
    })
@stop
