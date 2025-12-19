@extends('layouts.master')

@section('title') Create Region @stop

@section('content')

<div id="page-wrapper">

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Create Region</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>



    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-lock"></i> Region Information </h2>
        </div>
        <div class="panel-body">
            <form class="form-horizontal col-md-6 col-xs-12" method="post" action="/region/save" enctype="multipart/form-data">>
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Region Name</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="" name="region_name" placeholder="">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputPassword3" class="col-sm-2 control-label">Region Code</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control"  name="region_code" id="" placeholder="">
                  </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control"  name="region_email" id="" placeholder="">
                      <span class="help-block">Email person in charge</span>
                    </div>
                </div>
                 <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Image Thumbnail</label>
                    <div class="col-sm-10">
                        <input type="file" name="img_thumb" class="form-control input-sm">
                        <span class="help-block">Size : {{512}}px (W) x {{512}}px (H)</span>   
                    </div>
                </div>
                <div class="form-group">
                  <label for="inputPassword3" class="col-sm-2 control-label">Country</label>
                  <div class="col-sm-10">
                        <select class="form-control" id="select-country" name="region_country">
                            <option value="">-Select Country-</option>
                            <?php foreach ($countries as $key => $value) { ?>
                            <option value="<?php echo $value->id;?>"><?php echo $value->name;?></option>
                            <?php } ?>
                        </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputPassword3" class="col-sm-2 control-label">States</label>
                  <div class="col-sm-10">
                      <div id="checkbox-state" >
                        
                      </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default">Save Region</button>
                  </div>
                </div>
            </form>
        </div>

    </div>
    
</div>

@stop
@section('inputjs')
<script>
    
    $( document ).ready(function() {
        
        $("#select-country").change(function(){
           
           $.ajax({
                method: "POST",
                url: "/region/states",
                data: {
                    'country_id':$("#select-country").val()
                },
                beforeSend: function(){
                $('.loading').show();
                    console.log('Migrating..');
                },
                success: function(data) {
                   // var obj = JSON.parse(data);
                    var obj = data;
                    var str = '';
                    $.each(obj.data.states, function (index, value) {
                        var disable = '';
                        var region = '';
                        if(value.region_id > 0){ 
                            var disable ='disabled'; 
                            var region = ' <strong>('+value.region_code+')</strong>'; 
                        }
                        str = str + '<div><input name="region_states[]" value="'+value.id+'" type="checkbox" '+disable+'> '+value.name+region+' </div>'
                    });
                    
                    $("#checkbox-state").html(str);
                   
                }
          })
           
        });
        
    });
    
</script>
@stop