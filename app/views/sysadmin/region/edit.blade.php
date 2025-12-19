@extends('layouts.master')

@section('title') Edit Region @stop

@section('content')

<div id="page-wrapper">

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Edit Region</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>



    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-lock"></i> Region Information </h2>
        </div>
        <div class="panel-body">
            <form class="form-horizontal col-md-6 col-xs-12" method="post" action="/region/save" enctype="multipart/form-data">
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Region Name</label>
                  <div class="col-sm-10">
                      <input type="hidden" class="form-control" id="" name="region_id" placeholder="" value="<?php echo $region->id; ?>">
                      <input type="text" class="form-control" id="" name="region_name" placeholder="" value="<?php echo $region->region; ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputPassword3" class="col-sm-2 control-label">Region Code</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control"  name="region_code" id="" placeholder="" value="<?php echo $region->region_code; ?>">
                  </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control"  value="<?php echo $region->email_pic; ?>" name="region_email" id="" placeholder="">
                      <span class="help-block">Email person in charge</span>
                    </div>
                </div>
                <div class="form-group">   
                    <label for="inputPassword3" class="col-sm-2 control-label">Image Thumbnail</label>
                    <div class="col-sm-10">
                      <br>
                        <img style="background-color: #e8e8e8;" src="/images/region/{{$region->img_thumb}}" alt="" class="img-responsive center-block"> 
                      <br>
                        <input type="file" name="img_thumb" class="form-control input-sm">
                        <span class="help-block">Size : {{512}}px (W) x {{512}}px (H)</span>   
                    </div>
                </div>
                <div class="form-group">
                  <label  class="col-sm-2 control-label">Country</label>
                  <label  class="col-sm-10 control-label" style="text-align: left;"><?php echo $region->name; ?> </label>
                </div>
                <div class="form-group">
                  <label for="inputPassword3" class="col-sm-2 control-label">States</label>
                  <div class="col-sm-10">
                      <div id="checkbox-state" >
                        <?php foreach ($states as $key => $value) {  ?>
                            <div><input name="region_states[]" value="<?php echo $value->id; ?>" type="checkbox" <?php if($value->region_id > 0 ){ echo  "checked"; } ?> <?php if($value->region_id != $region->id && $value->region_id > 0 ){ echo  "disabled"; } ?>> 
                                <?php echo $value->name; ?> <?php if($value->region_id > 0 ){  echo " - ".$value->region_code.""; } ?>
                            </div>
                        <?php } ?>
                      </div>
                  </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Status</label>
                    <div class="col-sm-10" style="padding-left: 0px;">
                        <label for="inputPassword3" class="col-sm-10 control-label" style="text-align: left;"> <input type="radio" value ="1" name="status" <?php echo $region->activation == 1? 'checked':''  ; ?>> Active  <input type="radio" value ="0" name="status" <?php echo $region->activation == 0? 'checked':''  ; ?>> Inactive</label>
                    </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10">
                      <a type="submit" class="btn btn-default" href="/region">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Region</button>
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
                    var obj = JSON.parse(data);
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