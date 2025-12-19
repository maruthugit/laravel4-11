@extends('layouts.master')

@section('title') Jocom Combo Deals Management @stop

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
            <h1 class="page-header">Jocom Combo Deals Management
              <span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}jcmcombodeals"><i class="fa fa-refresh"></i></a>
              </span>
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(array('url' => 'jcmcombodeals/store' , 'class' => 'form-horizontal form-submit', 'files' => true)) }}

    <div class="panel panel-default">
      @if (Session::has('message'))
                <div class="alert alert-success">
                    <i class="fa fa-thumbs-up"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
                </div>
            @endif
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Create Jocom Combo Deals </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                 <div class='form-group'>
                {{ Form::label('main_title', 'Main Title', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-6">
                    {{ Form::text('main_title', null, array('class' => 'form-control') ) }}
                    </div>
                </div> 
                <div class='form-group'>
                {{ Form::label('rule_name', 'Rule Name', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-6">
                    {{ Form::text('rule_name', null, array('class' => 'form-control') ) }}
                    </div>
                </div>  
                <div class="form-group">
                    {{ Form::label('Date', 'Valid From/Until', array('class'=> 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <div class="input-group" id="datetimepicker_from">
                            {{ Form::text('valid_from', '', ['id' => 'valid_from', 'class' => 'valid_from form-control', 'tabindex' => 1]) }}
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-group" id="datetimepicker_to">
                            {{ Form::text('valid_to', '', ['id' => 'valid_to', 'class' => 'valid_to form-control', 'tabindex' => 2]) }}
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class='form-group'>
                {{ Form::label('banner_image', 'Banner Image', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-6">
                    <input type="file" id="banner_filename" name="banner_filename" class="form-control" accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps"  required = 'required' onchange="validateSize(this)">
                </div> 
                </div>
                <hr/>
                <div class="form-group @if ($errors->has('lid')) has-error @endif">
                            <?php $count = 1; ?>
                            <label class="col-lg-2 control-label" for="product">Products * </label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <span class="pull-left"><button id="addProdBtn" name="addProdBtn" class="btn btn-primary addProdBtn" data-toggle="tooltip" href="/jcmcombodeals/ajaxproduct"><i class="fa fa-plus"></i> Add Product</span>
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
                                            <th class="cell-small col-sm-1 text-center">Stock in Hand</th>
                                            <th class="cell-small col-sm-1 text-center">Per User Min Purchase Qty</th>
                                            <th class="cell-small col-sm-1 text-center">Limit Quantity</th>
                                            <th class="cell-small col-sm-1 text-center">Seq</th>
                                            <th class="cell-small text-center col-sm-1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ptb">
                                        <tr id="emptyproduct">
                                            <td colspan="8">No product added.</td>
                                        </tr>
                                    </tbody>
                                </table>
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
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Jocom Combo Deals Listing </h2>
        </div>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: none">
                <table class="table table-striped table-bordered table-hover" id="table_id" >
                    <thead>
                        <tr>
                            <th class="col-sm-1">Id</th>
                            <th class="col-sm-1">Main Title</th>
                            <th class="col-sm-1">Rule Name</th>
                            <th class="col-sm-1 text-center">Valid From</th>
                            <th class="col-sm-1 text-center">Valid To</th>
                            <th class="col-sm-1 text-center">Remaining Time</th>
                            <th class="col-sm-1 text-center">Status</th>
                            <th class="col-sm-1 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php foreach ($list as $key => $value) { ?>
                        <tr>
                            <td><?php echo $value->id; ?></td>
                            <td><?php echo $value->main_title; ?></td>
                            <td><?php echo $value->rule_name; ?></td>
                            <td><?php echo $value->valid_from; ?></td>
                            <td><?php echo $value->valid_to; ?></td>
                            <td>
                                
                                <span class='countdown' value='<?php echo $value->valid_to; ?>'></span>
                                
                            </td>
                            <td><?php if ($value->status == 1) {
                                echo '<center><img src="/images/data/thumbs/active.png" style="height: 25px;width: 25px;" title="Active" alt="Active"></center>';
                            }else{ 
                                echo '<center><img src="/images/data/thumbs/deactive.png" style="height: 25px;width: 25px;" title="Active" alt="Active"></center>';
                            } ?></td>
                            <td><a class="btn btn-primary" title="" data-toggle="tooltip" href="/jcmcombodeals/edit/<?php echo $value->id; ?>"><i class="fa fa-pencil"></i></a>
                              
                              <a id="deleteBan" class="btn btn-danger" title="" data-toggle="tooltip" data-value="<?php echo $value->id; ?>" href="/jcmcombodeals/delete/<?php echo $value->id; ?>"><i class="fa fa-times"></i></a>
                            </td>
                            </tr>
                       <?php } ?>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js" ></script>

<script>
    $('.countdown').each(function(){
        $(this).countdown($(this).attr('value'), function(event) {
            $(this).text(
                event.strftime('%D days %H:%M:%S')
            );
        });
    });
    function validateSize(input) {
  const fileSize = input.files[0].size / 1024 / 1024; // in MiB
  if (fileSize > 0.100) {
    alert('File size should not exceeds 100kB');
    $('#banner_filename').val('');
  }
  if (input && input.files.length > 0) 
  {
        var img = new Image();

        img.src = window.URL.createObjectURL( input.files[0] );
        img.onload = function() 
        {
            var width = this.naturalWidth,
                height = this.naturalHeight;
                
               if(width>640 || height>300){
                   alert("Image resolution must be less then or equal to 640X300");
                    $('#banner_filename').val('');
               }
        };
    }
}
</script>
<script>
    $(function() {
        $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });

        var table = $('#table_id').DataTable(
        {searching: false,pageLength: 10,
            "order": [[ 0, "desc" ]]
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
    
    $('form.form-submit').on('submit', function(e) {
    
        var position = [];

        $('.seq').each(function() {
            position.push(this.value);
        });

        if (duplicateExists(position)) {
            e.preventDefault();
            alert('There is a duplicate seq. Please check');
            return;
        }
    });

    function duplicateExists(w){
        return new Set(w).size !== w.length 
    }
@stop