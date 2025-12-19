
@extends('layouts.master')

@section('title', 'Cost Price')

@section('content')
<style>.loader {
    background-color: rgb(54 50 50 / 95%);
    height: 100%;
    width: 100%;
    position: fixed;
    z-index: 1000;
    margin-top: 0px;
    top: 0px;
}
.loader-centered {
    position: absolute;
    left: 50%;
    top: 50%;
    height: 200px;
    width: 200px;
    margin-top: -100px;
    margin-left: -132px;
}
.object {
    width: 50px;
    height: 50px;
    background-color: rgba(255, 255, 255, 0);
    margin-right: auto;
    margin-left: auto;
    border: 4px solid #fff;
    left: 73px;
    top: 73px;
    position: absolute;
}

.square-one {
    -webkit-animation: first_object_animate 1s infinite ease-in-out;
    animation: first_object_animate 1s infinite ease-in-out;
}
.square-two {
    -webkit-animation: second_object 1s forwards, second_object_animate 1s infinite ease-in-out;
    animation: second_object 1s forwards, second_object_animate 1s infinite ease-in-out;
}
.square-three {
    -webkit-animation: third_object 1s forwards, third_object_animate 1s infinite ease-in-out;
    animation: third_object 1s forwards, third_object_animate 1s infinite ease-in-out;
}

@-webkit-keyframes second_object {
    100% {
        width: 100px;
        height: 100px;
        left: 48px;
        top: 48px;
    }
}
@keyframes second_object {
    100% {
        width: 100px;
        height: 100px;
        left: 48px;
        top: 48px;
    }
}
@-webkit-keyframes third_object {
    100% {
        width: 150px;
        height: 150px;
        left: 23px;
        top: 23px;
    }
}
@keyframes third_object {
    100% {
        width: 150px;
        height: 150px;
        left: 23px;
        top: 23px;
    }
}

@-webkit-keyframes first_object_animate {
    0% {
        -webkit-transform: perspective(100px);
    }
    50% {
        -webkit-transform: perspective(100px) rotateY(-180deg);
    }
    100% {
        -webkit-transform: perspective(100px) rotateY(-180deg) rotateX(-180deg);
    }
}

@keyframes first_object_animate {
    0% {
        transform: perspective(100px) rotateX(0deg) rotateY(0deg);
        -webkit-transform: perspective(100px) rotateX(0deg) rotateY(0deg);
    }
    50% {
        transform: perspective(100px) rotateX(-180deg) rotateY(0deg);
        -webkit-transform: perspective(100px) rotateX(-180deg) rotateY(0deg);
    }
    100% {
        transform: perspective(100px) rotateX(-180deg) rotateY(-180deg);
        -webkit-transform: perspective(100px) rotateX(-180deg) rotateY(-180deg);
    }
}

@-webkit-keyframes second_object_animate {
    0% {
        -webkit-transform: perspective(200px);
    }
    50% {
        -webkit-transform: perspective(200px) rotateY(180deg);
    }
    100% {
        -webkit-transform: perspective(200px) rotateY(180deg) rotateX(180deg);
    }
}

@keyframes second_object_animate {
    0% {
        transform: perspective(200px) rotateX(0deg) rotateY(0deg);
        -webkit-transform: perspective(200px) rotateX(0deg) rotateY(0deg);
    }
    50% {
        transform: perspective(200px) rotateX(180deg) rotateY(0deg);
        -webkit-transform: perspective(200px) rotateX(180deg) rotateY(0deg);
    }
    100% {
        transform: perspective(200px) rotateX(180deg) rotateY(180deg);
        -webkit-transform: perspective(200px) rotateX(180deg) rotateY(180deg);
    }
}

@-webkit-keyframes third_object_animate {
    0% {
        -webkit-transform: perspective(300px);
    }
    50% {
        -webkit-transform: perspective(300px) rotateY(-180deg);
    }
    100% {
        -webkit-transform: perspective(300px) rotateY(-180deg) rotateX(-180deg);
    }
}

@keyframes third_object_animate {
    0% {
        transform: perspective(300px) rotateX(0deg) rotateY(0deg);
        -webkit-transform: perspective(300px) rotateX(0deg) rotateY(0deg);
    }
    50% {
        transform: perspective(300px) rotateX(-180deg) rotateY(0deg);
        -webkit-transform: perspective(300px) rotateX(-180deg) rotateY(0deg);
    }
    100% {
        transform: perspective(300px) rotateX(-180deg) rotateY(-180deg);
        -webkit-transform: perspective(300px) rotateX(-180deg) rotateY(-180deg);
    }
}
</style>
        <div class="loader" style="display: none;" >
    <div class="loader-centered">
    <h1 style="color:white;margin-top: 200px !important;width: 127%;">Please Wait!!!</h1>
        <div class="object square-one"></div>
        <div class="object square-two"></div>
        <div class="object square-three"></div>
    </div>
</div>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Cost Price 
            
            </h1>
        </div>
    </div>
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
            <h3 class="panel-title"><i class="fa fa-search"></i> Advanced Search</h3>
        </div>
        <div class="panel-body">
            <form method="POST" id="nonform">
                <div class="row">
                    
                    <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">Product Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Product Name" id="name" value="<?php echo Input::get('name');?>">
                                    <!-- {{ Form::text('name', Input::get('name'), ['id' => 'name', 'class' => 'form-control', 'placeholder' => 'Product Name', 'tabindex' => 1]) }} -->
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">Product ID</label>
                                    <input type="text" name="product_id" class="form-control" placeholder="Product ID" id="product_id" value="<?php echo Input::get('product_id') ?>">
                                   <!--  {{ Form::text('product_id', Input::get('product_id'), ['id' => 'product_id', 'class' => 'form-control', 'placeholder' => 'Product ID', 'tabindex' => 1]) }} -->
                                </div>
                            </div>
                        
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="vendor">Vendor</label>
                            <select name="vendor" class="form-control">
                                <option value="all">All</option>
                                @foreach($seller as $value)
                                <?php $old=Input::get('vendor'); ?>
                    @if ($old== $value['id'])
    <option value="{{ $value['id'] }}" selected>{{ $value['name'] }}</option>
@else
    <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
@endif

                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                {{ Form::submit('Search', ['class' => 'btn btn-primary btn-center', 'tabindex' => 6]) }}
            </form>

        </div>
    </div>
  <div>
    <a class="btn btn-danger pull-right" id="importcost" title="Import Cost Price" data-toggle="modal" data-target="#myModal" style="margin-left:6px"><i class="fa fa-upload" aria-hidden="true"> Import</i></a>
          <button id="export" class="btn btn-success pull-right">Export</button>         
<br>
</br>
  </div>     
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> SKU Listing </h3>
        </div>
        <div class="panel-body">
            <div class="dataTable_wrapper">
                <table class="table table-bordered table-striped table-hover" id="dataTables-sku">
                    <thead>
                        <tr>
                            <th class="col-sm-1">Product ID</th>
                            <th class="col-lg-2">SKU</th>
                            <th class="col-lg-2">Product Name</th>
                            <th class="col-sm-1">Vendor Name</th>
                            <th class="col-sm-1">Inventory QTY</th>
                            <th class="col-sm-1">Cost Price</th>
                            <th class="col-sm-1">Total Amount</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
       <div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Import Cost Price Details</h4>
      </div>
      <div class="modal-body">
        <div class="">
                        <table style="width:100%; border: 1px solid black;
  border-collapse: collapse;">
  <tr>
    <th style="border: 1px solid black;
  border-collapse: collapse; text-align: center;">PRODUCT ID</th>
    <th style="border: 1px solid black;
  border-collapse: collapse; text-align: center;">SKU</th> 
    <th style="border: 1px solid black;
  border-collapse: collapse; text-align: center;">SELLER ID</th>
    <th style="border: 1px solid black;
  border-collapse: collapse; text-align: center;">COST PRICE</th>

  </tr>
  <tr>
      <td style="border: 1px solid black;
  border-collapse: collapse; text-align: center;">1</td>
      <td style="border: 1px solid black;
  border-collapse: collapse; text-align: center;">JC-0001</td>
      <td style="border: 1px solid black;
  border-collapse: collapse; text-align: center;">34</td>
      <td style="border: 1px solid black;
  border-collapse: collapse; text-align: center;">4.00</td>
  </tr>
</table>
                    </div>
                    <div class="">
                        </br>
                        <p>Excel Data Must be in Same Order as mentioned Above</p>
                    </div>
                    <b>Format: CSV</b>
                     <p>Download Sample: <a href="/public/public/media/csv/costprice/sample.csv" style="margin-right:10px;margin-bottom:3px " download="sample.csv">Sample</a></p>
        <form method="post" enctype="multipart/form-data"  action="/product/importcostprice">
            <div class="form-group">
           <input type="file" name="import" class="form-control" accept=".csv" required>
       </div>
       
      </div>

      <div class="modal-footer">
       <button type="submit" class="btn btn-primary" style="float: right;">Submit</button>
      </div> 

        </form>
    </div>

  </div>
</div> 

</div>

@stop

@section('script')
$(document).ready(function() {
   $("#name").click(function(){
    var product_id=$('#product_id').val();
        if(product_id){
        var name=$('#name').val("");
          alert("Please Fill Only one Product filter");
          return false;
       }
   });

    $("#product_id").click(function(){
    var name=$('#name').val();
        if(name){
        var name=$('#product_id').val("");
          alert("Please Fill Only one Product filter");
          return false;
       }
   });


});
$('#dataTables-sku').dataTable({
    "autoWidth": false,
    "processing": true,
    "serverSide": true,
    "ajax": "{{ URL::to('product/costnode?'.http_build_query(Input::all())) }}",
    "order": [[0,'desc']],
    "columnDefs": [{
        "targets": "_all",
        "defaultContent": ""
    }],
    "columns": [
        { "data": "0", "searchable" : true },
        { "data": "1","searchable" : true },
        { "data": "2","searchable" : true },
        { "data": "3" },
        { "data": "5",},
        { "data": "4",},
        { "data": "6",},
     
    ]
    
});



$('#export').on('click', function () {
        $url="{{ URL::to('product/costreport?'.http_build_query(Input::all())) }}";
    loader($url);
    });
function loader($url){
        $.ajax({
  url: $url,
  beforeSend: function() {
    window.location = $url;
      $(".loader").show();
  },          
  complete: function() {
  $(".loader").hide();
  }
 
});
    }



@stop
