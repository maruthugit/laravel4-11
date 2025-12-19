@extends('layouts.master')

@section('title') Products @stop

@section('content')

<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

/*input[type=checkbox] {
    width: 8mm;
    -webkit-appearance: none;
    -moz-appearance: none;
    height: 8mm;
    border: 0.1mm solid black;
}

input[type=checkbox]:checked {
    background-color: #2196F3;
}

input[type=checkbox]:checked:after {
    margin-left: 2.2mm;
    margin-top: 1mm;
    width: 3mm;
    height: 5mm;
    border: solid white;
    border-width: 0 1.5mm 1.5mm 0;
    -webkit-transform: rotate(45deg);
    -moz-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
    content: "";
    display: inline-block;
}*/

/* Spinner */
.loadin {
    position: fixed;
    top: 0; right: 0;
    bottom: 0; left: 0;
    background: #fff;
}
.loader {
    left: 50%;
    margin-left: -4em;
    font-size: 10px;
    border: .8em solid rgba(218, 219, 223, 1);
    border-left: .8em solid rgba(58, 166, 165, 1);
    animation: spin 1.1s infinite linear;
}
.loader, .loader:after {
    border-radius: 50%;
    width: 8em;
    height: 8em;
    display: block;
    position: absolute;
    top: 50%;
    margin-top: -4.05em;
}

@keyframes spin {
  0% {
    transform: rotate(360deg);
  }
  100% {
    transform: rotate(0deg);
  }
}
/* Spinner */
.modal-backdrop {
    /* bug fix - no overlay */    
    display: none;    
}
</style>

<div id="page-wrapper">

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Products Bulk Edit
                 <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}product/bulkedit"><i class="fa fa-refresh"></i></a>
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
                                    <label for="seller">Seller</label>
                                    {{ Form::select('seller', array_merge(['any' => 'Any'], $sellers), Input::get('seller'), ['id' => 'seller', 'class' => 'form-control', 'tabindex' => 2]) }}
                                    <input type="hidden" name="loaded_seller" value="{{$seller}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="category">Category ID</label>
                                    {{ Form::text('category', Input::get('category'), ['autocomplete' => 'off', 'id' => 'category', 'class' => 'form-control', 'id' => 'category', 'placeholder' => 'Category ID', 'tabindex' => 3]) }}
                                    <p>* Separate with , for multiple category</p>
                                    <input type="hidden" name="loaded_category" value="{{$category}}">
                                    <div id="categoryAutoComplete" class="list-group autocomplete"></div>
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
                  {{ Form::open(array('class' => 'form-horizontal')) }}
                    <div style="margin-bottom: 10px;">
                        <button class="btn btn-primary" id="amendSelected" type="submit">Amend Selected</button>
                        <!-- <div class="btn btn-primary" id="revokeSelected">Revoke Selected</div> -->
                        <div class="btn btn-primary" id="amendAll">Amend all</div>
                        <div class="btn btn-primary" id="revokeAll">Revoke</div>
                    </div>
                    
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-products">
                            <thead>
                                <tr>
                                    <th class="col-sm-1"><input name="select_all" type="checkbox"></th>
                                    <th class="col-sm-1">Price Label ID</th>
                                    <th class="col-sm-1">SKU</th>
                                    <th class="col-sm-2">Product Name</th>
                                    <th class="col-sm-2">Price Label</th>
                                    <th class="col-sm-1 text-center">Quantity</th>
                                    <!-- <th class="col-sm-1 text-center">Original Quantity</th> -->
                                    <!-- <th class="col-sm-1 text-center">Action</th> -->
                                </tr>
                            </thead>
                        </table>
                    </div>
                  {{ Form::close() }}
                </div>
            </div>
            <div class="loadin" style="display: none">
                <div class="loader"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="amendModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Amend Quantity </h4>
                </div>
                <div class="modal-body">
                  {{ Form::open(array('class' => 'form-horizontal')) }}
                  
                  <div class="form-group">
                    {{ Form::label('amend_seller', 'Seller', array('class'=> 'col-lg-4 control-label')) }}
                    <div class="col-lg-5">
                      {{ Form::select('amend_seller', array_merge(['any' => 'Any'], $sellers), Input::get('seller'), ['id' => 'amend_seller', 'class' => 'form-control', 'tabindex' => 2]) }}
                    </div>
                  </div>

                  <div class="form-group">
                    {{ Form::label('amend_category', 'Category ID', array('class'=> 'col-lg-4 control-label')) }}
                    <div class="col-lg-5">
                      <input type="text" name="amend_category" id="amend_category" value="{{Input::get('category')}}" class="form-control">
                    </div>
                  </div>

                  <div class="form-group">
                    {{ Form::label('amend_quantity', 'Quantity', array('class'=> 'col-lg-4 control-label')) }}
                    <div class="col-lg-5">
                      <input type="number" name="amend_quantity" id="amend_quantity" class="form-control" value="" required>
                    </div>
                  </div>
                  <label class="radio-inline col-lg-4 control-label">
                      <input type="radio" name="amend_option" id="amend_option1" value="append" checked> Append
                  </label>
                  <label class="radio-inline control-label">
                     <input type="radio" name="amend_option" id="amend_option2" value="update"> Update
                  </label>
                  <br><br>
                  <div class="form-group">
                      <div class="col-sm-offset-4 col-sm-10">
                          <button type="submit" id="amendQuantity" class="btn btn-primary pull-left">Ammend</button>
                      </div>
                  </div>
                {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="revokeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Revoke Quantity </h4>
                </div>
                <div class="modal-body">
                <div class="panel panel-default">
                  <div class="panel-heading">
                      <h3 class="panel-title"><i class="fa fa-list"></i> Amendment Listing</h3>                    
                  </div>
                  <!-- /.panel-heading -->
                  <div class="panel-body">
                    {{ Form::open(array('class' => 'form-horizontal')) }}
                      <div class="table-responsive" style="overflow-x: none">
                          <table class="table table-striped table-bordered table-hover" id="dataTables-revoke">
                              <thead>
                                  <tr>
                                      <th class="col-sm-2">Seller</th>
                                      <th class="col-sm-2">Category</th>
                                      <th class="col-sm-1">Date</th>
                                      <th class="col-sm-1">Time</th>
                                      <th class="col-sm-2">Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                              </tbody>
                          </table>
                      </div>
                    {{ Form::close() }}                     
                  </div>
                  <!-- /.panel-body -->
                </div>
                </div>
            </div>
        </div>
    </div>
@stop


@section('inputjs')
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js" integrity="sha384-jspctxrQmizRroXAKjoWWOOhkG3aZesspL7dmISSX5+p1LfXftbGQocYmzOsv9wx" crossorigin="anonymous"></script>
<script>

    // var timer;

    // $('#category').keydown(function () {
    //     clearTimeout(timer);
    //     timer = setTimeout(function () {
    //         if ($('#category').val()) {
    //             $.ajax({
    //                 url: '{{ url('api/categorysearch?keyword=') }}' + $('#category').val() + '&limit=5',
    //                 success: function (result) {
    //                     if ($('#category').is(":focus")) {
    //                         var candidates = $.parseJSON(result);
    //                         var size = 0;

    //                         $('#categoryAutoComplete').html('');

    //                         $.each(candidates, function (i, candidate) {
    //                             var categoryName = candidate.category_name;

    //                             if (categoryName.search($('#category').val())) {
    //                                 var clickAction = "$('#categoryAutoComplete').html(''); $('#category').val('" + candidate.category_name + "'); return false;";

    //                                 $('#categoryAutoComplete').append('<a href="#" onclick="' + clickAction + '" class="list-group-item">' + candidate.category_name + '</a>');
    //                                 size++;
    //                             }
    //                         });

    //                         if ($('#category').is(':focus') && size > 0) {
    //                             $('#categoryAutoComplete').show();
    //                         }
    //                     }
    //                 }
    //             });
    //         } else {
    //             $('#categoryAutoComplete').html('');
    //         }
    //     }, 200);
    // });

// Updates "Select all" control in a data table
//
function updateDataTableSelectAllCtrl(table){
   var $table             = table.table().node();
   var $chkbox_all        = $('tbody .checkbox', $table);
   var $chkbox_checked    = $('tbody .checkbox:checked', $table);
   var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);

   // If none of the checkboxes are checked
   if($chkbox_checked.length === 0){
      chkbox_select_all.checked = false;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If all of the checkboxes are checked
   } else if ($chkbox_checked.length === $chkbox_all.length){
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If some of the checkboxes are checked
   } else {
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = true;
      }
   }
}

// Array holding selected row IDs
var rows_selected = [];
var table = $('#dataTables-products').DataTable({
    "ajax": "{{ URL::to('product/productsquantity?'.http_build_query(Input::all())) }}",
    'columnDefs': [{
        'targets': 0,
        'searchable':false,
        'orderable':false,
        'className': 'dt-body-center',
        'render': function (data, type, full, meta){
            return '<input type="checkbox" class="checkbox" style="display: inline-block">';
        }
    }],
    "order" : [[ 1, 'desc' ]],
    "columns" : [
        { "data" : "0", "className" : "text-center" },
        { "data" : "0", "className" : "text-center" },
        { "data" : "1" },
        { "data" : "2" },
        { "data" : "4", "className" : "text-center status" },
        { "data" : "5" },
        // { "data" : "7", "orderable" : false, "searchable" : false, "className" : "text-center" },
    ],
    'rowCallback': function(row, data, dataIndex ){
         // Get row ID
        var rowId = row.id;

        // If row ID is in the list of selected row IDs
        if($.inArray(rowId, rows_selected) !== -1){
            $(row).find('.checkbox').prop('checked', true);
            $(row).addClass('selected');
        }
    }
});

var revTable = $('#dataTables-revoke').dataTable({
  "ajax": "{{ URL::to('product/revokelist') }}",
  "order" : [[ 0, 'desc' ], [ 1, 'desc' ]],
  // "retrieve" : true,
  "columns" : [
      { "data" : "1", "className" : "text-center" },
      { "data" : "2", "className" : "text-center" },
      { "data" : "0", "className" : "text-center" },
      { "data" : "3", "className" : "text-center" },
      { "data" : "4", "className" : "text-center" }
  ],
});

// Handle click on checkbox
$('#dataTables-products tbody').on('click', '.checkbox', function(e){
  var $row = $(this).closest('tr');

  // Get row data
  var data = table.row($row).data();

  // Get row ID
  var rowId = data[0];

  // Determine whether row ID is in the list of selected row IDs 
  var index = $.inArray(rowId, rows_selected);

  // If checkbox is checked and row ID is not in list of selected row IDs
  if(this.checked && index === -1){
     rows_selected.push(rowId);

  // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
  } else if (!this.checked && index !== -1){
     rows_selected.splice(index, 1);
  }

  if(this.checked){
     $row.addClass('selected');
  } else {
     $row.removeClass('selected');
  }

  // Update state of "Select all" control
  updateDataTableSelectAllCtrl(table);

  // Prevent click event from propagating to parent
  e.stopPropagation();
});

// Handle click on table cells with checkboxes
$('#dataTables-products').on('click', 'tbody td, thead th:first-child', function(e){
  $(this).parent().find('.checkbox').trigger('click');
});

// Handle click on "Select all" control
$('thead input[name="select_all"]', table.table().container()).on('click', function(e){
    if(this.checked){
        $('#dataTables-products tbody .checkbox:not(:checked)').trigger('click');
    } else {
        $('#dataTables-products tbody .checkbox:checked').trigger('click');
    }

    // Prevent click event from propagating to parent
    e.stopPropagation();
});

// Handle table draw event
table.on('draw', function(){
  // Update state of "Select all" control
  updateDataTableSelectAllCtrl(table);
});


$('#amendSelected').on('click', function () {
      
  var data = table.rows('.selected').data();console.log(table.rows('.selected'));
  var datacheck = data.length; 

  if(datacheck > 0){
    var priceId = [];
    var qtys = []
    for (var i = 0; i < data.length; i++){
      priceId.push(data[i][0]);
      qtys.push($('#' + data[i][0]).val());
    }

    var price_id = priceId.join();
    var qty = qtys.join();

    $.ajax({
      method: "POST",
      url: "/product/amendselected",
      data: {
          'price_id':price_id,
          'qty':qty
      },
      beforeSend: function(){
      $('.loadin').show();
          console.log('Updating..');
      },
      success: function(data) {
        console.log(data);

        alert('Amended Successfully!');
        if(data.respStatus == 0){ 
        }

        table.ajax.reload(function() {
            $('.loadin').hide(); 
        }, false);
      }
    })
  }
  else {
    alert('No Row selected');
  }
});

$('#amendAll').on('click', function() {
    $("#amendModal").modal('show');
});

$('#revokeAll').on('click', function() {
    $("#revokeModal").modal('show');
});

$('#amendQuantity').on('click', function(){

  var amend_seller = $('#amend_seller').val();
  var amend_category = $('#amend_category').val();
  var quantity = $('#amend_quantity').val();
  var option = $("input[name='amend_option']:checked").val();

  console.log(amend_seller);
  console.log(amend_category);
  console.log(quantity);
  console.log(option)

  if (quantity != null && quantity.length > 0) {
    
    $.ajax({
      method: "POST",
      url: '/product/amendall',
      dataType: 'json',
      data: {
        'seller': amend_seller,
        'category': amend_category,
        'quantity': quantity,
        'option': option,
      },
      beforeSend: function() {
        $('.loadin').show();
      },
      success: function(data) {
        console.log(data);
        if(data.response == 1){
          // $("#amendAll").submit();
        }
      },
      error: function(error) {
        console.log(error);
        alert(error);
      }
    });  
  } else {
    alert('Please enter quantity.');
    return false;
  }
});

$('body').on('click', '.revokeBtn', function() {
  var quantity_id = this.id.substring(3);

  $('.loadin').show();
  $.ajax({
    method: "POST",
    url: '/product/revokeall',
    dataType: 'json',
    data: {
      'quantity_id': quantity_id
    },
    success: function(data) {
      console.log(data);
      if(data.response == 1){
        // $("#amendAll").submit();
      }
      $("#revokeModal").modal('hide');
      table.ajax.reload(function() {
          $('.loadin').hide(); 
      }, false);
      revTable.ajax.reload();
    },
    error: function(error) {
      $('.loadin').hide();
      console.log(error);
      alert(error);
      $("#revokeModal").modal('hide');
    }
  });
});

    
</script>
@stop

