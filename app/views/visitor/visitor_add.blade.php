@extends('layouts.master')

@section('title') Visitor Management @stop

@section('content')

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>
<div id="page-wrapper">
    @if ($errors->any())
        {{ implode('', $errors->all('<div class=\'bg-danger alert\'>:message</div>')) }}
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Visitor Management
              <span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}visitor"><i class="fa fa-refresh"></i></a>
              </span>
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(array('url' => 'visitor/store' , 'class' => 'form-horizontal', 'files' => true)) }}

    <div class="panel panel-default">
      @if (Session::has('message'))
                <div class="alert alert-success">
                    <i class="fa fa-thumbs-up"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
                </div>
            @endif
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Create Visitor </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                {{ Form::label('visitor_name', 'Visitor Name', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-6">
                    {{ Form::text('visitor_name', null, array('class' => 'form-control') ) }}
                    </div>
                </div>  
                <div class='form-group'>
                {{ Form::label('visitor_ic', 'Visitor NRIC No./Email', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-6">
                    {{ Form::text('visitor_ic', null, array('class' => 'form-control') ) }}
                    </div>
                </div>  
                <div class='form-group'>
                {{ Form::label('visitor_datetime', 'Visitor Date & Time', array('class'=> 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <div class="input-group" id="datetimepicker_from">
                            {{ Form::text('visitor_datetime', '', ['id' => 'visitor_datetime', 'class' => 'valid_from form-control', 'tabindex' => 1]) }}
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                </div>  

                <div class='form-group'>
                {{ Form::label('visit_purpose', 'Visit Purpose', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-6">
                    {{ Form::textarea('visit_purpose', '', array('class'=> 'form-control')) }}
                    </div>
                </div>

                <hr/>
            </div>

            <div class='form-group'>
                <div class="col-lg-10">
                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                    {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}

    <div class="panel panel-default">
      <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Visitor Listing </h2>
        </div>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: none">
                <table class="table table-striped table-bordered table-hover" id="dataTables-vistor" >
                    <thead>
                        <tr>
                            <th class="col-sm-1">Id</th>
                            <th class="col-sm-1">Visitor Name</th>
                            <th class="col-sm-1 text-center">Visitor IC</th>
                            <th class="col-sm-1 text-center">Visitor Date & Time</th>
                            <th class="col-sm-1 text-center">Visit Purpose</th>
                            <th class="col-sm-1 text-center">Status</th>
                            <th class="col-sm-1 text-center">Action</th>
                        </tr>
                    </thead>
                    
                </table>
            </div>
        </div>
    </div>


</div>
<script src="/js/jquery.countdown.min.js" ></script>

<script>
    $('.countdown').each(function(){
        $(this).countdown($(this).attr('value'), function(event) {
            $(this).text(
                event.strftime('%D days %H:%M:%S')
            );
        });
    });
</script>
<script>
    $(function() {
        $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });

        var table = $('#table_id').DataTable(
        {searching: false,pageLength: 10,
            "order": [[ 1, "desc" ]]
        }
      );
    });


      $('body').on('click', '.remove-seller', function() {
       
        if($("#multipleSellerTable").children().length <= 1){
            alert('Must has minimum 1 Product');
        }else{
             var seller_id = $(this).attr("seller-id");
             $(".seller-cost-"+seller_id).remove();
            console.log($(this).parent().parent().remove());
        }
        
    });
   
   $("#add-seller-btn").click(function(){
       
        console.log($("#seller_name2").val());
       
        var seller_id = $("#seller_name2").val();
        var str = '<tr><td>'+seller_id+'<input name="product_sku[]" value="'+seller_id+'" type="hidden"></td> <td><input type="text" name="product_price[]"></td><td><input type="text" name="product_quantity[]"></td> <td><a seller-id="'+seller_id+'" class="btn btn-danger btn-xs remove-seller"><i class="fa fa-trash-o"></i> Remove</a></td></tr>';
                   $("#multipleSellerTable").append(str);

   });

</script>

@stop

@section('script')

$('#dataTables-vistor').dataTable({
    "autoWidth": false,
    "processing": true,
    "serverSide": true,
    "ajax": "{{ URL::to('visitor/listing?'.http_build_query(Input::all())) }}",
    "order": [[0,'desc']],
    "columnDefs": [{
        "targets": "_all",
        "defaultContent": ""
    }],
    "columns": [
        { "data": "0", "searchable" : false },
        { "data": "1" },
        { "data": "2" },
        { "data": "3" },
        { "data": "4", "visible": false, "orderable" : false},
        { "data": "5", "orderable" : false, "searchable" : false },
        { "data": "6" }
        
    ]
});


$(document).on("click", "#deletevisitor", function(e) {
    var link = $(this).attr("href");
    e.preventDefault();
    bootbox.confirm({
        title: "Delete entry",
        message: "Are you sure to delete this visitor - " + $(this).attr("data-value") + " ?",
        callback: function (result) {
            if (result === true) {
                console.log("Delete customer id");
                window.location = link;
            } else {
                console.log("IGNORE");
            }
        }
    });
});


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

    $(document).on("click", "#deleteBan", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete this - " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete banner id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
@stop