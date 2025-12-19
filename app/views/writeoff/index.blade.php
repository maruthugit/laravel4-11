@extends('layouts.master')
@section('title') Write Off Stock @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12 page-header">
            <div class="col-md-2 pull-left"><h3><i class="fa fa-trash-o"></i> Write Off Stock</h3></div>
            <div class="col-md-2 pull-right" style="padding: 10px;text-align: right;" data-toggle="modal" data-target="#myModal">
                <a class="btn btn-default"><i class="fa fa-pencil"></i> Create Write Off</a>
            </div>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-striped" id="list-write-off">
                <thead>
                    <th>Application No</th>
                    <th>Requestor By</th>
                    <th>Approved By / Rejected By</th>
                    <th>Received By</th>
                    <th>Item Summary</th>
                    <th>Status</th>
                    <th>File</th>
                    <th>Action</th>
                </thead>
                <tbody></tbody>
          </table>
        </div>        
    </div>
    <!-- Modal -->
    <div class="row">
        <!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">New Write Off</h4>
      </div>
      <div class="modal-body">
          <div class="row">
          <div class="col-md-12 col-xs-12">
              <div class="col-md-12 col-xs-12">
              <form>
                
                <div class="form-group">
                    <div class="panel panel-default">
  <div class="panel-body">
       <div class="row">
                        
                        <div class="row" style="padding:15px;">
                        <div class="col-md-4 pull-right" style="margin-bottom:15px;">
                            <div class="input-group">
                                <input type="text" class="form-control" id="transaction_from" placeholder="" aria-describedby="basic-addon1">
                                <span class="input-group-addon" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        </div>
                        <hr>
                        <div class="col-md-12" style="">
                            <div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle"></i> Please insert registered SKU in warehouse.</div>
                        </div>
                        <div class="row"  style="padding:15px;">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon1">Product ID</span>
                                <input type="text" id="product_id" class="form-control" placeholder="" aria-describedby="basic-addon1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon1">Quantity</span>
                                <input type="text" id="quantity" class="form-control" placeholder="" aria-describedby="basic-addon1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon1">Expire Date</span>
                                <input type="text" id="expired_date" class="form-control" placeholder="" aria-describedby="basic-addon1">
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top:15px;">
                            <a class="btn btn-block btn-default pull-right" v-on:click="addProduct()">Add </a>
                        </div>
                        </div>
                    </div>
  </div>
</div>
                 
                </div>
                <div class="form-group">
                  <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped">
                                <thead>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Expired Date</th>
                                    <th>Quantity</th>
                                    <th></th>
                                </thead>
                              <tbody>
                                  <tr  v-for="(item, index) in selectedProduct">
                                    <td>@{{item.name}}<br>@{{item.label}}</td>
                                    <td>@{{item.sku}}</td>
                                    <td>@{{item.expired_date}}</td>
                                    <td>@{{item.quantity}}</td>
                                    <td><a ><i v-on:click="RemoveProduct(index)" class="fa fa-2x fa-trash-o"></i></a></td>
                                  </tr>
                              </tbody>

                            </table>
                        </div>
                  </div>
                  
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Reason for goods write off :</label>
                  <textarea class="form-control"rows="5" id="remarks" style="font-style: italic" placeholder="Must be a good reason .."></textarea>
                </div>
            </form>
                </div>
          </div>
           </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" v-on:click="savePost" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>


<!-- Edit Modal -->
<div class="modal fade" id="edit-write-off" tabindex="-1" role="dialog" aria-labelledby="edit-write-off">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Write Off</h4>
      </div>
      <div class="modal-body">
          <div class="row">
          <div class="col-md-12 col-xs-12">
              <div class="col-md-12 col-xs-12">
              <form>
                
                <div class="form-group">
                    <div class="panel panel-default">
  <div class="panel-body">
       <div class="row">
                        
                        <div class="row" style="padding:15px;">
                        <div class="col-md-4 pull-right" style="margin-bottom:15px;">
                            <div class="input-group">
                                <input type="text" v-model="EditDocDate" class="form-control" id="edit_write_off_date" placeholder="" aria-describedby="basic-addon1">
                                <span class="input-group-addon" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        </div>
                        <hr>
                        <div class="col-md-12" style="">
                            <div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle"></i> Please insert registered SKU in warehouse.</div>
                        </div>
                        <div class="row"  style="padding:15px;">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon1">Product ID</span>
                                <input type="text" id="edit_product_id" class="form-control" placeholder="" aria-describedby="basic-addon1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon1">Quantity</span>
                                <input type="text" id="edit_quantity" class="form-control" placeholder="" aria-describedby="basic-addon1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon1">Expire Date</span>
                                <input type="text" id="edit_expired_date" class="form-control" placeholder="" aria-describedby="basic-addon1">
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top:15px;">
                            <a class="btn btn-block btn-default pull-right" v-on:click="AddEditProduct()">Add </a>
                        </div>
                        </div>
                    </div>
  </div>
</div>
                 
                </div>
                <div class="form-group">
                  <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped">
                                <thead>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Expired Date</th>
                                    <th>Quantity</th>
                                    <th></th>
                                </thead>
                              <tbody>
                                  <tr  v-for="(item, index) in EditSelectedProduct">
                                    <td>@{{item.name}}<br>@{{item.label}}</td>
                                    <td>@{{item.sku}}</td>
                                    <td>@{{item.expired_date}}</td>
                                    <td>@{{item.quantity}}</td>
                                    <td><a ><i v-on:click="EditRemoveProduct(index)" class="fa fa-2x fa-trash-o"></i></a></td>
                                  </tr>
                              </tbody>

                            </table>
                        </div>
                  </div>
                  
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Reason for goods write off :</label>
                  <textarea v-model="EditRemarks"  class="form-control"rows="5" id="edit_remarks" style="font-style: italic" placeholder="Must be a good reason .."></textarea>
                </div>

                <div class="row" style="padding-bottom: 15px;">
                    <div class="col-md-6">
                        <div class="input-group"><span id="basic-addon1" class="input-group-addon">Approved BY</span> 
                        <input type="text" v-model="EditApproved"  id="edit_approval" placeholder="" aria-describedby="basic-addon1" class="form-control">
                        </div>
                    </div> 
                    <div class="col-md-6">
                        <div class="input-group"><span id="basic-addon1" class="input-group-addon">Received BY</span> 
                            <input type="text" v-model="EditReceived"  id="edit_receiver" placeholder="" aria-describedby="basic-addon1" class="form-control">
                        </div>
                    </div> 
            </form>
                </div>
          </div>
           </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" v-on:click="saveUpdatePost()" class="btn btn-primary">Save Update</button>
      </div>
    </div>
  </div>
</div>
<!-- Edit Modal -->
</div>
<!-- Upload Modal -->


<div class="modal fade" id="upload-write-off-modal" tabindex="-1" role="dialog" aria-labelledby="upload-write-off">
  <div class="modal-dialog " role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Upload Scan Copy</h4>
      </div>
      <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
                    <form>
                        <div role="alert" class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Once file uploaded no information will be availabel for update</div>
                        <div class="form-group">
                            <label for="exampleInputFile">Please attached copy with actual signature</label>
                            <input type="file" accept="application/pdf"  id="file">
                        </div>
                        
                </form>
            </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button"  v-on:click="uploadPDF()" class="btn btn-primary">Upload</button>
      </div>
    </div>
  </div>
</div>


<!-- Upload Modal -->


</div>

</div>
@stop
@section('inputjs')

<script>
    $(document).ready(function(){


        
    var nM = new Vue({
	el: '#page-wrapper',
	data: {
            'selectedProduct':[],
            'EditSelectedProduct':[],
            'EditID':'',
            'EditDocDate':'',
            'EditRemarks':'',
            'EditApproved':'',
            'EditReceived':'',
            'UploadID':''
	    },
        mounted:function() {
            $('#transaction_from').datetimepicker({
                format: 'YYYY-MM-DD'
            });

            $('#expired_date').datetimepicker({
                format: 'YYYY-MM-DD'
            });


            $('#edit_write_off_date').datetimepicker({
                format: 'YYYY-MM-DD'
            });

            $('#edit_expired_date').datetimepicker({
                format: 'YYYY-MM-DD'
            });
        },
        watch: {
            
        },
        methods: {
            readyUI:function(){
              
            },
            viewEditPost: function(id){

                var vueapp = this;
                vueapp.EditID = id;
                $("#edit-write-off").modal("show");

                $.ajax({
                    type: 'GET',
                    url: '/warehouse/getwriteoff/'+id,
                    data: { },
                    dataType: "json",
                    success: function(resultData) { 
                        console.log(resultData);
    
                        vueapp.EditRemarks = resultData.write_off_details.remarks;
                        vueapp.EditApproved = resultData.write_off_details.approved_by;
                        vueapp.EditReceived = resultData.write_off_details.received_by;

                        vueapp.EditSelectedProduct = resultData.write_off_details;
                        vueapp.EditDocDate = resultData.write_off_info.doc_date;
                        vueapp.EditRemarks = resultData.write_off_info.remarks;
                        vueapp.EditApproved = resultData.write_off_info.approved_by;
                        vueapp.EditReceived = resultData.write_off_info.received_by;
                    }
                });
            },
            updateInfo: function(){

                var objData = this;
                
                var doc_date =  $("#edit_write_off_date").val();
                var remarks = $("#edit_remarks").val();
                var products = objData.selectedProduct;

                $.ajax({
                    type: 'POST',
                    url: '/warehouse/storewriteoff',
                    data: {
                        "doc_date" :doc_date,
                        "remarks" :remarks,
                        "products" :products,
                    },
                    dataType: "json",
                    success: function(resultData) { 
                        alert(resultData.message);
                        console.log(resultData.message);
                        $("#myModal").modal("hide");
                    }
                });
                
            },
            approvePost: function(id){

                var txt;
                var id = id;
                var r = confirm("Are you sure to approve? ");
                if (r == true) {
                    //txt = "You pressed OK!";
                    $.ajax({
                        type: 'POST',
                        url: '/warehouse/approvewriteoff',
                        data: {
                            "id" :id
                        },
                        dataType: "json",
                        success: function(resultData) { 
                            alert(resultData.message);
                            location.reload();
                        }
                    });
                } else {
                   
                }
                //alert(txt);
                
            },

            rejectPost: function(id){

                var txt;
                var id = id;
                var r = confirm("Are you sure to reject? ");
                if (r == true) {
                    $.ajax({
                        type: 'POST',
                        url: '/warehouse/rejectwriteoff',
                        data: {
                            "id" :id
                        },
                        dataType: "json",
                        success: function(resultData) { 
                            alert(resultData.message);
                            location.reload();
                        }
                    });
                } else {
                }

            },
            savePost: function(){

                var objData = this;
                
                var doc_date =  $( "#transaction_from" ).val();
                var remarks = $("#remarks").val();
                var products = objData.selectedProduct;

            
                $.ajax({
                    type: 'POST',
                    url: '/warehouse/storewriteoff',
                    data: {
                        "doc_date" :doc_date,
                        "remarks" :remarks,
                        "products" :products,
                    },
                    dataType: "json",
                    success: function(resultData) { 
                        alert(resultData.message);
                        console.log(resultData.message);
                        $("#myModal").modal("hide");
                        $('#list-write-off').DataTable().ajax.reload(); 
                    }
                });
            
                
            },
            saveUpdatePost: function(){

                var objData = this;
                var doc_date =  $( "#edit_write_off_date").val();
                var remarks = $("#edit_remarks").val();
                var products = objData.EditSelectedProduct;
                var approved_by = $( "#edit_approval").val();
                var received_by = $( "#edit_receiver").val();
                var id = objData.EditID;
            
                $.ajax({
                    type: 'POST',
                    url: '/warehouse/updatewriteoff',
                    data: {
                        "id" :id,
                        "approved_by" :approved_by,
                        "received_by" :received_by,
                        "doc_date" :doc_date,
                        "remarks" :remarks,
                        "products" :products,
                    },
                    dataType: "json",
                    success: function(resultData) { 
                       
                        console.log(resultData.message);
                        $("#edit-write-off").modal("hide");
                        $('#list-write-off').DataTable().ajax.reload(); 
                        alert(resultData.message);
                    }
                });
                
            },
            uploadPDF: function(){
                //stop submit the form, we will post it manually.
                event.preventDefault();

                // Get form
                var form = $('#file')[0];
                console.log(form);

                // Create an FormData object 
                var data = new FormData(form);
                var file = $( '#file' ).get( 0 ).files[0];
                id = this.UploadID ;
                // If you want to add an extra field for the FormData
                data.append("id", id);
                data.append( 'file', file );

                // disabled the submit button

                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: "/warehouse/uploaddoc",
                    data: data,
                    processData: false,
                    contentType: false,
                    cache: false,
                    timeout: 600000,
                    success: function (data) {
                       alert(data.message);
                       if(data.error !== true){
                        $("#upload-write-off-modal").modal("hide");
                        this.UploadID = '';
                        $('#list-write-off').DataTable().ajax.reload();   
                       }
                    },
                    error: function (e) {
                        console.log("ERROR : ", e);
                    }
                });


            },
            uploadPost: function(id){
                $("#upload-write-off-modal").modal("show");
                this.UploadID = id;
            },
            AddEditProduct: function(){

                var objData = this;

                var id =  $( "#edit_product_id").val();
                var quantity =  $( "#edit_quantity" ).val();
                var expired_date =  $( "#edit_expired_date" ).val();

                //if(parseInt(id) > 0 ){
                    $.ajax({
                        type: 'GET',
                        url: '/warehouse/checkproduct/'+id,
                        data: {
                            "id" :id,
                        },
                        dataType: "json",
                        success: function(resultData) { 

                            if(resultData === 0){
                                alert("No valid product found in the warehouse!")
                            }else{
                                (objData.EditSelectedProduct).push({
                                    "name":resultData.name,
                                    "product_id":resultData.product_id,
                                    "sku":resultData.sku,
                                    "quantity":quantity,
                                    "expired_date":expired_date,
                                });

                                console.log(objData.EditSelectedProduct);
                            }
                            
                        }
                    });
                //}
            },
            addProduct: function(){

                var objData = this;
                
                var id =  $( "#product_id").val();
                var quantity =  $( "#quantity" ).val();
                var expired_date =  $( "#expired_date" ).val();

                //if(parseInt(id) > 0 ){
                    $.ajax({
                        type: 'GET',
                        url: '/warehouse/checkproduct/'+id,
                        data: {
                            "id" :id,
                        },
                        dataType: "json",
                        success: function(resultData) { 

                            if(resultData === 0){
                                alert("No valid product found in the warehouse!")
                            }else{
                                (objData.selectedProduct).push({
                                    "name":resultData.name,
                                    "product_id":resultData.product_id,
                                    "sku":resultData.sku,
                                    "quantity":quantity,
                                    "expired_date":expired_date,
                                });

                                console.log(objData.selectedProduct);
                            }
                            
                        }
                    });
                //}
            },
            RemoveProduct: function(index){
                this.$delete(this.selectedProduct, index);
            },
            EditRemoveProduct: function(index){
                this.$delete(this.EditSelectedProduct, index);
            },
        },
        created: function(){
            //this.getPost()
        }
    });


  


    $('body').on('click', '.edit-write-off', function(){
        var id = $(this).attr("data-id");
        nM.viewEditPost(id);
    });

    $('body').on('click', '.approve-write-off', function(){
        console.log($(this).attr("data-id"));
        var id = $(this).attr("data-id");
        nM.approvePost(id);
    });

    $('body').on('click', '.reject-write-off', function(){
        console.log($(this).attr("data-id"));
        var id = $(this).attr("data-id");
        nM.rejectPost(id);
    });

    $('body').on('click', '.upload-write-off', function(){
        var id = $(this).attr("data-id");
        nM.uploadPost(id);
    });

    

// upload-write-off
    
    
    
    // if($('#dataTables-posts').length > 0){
    
        $('#list-write-off').dataTable({
            "autoWidth" : false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{ URL::to('warehouse/listwriteoff') }}",
            "order" : [[0,'desc']],
            "columnDefs" : [{
                "targets" : "_all",
                "defaultContent" : ""
            }],
            "columns" : [
                { data: 'doc_no', name: 'doc_no' },
                { data: 'prepared_by', name: 'prepared_by' },
                { data: 'approved_by', name: 'approved_by' },
                { data: 'received_by', name: 'received_by' },
                { data: 'item_summary', name: 'item_summary' },
                { data: 'status', name: 'status' },
                { data: 'file', name: 'file' },
                { data: 'action', name: 'action' },
            ]
        });
    
    // }
    
 
    
    
    
});
</script>

@stop