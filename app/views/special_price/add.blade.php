@extends('layouts.master')
@section('title', 'SP')
@section('content')

<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Special Price - Add</h1>
		</div>
		<div class="row">
        <div class="col-lg-12">
            {{ Form::open(array('url'=>'/special_price/store', 'class' => 'form-horizontal')) }}
            <div class="panel panel-default">
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
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Special Price Details</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
		                <div class="form-group @if ($errors->has('group_id')) has-error @endif">
		                    {{ Form::label('group', 'Group Name', array('class'=> 'col-lg-2 control-label')) }}
		                    <div class="col-lg-3">
		                        {{ Form::select('group_id', array('' => '- - - Select Group Name - - -') + $groups, null, ['class' => 'form-control']) }}
                                {{ $errors->first('group_id', '<p class="help-block">Please select a Group</p>') }}
		                    </div>
		                </div>
                        
                        <div class="form-group @if ($errors->has('lid')) has-error @endif">
                            <?php $count = 1; ?>
                            <label class="col-lg-2 control-label" for="product">Products * </label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <span class="pull-left"><button id="addProdBtn" name="addProdBtn" class="btn btn-primary addProdBtn" data-toggle="tooltip" href="/special_price/ajaxproduct"><i class="fa fa-plus"></i> Add Product</span>
                                    </div>
                                </div>
                                <br />
                            <div class="clearfix">{{ $errors->first('lid', '<p class="help-block">:message</p>') }}</div>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-2">Product Name &amp; SKU</th>
                                            <th class="hidden-xs hidden-sm col-sm-3">Label</th>
                                            <th class="hidden-xs hidden-sm col-sm-1 text-center">Actual Price <br>({{Config::get("constants.CURRENCY")}})</th>
                                            <th class="hidden-xs hidden-sm col-sm-1 text-center">Promotion Price <br>({{Config::get("constants.CURRENCY")}})</th>
                                            <th class="cell-small col-sm-1 text-center">Discount Amount</th>
                                            <th class="cell-small text-center col-sm-1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ptb">
                                        <tr id="emptyproduct">
                                            <td colspan="5">No product added.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
	</div>

	@if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 5, 'AND'))
	<div class='form-group'>
		<div class="col-lg-10">
			{{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
			{{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
		</div>
	</div>
	@endif
	{{ Form::close() }}
</div>
</div>
	
@stop

@section('script')
    localStorage.clear();
	$('#addProdBtn').colorbox({
        iframe:true, width:"90%", height:"90%",
        onClosed:function(){
        	calSubTotal();
            calculateTotal();
        }
    });

    function currencyFormat(num) {
        return num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
    }

    function calSubTotal() {
        var subTotal    = 0;
        var subPromo    = 0;
        var qty         = 0;
        var prodprice   = 0;
        var promoprice  = 0;

        $('tr.product').each(function(){
            var cur     = '#'+this.id;
            prodprice   = $(cur + ' td.p_price').text();
            promoprice  = $(cur + ' td.promo_price').text();
            qty         = $(cur + ' td.qty').text();
            var price   = prodprice;

            if(promoprice > 0) {
                //alert('Promo Price: '+promoprice);
                price = promoprice;
            }

            subTotal  = qty * parseFloat(price.replace(/,/g,''));

            //alert('Product price: '+prodprice+'\nQty: '+qty+'\nSub-Total: '+subTotal);
            $(cur + ' .subtotal').html(currencyFormat(subTotal));

        });
    }

    function calculateTotal() {
        var grandTotal = 0;
        var grandQty = 0;

        $('tr.product td.subtotal').each(function(){
            subtotal = $(this).text();
            grandTotal += parseFloat(subtotal.replace(/,/g,''));
        });

        $('tr.product td.qty').each(function(){
            qty = $(this).text();
            grandQty += parseFloat(qty.replace(/,/g,''));;
        });
        
        $('.grand_total').html(currencyFormat(grandTotal));
        $('.grand_qty').html(grandQty);

    }

    $(document).on("click", "#deleteProduct", function(e) {
        e.preventDefault();
        $(this).closest("tr").remove();
        if(!$('.disc_product').length) {
            $('#emptyproduct').show();
        }
    });
@stop