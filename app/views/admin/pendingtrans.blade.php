@extends('layouts.master')
@section('title') Pending Transactions  @stop
@section('content')
<style>
    .center{
        text-align: center;
    }
    .loading {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999999;
        /*background: #3d464d;*/
        background: #FFF;
        opacity: 1.00;
        display: none;
        color: red;
        }
    .loading #load-message {
        width: 80px;
        height: 80px;
        position: absolute;
        left: 50%;
        right: 50%;
        bottom: 50%;
        top: 50%;
        margin: -20px;
        color: red;
    }
</style>
<div class="loading"><span id="load-message"></span></div>
<form id="frm-trans" ction="{{asset('/')}}transaction/new" method="POST">
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Pending Transactions
                 <span class="pull-right">
                    <!-- <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}product/createfoc"><i class="fa fa-plus"></i> Approve / Complete Transactions</a> -->
                    <button type="button" name="approve" id="approve" class="btn btn-primary" title="" data-toggle="tooltip"><i class="fa fa-plus"></i> Approve / Complete Transactions</button>
                    <span style="width:10px;"></span>
                    <!-- <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}product"><i class="fa fa-times"></i> Cancel Transactions</a> -->
                    <button class="btn btn-primary" title="" data-toggle="tooltip" value="2" name="cancel" id="cancel" type="button"><i class="fa fa-times"></i> Cancel Transactions</button>
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
                    <h3 class="panel-title"><i class="fa fa-list"></i> Product List</h3>
                </div>
                
                 <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none;">
                        <table class="table table-bordered table-striped table-hover" id="dataTables-result">
                            <thead>
                                <tr>
                                    <th><input name="select_all" type="checkbox"></th>
                                    <th>Transaction ID</th>
                                    <th>Transaction Date</th>
                                    <th>Username</th>    
                                    <th>Platform</th>   
                                    <th>Total Amount</th>
                                    <th>State</th>  
                                    <th>Status</th>  
                                </tr>
                            </thead>
                            <!-- <tfoot>
                              <tr>
                                    <th></th>
                                    <th>Transaction ID</th>
                                    <th>Transaction Date</th>
                                    <th>Username</th>    
                                    <th>Total Amount</th>
                                    <th>State</th>  
                                    <th>Status</th>  
                              </tr>
                           </tfoot> -->
                 
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    </form>

@stop


@section('inputjs')
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script> -->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
{{ HTML::script('js/jquery.js') }}
{{ HTML::script('js/bootstrap.min.js') }}
 {{ HTML::script('js/sb-admin-2.js') }}
  {{ HTML::script('js/plugins/metisMenu/metisMenu.min.js') }}
<script type="text/javascript" src="https://cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">

<script>
    //
// Updates "Select all" control in a data table
//
function updateDataTableSelectAllCtrl(table){
   var $table             = table.table().node();
   var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
   var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
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

$(document).ready(function (){


   // Array holding selected row IDs
   var rows_selected = [];
   var table = $('#dataTables-result').DataTable({
      'ajax': "{{ URL::to('transaction/listingbulk?'.http_build_query(Input::all())) }}",
      'columnDefs': [{
         'targets': 0,
         'searchable':false,
         'orderable':false,
         // 'width':'1%',
         'className': 'dt-body-center',
         'render': function (data, type, full, meta){
             return '<input type="checkbox">';
         }
      }],
      'order': [1, 'desc'],
      "columns" : [
            { "data" : "id", "className" : "text-center" },
            { "data" : "id", "className" : "text-center" },
            { "data" : "transaction_date" },
        
            { "data" : "buyer_username", "className": "" },
            { "data" : "Platform", "className": "" },
            { "data" : "total_amount", "className" : "" },
            { "data" : "delivery_state", "className" : "text-center" },
            { "data" : "status", "className" : "text-center" },
           

            
        ],
      'rowCallback': function(row, data, dataIndex ){
         // Get row ID
         var rowId = row.id;

         // If row ID is in the list of selected row IDs
         if($.inArray(rowId, rows_selected) !== -1){
            $(row).find('input[type="checkbox"]').prop('checked', true);
            $(row).addClass('selected');
         }
      }
   });

   // Handle click on checkbox
   $('#dataTables-result tbody').on('click', 'input[type="checkbox"]', function(e){
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
   $('#dataTables-result').on('click', 'tbody td, thead th:first-child', function(e){
      $(this).parent().find('input[type="checkbox"]').trigger('click');
   });

   // Handle click on "Select all" control
   $('thead input[name="select_all"]', table.table().container()).on('click', function(e){
      if(this.checked){
         $('#dataTables-result tbody input[type="checkbox"]:not(:checked)').trigger('click');
      } else {
         $('#dataTables-result tbody input[type="checkbox"]:checked').trigger('click');
      }

      // Prevent click event from propagating to parent
      e.stopPropagation();
   });

   // Handle table draw event
   table.on('draw', function(){
      // Update state of "Select all" control
      updateDataTableSelectAllCtrl(table);
   });

   $('#submit').on('click', function(){

       var $table  = table.table().node();
       var chkbox_select_all  = $('thead input[name="select_all"]', $table);
        var $row = $table.closest('tr');
      // var $row = $table.closest('tbody');

       console.log($row);

      // alert(chkbox_select_all);

       $.each(chkbox_select_all, function(index){
         // Create a hidden element 
         // alert(index);
           console.log(index);
      });


   });
  
    $('#approve').on('click', function () {
      
      var data = table.rows('.selected').data();
      var datacheck = data.length; 
      // console.log(table.rows('.selected').data());

      if(datacheck > 0){
        var newarray=[];       
              for (var i=0; i < data.length ;i++){
                
                  // console.log("Name: " + data[i]['id'] + " Address: " + data[i][1] + " Office: " + data[i][2]);
                 newarray.push(data[i]['id']);
                 // newarray.push(data[i][1]);
                 
              }

        var sData = newarray.join();

        $.ajax({
                method: "POST",
                url: "/transaction/bulkapprove",
                data: {
                    'sData':sData
                },
                beforeSend: function(){
                $('.loading').show();
                    console.log('Migrating..');
                },
                success: function(data) {
                    console.log(data);

                    $('.loading').hide();   
                    alert('Approved Successfully!.');
                    if(data.respStatus == 0){ 
                    }

                    table.ajax.reload(null,false);

                }
          })


      }
      else {
        alert('No Row selected');
      }

      

    });

    $('#cancel').on('click', function () {
      
      var data = table.rows('.selected').data();
      var datacheck = data.length; 
      // console.log(table.rows('.selected').data());

      if(datacheck > 0){
        var newarray=[];       
              for (var i=0; i < data.length ;i++){
                
                 newarray.push(data[i]['id']);
                 // newarray.push(data[i][1]);
                 
              }

        var sData = newarray.join();

        $.ajax({
                method: "POST",
                url: "/transaction/bulkcancel",
                data: {
                    'sData':sData
                },
                beforeSend: function(){
                $('.loading').show();
                    console.log('Migrating..');
                },
                success: function(data) {
                    console.log(data);
                    $('.loading').hide();
                    alert('Cancelled Successfully!.');

                    if(data.respStatus == 1){ 
                    }
                    table.ajax.reload(null,false);    

                }
          })


      }
      else {
        alert('No Row selected');
      }

      

    });

   // Handle form submission event 
   $('#frm-trans').on('submit', function(e){
      var form = this;
      // alert('transaction');
      // Iterate over all selected checkboxes
      // alert(form);
      // $('#submit').val();
      // console.log($('#submit').val());
      console.log();
       var rows_selected = table.column(0).checkboxes.selected();

      $.each(rows_selected, function(index, rowId){
         // Create a hidden element 
        // console.log(rowId);

         $(form).append(
             $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'id[]')
                .val(rowId)
         );
      });

      console.log(form);

      // FOR DEMONSTRATION ONLY     
    
            $('#frm-trans').attr('action', "/transaction/new");
            $("#frm-trans").submit();
           //  e.preventDefault();
       
      // Prevent actual form submission
      // e.preventDefault();
   });
});



</script>
@stop

   

