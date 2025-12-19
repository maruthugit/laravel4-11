@extends('layouts.master')
@section('title', 'SP')
@section('content')

<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Special Price - Edit</h1>
		</div>
		<div class="row">
        <div class="col-lg-12">
            {{ Form::open(array('url'=>'/special_price/update/'.$id, 'class' => 'form-horizontal')) }}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Special Price Details</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        <div class="form-group @if ($errors->has('id')) has-error @endif">
                            {{ Form::label('group', 'Group Name', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-6">
                            {{ Form::text('id', $group->name, array('class' => 'form-control', 'disabled') ) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                <h3 class="panel-title">Product Listing </h3>
            </div>

            <div class="panel-body">
                <div class="table-responsive" style="overflow-x: none;" >
                    <table class="table table-bordered table-striped table-hover" id="dataTables-disc">
                        <thead>
                            <tr>
                                <th class="col-sm-1">ID</th>
                                <th class="text-center col-sm-3">Product Name</th>
                                <th class="text-center col-sm-3">Label</th>
                                <th class="text-center col-sm-1">Actual Price<br>({{Config::get("constants.CURRENCY")}})</th>         
                                <th class="text-center col-sm-1">Promotion Price<br>({{Config::get("constants.CURRENCY")}})</th>  
                                <th class="text-center col-sm-1">Discount<br>Amount</th>
                                <th class="text-center col-sm-1">Discount<br>Type</th>               
                                <th class="text-center col-sm-1">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
            
        </div>
	</div>
	{{ Form::close() }}
</div>
</div>
	
@stop

@section('script')
    localStorage.clear();

    
    $('#dataTables-disc').dataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('special_price/discounts/'.$id) }}",
        "order" : [[0,'desc']],
        "columnsDef" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0", "orderable" : false },
            { "data" : "1", "class" : "text-left", "orderable" : false, "searchable" : false },
            { "data" : "2", "class" : "text-left", "orderable" : false },
            { "data" : "3", "class" : "text-center" },
            { "data" : "4", "class" : "text-center" },
            { "data" : "5", "class" : "text-center", "orderable" : false, "searchable" : false },
            { "data" : "6", "visible" : false},
            { "data" : "7", "class" : "text-center" },
        ],
        "createdRow" : function (row, data, rowIndex) {
            $(row).attr('id', data[0]);
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


    $(document).on("click", "#selectItem", function(e) {
        e.preventDefault();
        var id       = $(this).closest("tr").attr("id");


        $.colorbox({
            iframe:true, width:"55%", height:"40%",
            href:"/special_price/ajaxdiscount/" + id,
        });
    });

    $(document).on("click", "#deleteProduct", function(e) {
        e.preventDefault();
        $(this).closest("tr").remove();
        if(!$('.disc_product').length) {
            $('#ptb').append('<tr id="emptyproduct"><td colspan="5">No product added.</td></tr>');
        }
    });
@stop