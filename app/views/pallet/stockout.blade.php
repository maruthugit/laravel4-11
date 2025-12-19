@extends('layouts.master')

@section('title', 'Stock')

@section('content')


<script type="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Stock Out
                <span class="pull-right">
                    <a class="btn btn-default" href="{{ url('pallet') }}"><i class="fa fa-reply"></i></a>
                </span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-xing"></i> Pallet  Details</h3>
                </div>


                <div class="panel-body">
                    {{ Form::open(['url' => 'pallet/stockout', 'method' => 'post' ,'id'=>'form-id']) }}
                        <div class="form-horizontal">


<div id="myRadioGroup">
                          <div class="form-group">
                        
                      <label class="col-lg-2 control-label">Return</label>
                      <div class="col-lg-3">
                      <input type="radio" name="type" value="return" id='watch-me' required="required" >
                            
                      </div>
                    </div>

                          <div class="form-group ">
                        
                      <label class="col-lg-2 control-label">Write Off </label>
                      <div class="col-lg-3">
                      <input type="radio" name="type" value="writeoff" required="required" >
                            
                      </div>
                    </div>

                          <div class="form-group ">
                        
                      <label class="col-lg-2 control-label">Transfer </label>
                      <div class="col-lg-3">
                      <input type="radio" name="type" value="transfer" required="required" >
                            
                      </div>
                    </div>
                           
<div id="Cars2" class="desc d_1"  style="display: none;">
                         <div class="form-group required {{ $errors->first('pallet_code', 'has-error') }}">
                                <label class="col-lg-2 control-label">Pallet </label>
                                <div class="col-lg-3">
             <select class="form-control"  id="pp_id" name="pp_id" ><option value="">Choose Pallet</option>
                                         
                                         <?php foreach ($pallets as $key => $value) { ?>
                                         <option value="<?php echo $value->id; ?>" 
                                          >
                                          <?php echo $value->pallet_code;?>
                                            
                                          </option>
                                             <?php } ?>


                                         </select>                                     
                                </div>
                            </div>


                               <div class="form-group {{ $errors->first('supplier_id', 'has-error') }}">
                 <label class="col-lg-2 control-label">Supplier </label>

                 <div class="col-lg-3">
                 <select class="form-control" id="supplier_id" name="supplier_id" >
                                           <?php if(Session::get('branch_access') != 1){?>
                                              <option value="">Supplier</option>
                                                    <?php } ?>
                                             <?php foreach ($suppliers as $key => $value)  { ?>
                                             <option value="<?php echo $value->id; ?>" ><?php echo $value->supplier_name; ?></option>
                                                    <?php  } ?>
                                          </select>
                                        </div>
            </div>



                            <div class="form-group {{ $errors->first('dt', 'has-error') }}">
                                     <label  class="col-lg-2 control-label"> Date</label>
                                     <div class="col-lg-2">
                                    <div class='input-group date' id='datetimepicker3'>
                                      
                                        <input type='text' class="form-control" name="dt" value="<?php echo (Input::get('date')); ?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                 </div>
                                </div>
                            </div>


                           
                             <div class="form-group required {{ $errors->first('qty', 'has-error') }}">
                                <label class="col-lg-2 control-label">Quantity </label>
                                <div class="col-lg-3">
                                    {{ Form::text('qty', null, ['class' => 'form-control','id'=>'qty']) }}
                                    {{ $errors->first('qty', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>


                               

                         <div class="form-group required {{ $errors->first('rm', 'has-error') }}">
                                <label class="col-lg-2 control-label">Remarks </label>
                                <div class="col-lg-3">
                                    {{ Form::textarea('rm', null, ['class' => 'form-control','id'=>'rm']) }}
                                    {{ $errors->first('rm', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                     
</div>
<br>
<div id="Cars3" class="desc d_2" style="display: none;">


 <div class="form-group required">
                                <label class="col-lg-2 control-label">Pallet </label>
                                <div class="col-lg-3">
             <select class="form-control"  id="pp_id1" name="pp_id1" ><option value="">Choose Pallet</option>
                                         <?php foreach ($pallets as $key => $value) { ?>
                                         <option value="<?php echo $value->id; ?>" 
                                          >
                                          <?php echo $value->pallet_code;?>
                                            
                                          </option>
                                             <?php } ?>


                                         </select>                                     
                                </div>
                            </div>


                            

                               <div class="form-group">
                 <label class="col-lg-2 control-label">Supplier </label>

                 <div class="col-lg-3">
                 <select class="form-control" id="sp" name="sp" >
                                           <?php if(Session::get('branch_access') != 1){?>
                                              <option value="">Supplier</option>
                                                    <?php } ?>
                                             <?php foreach ($suppliers as $key => $value)  { ?>
                                             <option value="<?php echo $value->id; ?>" ><?php echo $value->supplier_name; ?></option>
                                                    <?php  } ?>
                                          </select>
                                        </div>
            </div>

                       



                           
                            <div class="form-group required {{ $errors->first('quantity', 'has-error') }}">
                                <label class="col-lg-2 control-label">Quantity </label>
                                <div class="col-lg-3">
                                    {{ Form::text('qty1', null, ['class' => 'form-control','id'=>'qty']) }}
                                    {{ $errors->first('qty1', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            
                               <div class="form-group {{ $errors->first('dat', 'has-error') }}">
                                     <label  class="col-lg-2 control-label"> Date</label>
                                     <div class="col-lg-2">
                                    <div class='input-group date' id='datetimepicker5'>
                                      
                                        <input type='text' class="form-control" name="dat" value="<?php echo (Input::get('date')); ?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                 </div>
                                </div>
                            </div>

                               

                         <div class="form-group required {{ $errors->first('pallet_Description', 'has-error') }}">
                                <label class="col-lg-2 control-label">Remarks </label>
                                <div class="col-lg-3">
                                    {{ Form::textarea('rem', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('rem', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                     
</div>

                         <div id="Cars4" class="desc d_3" style="display: none;">
                        
 <div class="form-group required">
                                <label class="col-lg-2 control-label">Pallet </label>
                                <div class="col-lg-3">
             <select class="form-control"  id="pp_id2" name="pp_id2" ><option value="">Choose Pallet</option>
                                         <?php foreach ($pallets as $key => $value) { ?>
                                         <option value="<?php echo $value->id; ?>" 
                                          >
                                          <?php echo $value->pallet_code;?>
                                            
                                          </option>
                                             <?php } ?>


                                         </select>                                     
                                </div>
                            </div>




                               <div class="form-group">
                 <label class="col-lg-2 control-label">Supplier </label>

                 <div class="col-lg-3">
                 <select class="form-control" id="supplier_id2" name="supplier_id2" ><option value="">Choose Pallet</option>
                                           <?php if(Session::get('branch_access') != 1){?>
                                              <option value="">Supplier</option>
                                                    <?php } ?>
                                             <?php foreach ($suppliers as $key => $value)  { ?>
                                             <option value="<?php echo $value->id; ?>" ><?php echo $value->supplier_name; ?></option>
                                                    <?php  } ?>
                                          </select>
                                        </div>
            </div>




                            <div class="form-group required {{ $errors->first('quantity', 'has-error') }}">
                                <label class="col-lg-2 control-label">Quantity </label>
                                <div class="col-lg-3">
                                    {{ Form::text('quantity', null, ['class' => 'form-control','id'=>'quantity']) }}
                                    {{ $errors->first('quantity', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                               

                        

                               <div class="form-group required {{ $errors->first('from', 'has-error') }}">
                                <label class="col-lg-2 control-label">From </label>
                                <div class="col-lg-3">
                                    {{ Form::text('from', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('from', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                               

                         <div class="form-group required {{ $errors->first('to', 'has-error') }}">
                                <label class="col-lg-2 control-label">To </label>
                                <div class="col-lg-3">
                                    {{ Form::text('to', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('to', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                              <div class="form-group {{ $errors->first('expired_date', 'has-error') }}">
                                     <label  class="col-lg-2 control-label"> Date</label>
                                     <div class="col-lg-2">
                                    <div class='input-group date' id='datetimepicker4'>
                                      
                                        <input type='text' class="form-control" name="date2" value="<?php echo (Input::get('date')); ?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                 </div>
                                </div>
                            </div>

                             <div class="form-group required {{ $errors->first('pallet_Description', 'has-error') }}">
                                <label class="col-lg-2 control-label">Remarks </label>
                                <div class="col-lg-3">
                                    {{ Form::textarea('remarks', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('remarks', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                     
</div>

                     <br>     

  <div class="form-group">
                                <div class="col-lg-3 col-lg-offset-2">
                                    <input class="btn btn-default" type="reset" value="Reset">
                                   
                                        <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save</button>
                                     @if (Permission::CheckAccessLevel(Session::get('role_id'), 21, 3, 'AND'))
                                    @endif
                                </div>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
<script type="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

<script src="/js/angular_modified.js"></script>


    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <!-- Bootstrap Date-Picker Plugin -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>  

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://code.jquery.com/jquery-3.1.0.js"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.min.js"></script>
<script type="text/javascript">
    
    jQuery(function($){
    
    $('#form-id').validate({
        debug: false,
        errorElement: "span",
        errorClass: "help-block",
        rules: {
            "pp_id": {
                required : true
            },
            "supplier_id": {
                required : true
            },
            "dt": {
                required : true
            },
            "qty": {
                required : true
            },
            "rm": {
                required : true
            },
            "pp_id1": {
                required : true
            },
            "sp": {
                required : true
            },
            "qty1": {
                required : true
            },
            "pp_id2": {
                required : true
            },
            "supplier_id2": {
                required : true
            },
            "quantity": {
                required : true
            }
           
            
        }
    });
    
});
</script>

@section('script')




    $('body').on('change', '#pp_id', function() {


        loadOptionSupplier($(this).val());
        console.log(pp_id)
      
        
    });

    function loadOptionSupplier(palletID){
       


        $.ajax({
                method: "get",
                url:"/pallet/country",
                dataType:'json',
                data: {
                    'pallet_id':palletID
                },
                error: function(xhr, status, error) {
            alert(status);
            alert(xhr.responseText);
        },

             
                beforeSend: function(){
                },
                success: function(data) {
               
                    console.log(data.data.suppliers);
                    var suppliersList = data.data.suppliers;
                    var str;
                    $.each(suppliersList, function (index, value) {
                        str = str + "<option value='"+value.id+"'>"+value.supplier_name+"</option>";
                       console.log(str);
                    });
                    $("#supplier_id").html(str);
                    $("#sp").html(str);
                    $("#supplier_id2").html(str);
                  
                    
                }
          })
      

      
        
    }

      $('body').on('change', '#pp_id1', function() {


        loadOptionSupplier($(this).val());
        console.log(pp_id1)
      
        
    });

     $('body').on('change', '#pp_id2', function() {


        loadOptionSupplier($(this).val());
        console.log(pp_id2)
      
        
    });

   


     




 $(document).ready(function() {
    


    $('#datetimepicker3').datetimepicker({
        format: 'YYYY-MM-DD'
    });
     $('#datetimepicker4').datetimepicker({
        format: 'YYYY-MM-DD'
    });
     $('#datetimepicker5').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    
});



$("input[name='type']").click(function () {
    $('#Cars2').css('display', ($(this).val() === 'return') ? 'block':'none');
     $('#Cars3').css('display', ($(this).val() === 'writeoff') ? 'block':'none');
      $('#Cars4').css('display', ($(this).val() === 'transfer') ? 'block':'none');
});



    var msg = '{{Session::get('alert')}}';
    var exist = '{{Session::has('alert')}}';
    if(exist){
      alert(msg);
    }


    

@stop
