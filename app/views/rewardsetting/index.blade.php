@extends('layouts.master')
@section('title') Reward Setting @stop
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
        }
    .loading #load-message {
        width: 40px;
        height: 40px;
        position: absolute;
        left: 50%;
        right: 50%;
        bottom: 50%;
        top: 50%;
        margin: -20px;
    }
    
    .title-cat{
        color: #2daf73;
    }
    
    .box-cat{
        margin-bottom: 20px;
    }
    
    .box-con{
        padding:20px;
        border: solid 1px #ddd;
        cursor: pointer;
    }
    
    .box-con:hover{
        padding:20px;
        background-color: #f3f3f3;
        border: solid 1px #ddd;
    }
    
    #BYGM_panel{
        display: none;
    }
    
    #RFRR_panel{
        display: none;
    }
    
    #load-bmgm{
        display: none;
    }
</style>
<div class="loading"><span id="load-message"></span></div>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
           <h1 class="page-header">Reward Setting</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list-alt fa-fw"></i> Reward Setting </h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3 box-cat" id="thumb_BRTH_panel">
                    <div class="box-con">
                        <div style="display: inline-block">
                            <div><i class="fa fa-birthday-cake fa-3x"></i></div>
                         </div>
                         <div style="display: inline-block;padding-left: 10px;">
                            <div class="title-cat"><strong>Birthday Reward</strong></div>
                            <div><small>Surprise customers !</small></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 box-cat" id="thumb_BYGM_panel">
                    <div class="box-con">
                        <div style="display: inline-block">
                            <div><i class="fa fa-gamepad fa-3x"></i></div>
                         </div>
                         <div style="display: inline-block;padding-left: 10px;">
                            <div class="title-cat"><strong>Buy more get more</strong></div>
                            <div><small>Reward for loyal customers</small></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 box-cat" id="thumb_RFRR_panel">
                    <div class="box-con">
                        <div style="display: inline-block">
                            <div><i class="fa fa-users fa-3x"></i></div>
                         </div>
                         <div style="display: inline-block;padding-left: 10px;">
                            <div class="title-cat"><strong>Referrer Reward</strong></div>
                            <div><small>Reward our little agents</small></div>
                        </div>
                    </div>
                </div>
                <!--<div class="col-md-3 box-cat" id="thumb_NEWS_panel">-->
                <!--    <div class="box-con">-->
                <!--        <div style="display: inline-block">-->
                <!--            <div><i class="fa fa-newspaper-o fa-3x"></i></div>-->
                <!--         </div>-->
                <!--         <div style="display: inline-block;padding-left: 10px;">-->
                <!--            <div class="title-cat"><strong>Newsletter Reward</strong></div>-->
                <!--            <div><small>Give some tip! </small></div>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->
                <!--<div class="col-md-3 box-cat" id="thumb_JPNT_panel">-->
                <!--    <div class="box-con">-->
                <!--        <div style="display: inline-block">-->
                <!--            <div><i class="fa fa-cube fa-3x"></i></div>-->
                <!--         </div>-->
                <!--         <div style="display: inline-block;padding-left: 10px;">-->
                <!--            <div class="title-cat"><strong>Jocom Point</strong></div>-->
                <!--            <div><small>Set the rewards</small></div>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->
                <!--<div class="col-md-3 box-cat" id="thumb_BPNT_panel">-->
                <!--    <div class="box-con">-->
                <!--        <div style="display: inline-block">-->
                <!--            <div><i class="fa fa-cube fa-3x"></i></div>-->
                <!--         </div>-->
                <!--         <div style="display: inline-block;padding-left: 10px;">-->
                <!--            <div class="title-cat"><strong>B Infinite Point</strong></div>-->
                <!--            <div><small>Set the rewards</small></div>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->
            </div>
        </div>
    </div>
    <div class="panel panel-default form-panel" id="BYGM_panel">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list-alt fa-fw"></i> Buy more get more </h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-8  col-xs-12">
                    <form id="bmgm_form">
                        <div class="ui active inverted dimmer" id="load-bmgm"> <div class="ui medium text loader">Loading ..</div></div>
                        <div class="form-group row">
                            <label for="" class="col-md-2 col-form-label col-form-label">Module Activation</label>
                            <div class="col-md-10">
                                <input name="bmgm_is_activate" id="bmgm_is_activate" type="checkbox" class="" id="colFormLabelSm" placeholder="">
                                <small style="">Activate this module</small>
                            </div>
                        </div>
                        <h4 class="ui horizontal divider header">
                            <i class="tag icon"></i>
                            Reward Setting
                        </h4>
                        <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label">First Stage
                            <i class="fa fa-question-circle" data-container="body" data-toggle="popover" title="First Stage" data-placement="right" data-content="Set voucher cash amount / JPoint total and tick at the checkbox to set the setting available for reward"></i></label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1" style="min-width:100px;">MYR</span>
                                    <input type="text" name="bmgm_1_stage_amount" id="bmgm_1_stage_amount" class="form-control" placeholder="Ex: 250.00" aria-describedby="basic-addon1">
                                </div>
                                <div class="input-group" style="margin-top:10px;">
                                    <span class="input-group-addon" id="basic-addon1" style="min-width:100px;">Voucher </span>
                                    <input type="text" name="bmgm_1_stage_voucher_amount" id="bmgm_1_stage_voucher_amount" class="form-control" aria-label="..." placeholder="">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="bmgm_1_is_stage_voucher" id="bmgm_1_is_stage_voucher" aria-label="..." >
                                    </span>
                                </div>
                                
                                <div class="input-group" style="margin-top:10px;">
                                    <span class="input-group-addon" id="basic-addon1" style="min-width:100px;">JPoint</span>
                                    <input type="text" name="bmgm_1_stage_point" id="bmgm_1_stage_point" class="form-control" aria-label="..." placeholder="">
                                    <span class="input-group-addon">
                                      <input type="checkbox" name="bmgm_1_is_stage_point" id="bmgm_1_is_stage_point" aria-label="...">
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label">Second Stage
                            <i class="fa fa-question-circle" data-container="body" data-toggle="popover" title="Second Stage" data-placement="right" data-content="Set voucher cash amount / JPoint total and tick at the checkbox to set the setting available for reward"></i></label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1" style="min-width:100px;">MYR</span>
                                    <input type="text" name="bmgm_2_stage_amount" id="bmgm_2_stage_amount" class="form-control" placeholder="Ex: 250.00" aria-describedby="basic-addon1">
                                </div>
                                <div class="input-group" style="margin-top:10px;">
                                    <span class="input-group-addon" id="basic-addon1" style="min-width:100px;">Voucher </span>
                                    <input type="text" name="bmgm_2_stage_voucher_amount" id="bmgm_2_stage_voucher_amount" class="form-control" aria-label="..." placeholder="">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="bmgm_2_is_stage_voucher" id="bmgm_2_is_stage_voucher" aria-label="..." >
                                    </span>
                                </div>
                                
                                <div class="input-group" style="margin-top:10px;">
                                    <span class="input-group-addon" id="basic-addon1" style="min-width:100px;">JPoint</span>
                                    <input type="text" name="bmgm_2_stage_point" id="bmgm_2_stage_point" class="form-control" aria-label="..." placeholder="">
                                    <span class="input-group-addon">
                                      <input type="checkbox" name="bmgm_2_is_stage_point" id="bmgm_2_is_stage_point" aria-label="...">
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label">Third Stage
                            <i class="fa fa-question-circle" data-container="body" data-toggle="popover" title="Third Stage" data-placement="right" data-content="Set voucher cash amount / JPoint total and tick at the checkbox to set the setting available for reward"></i></label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1" style="min-width:100px;">MYR</span>
                                    <input type="text" name="bmgm_3_stage_amount" id="bmgm_3_stage_amount" class="form-control" placeholder="Ex: 250.00" aria-describedby="basic-addon1">
                                </div>
                                <div class="input-group" style="margin-top:10px;">
                                    <span class="input-group-addon" id="basic-addon1" style="min-width:100px;">Voucher </span>
                                    <input type="text" name="bmgm_3_stage_voucher_amount" id="bmgm_3_stage_voucher_amount" class="form-control" aria-label="..." placeholder="">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="bmgm_3_is_stage_voucher" id="bmgm_3_is_stage_voucher" aria-label="..." >
                                    </span>
                                </div>
                                
                                <div class="input-group" style="margin-top:10px;">
                                    <span class="input-group-addon" id="basic-addon1" style="min-width:100px;">JPoint</span>
                                    <input type="text" name="bmgm_3_stage_point" id="bmgm_3_stage_point" class="form-control" aria-label="..." placeholder="">
                                    <span class="input-group-addon">
                                      <input type="checkbox" name="bmgm_3_is_stage_point" id="bmgm_3_is_stage_point" aria-label="...">
                                    </span>
                                </div>
                            </div>
                        </div>
                        <h4 class="ui horizontal divider header">
                            <i class="tag icon"></i>
                                General Setting
                        </h4>
                       <div class="form-group row">
                            <label for="" class="col-md-4 col-form-label col-form-label">Send Notification Via Push Notification</label>
                            <div class="col-md-8">
                                <input type="checkbox" name="bmgm_is_push_notification" class="" id="bmgm_is_push_notification" placeholder="">
                                <small style=""></small>
                            </div>
                        </div>
                       <hr>
                       <a class="btn btn-primary" id="bmgm_submit">Save</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Referrer Reward Start -->
    <div class="panel panel-default form-panel" id="RFRR_panel">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list-alt fa-fw"></i> Referrer Reward </h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-8  col-xs-12">
                    <form id="rfrr_form">
                        <div class="ui active inverted dimmer" id="load-rfrr"> <div class="ui medium text loader">Loading ..</div></div>
                        <div class="form-group row">
                            <label for="" class="col-md-2 col-form-label col-form-label">Module Activation</label>
                            <div class="col-md-10">
                                <input name="rfrr_is_activate" id="rfrr_is_activate" type="checkbox" class="" id="colFormLabelSm" placeholder="">
                                <small style="">Activate this module</small>
                            </div>
                        </div>
                        <h4 class="ui horizontal divider header">
                            <i class="tag icon"></i>
                            Reward Setting
                        </h4><br>

                        <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label">Referrer Reward
                            </label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1" style="min-width:100px;">JPoint</span>
                                    <input type="text" name="rfrr_point" id="rfrr_point" class="form-control" placeholder="Ex: 10.00" aria-describedby="basic-addon1">
                                </div>
                                
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label">Description
                            </label>
                            <div class="col-sm-5">
                                <textarea rows="5" id="rfrr_description" name="rfrr_description" class="form-control"></textarea>
                            </div>
                        </div>
                        
                        
                       <hr>
                       <a class="btn btn-primary" id="rfrr_submit">Save</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Referrer Reward End -->
    
    <!-- Birthday Reward Start -->
    <div class="panel panel-default form-panel" id="BRTH_panel" style="display:none;">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list-alt fa-fw"></i> Birthday Reward </h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-8  col-xs-12">
                    <form id="brth_form">
                        <div class="ui active inverted dimmer" id="load-brth"> <div class="ui medium text loader">Loading ..</div></div>
                        <div class="form-group row">
                            <label for="" class="col-md-2 col-form-label col-form-label">Module Activation</label>
                            <div class="col-md-10">
                                <input name="brth_is_activate" id="brth_is_activate" type="checkbox" class="" id="colFormLabelSm" placeholder="">
                                <small style="">Activate this module</small>
                            </div>
                        </div>
                        <h4 class="ui horizontal divider header">
                            <i class="tag icon"></i>
                            Reward Setting
                        </h4>
                        <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label">Birthday Reward
                            <i class="fa fa-question-circle" data-container="body" data-toggle="popover" title="First Stage" data-placement="right" data-content="Set voucher cash amount / JPoint total and tick at the checkbox to set the setting available for reward"></i></label>
                            <div class="col-sm-5">
                                <!--<div class="input-group">-->
                                <!--    <span class="input-group-addon" id="basic-addon1" style="min-width:100px;">MYR</span>-->
                                <!--    <input type="text" name="brth_1_stage_amount" id="brth_1_stage_amount" class="form-control" placeholder="Ex: 250.00" aria-describedby="basic-addon1">-->
                                <!--</div>-->
                                <div class="input-group" style="margin-top:10px;">
                                    <span class="input-group-addon" id="basic-addon1" style="min-width:100px;">Voucher </span>
                                    <input type="text" name="brth_1_stage_voucher_amount" id="brth_1_stage_voucher_amount" class="form-control" aria-label="..." placeholder="">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="brth_1_is_stage_voucher" id="brth_1_is_stage_voucher" aria-label="..." >
                                    </span>
                                </div>
                                
                                <div class="input-group" style="margin-top:10px;">
                                    <span class="input-group-addon" id="basic-addon1" style="min-width:100px;">JPoint</span>
                                    <input type="text" name="brth_1_stage_point" id="brth_1_stage_point" class="form-control" aria-label="..." placeholder="">
                                    <span class="input-group-addon">
                                      <input type="checkbox" name="brth_1_is_stage_point" id="brth_1_is_stage_point" aria-label="...">
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <h4 class="ui horizontal divider header">
                            <i class="tag icon"></i>
                                General Setting
                        </h4>
                       <div class="form-group row">
                            <label for="" class="col-md-4 col-form-label col-form-label">Send Notification Via Push Notification</label>
                            <div class="col-md-8">
                                <input type="checkbox" name="brth_is_push_notification" class="" id="brth_is_push_notification" placeholder="">
                                <small style=""></small>
                            </div>
                        </div>
                       <hr>
                       <a class="btn btn-primary" id="brth_submit">Save</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Birthday Reward End -->
</div>

@stop
@section('inputjs')
<script>
    $( document ).ready(function() {
       $('[data-toggle="popover"]').popover({ trigger: "hover" });
       
       $(".box-cat").click(function(){
            $(".form-panel").hide(); 
       });
       
    
        // BUY MORE GET MORE SECTION //

        $("#thumb_BYGM_panel").click(function(){
            
            $.ajax({
                method: "POST",
                url: "/sysadmin/reward/info",
                dataType:'json',
                data: {},
                beforeSend: function(){
                    $("#load-bmgm").show();
                },
                success: function(data) {
                  
                    
                    var resp = data;
                    
                    console.log(resp.info.activation);
                    
                    resp.info.activation === '1' ? $("#bmgm_is_activate").prop('checked',true):$("#bmgm_is_activate").prop('checked',false);
                    
                    $("#bmgm_1_stage_amount").val(resp.info.first_stage.amount);
                    resp.info.first_stage.is_voucher === '1' ? $("#bmgm_1_is_stage_voucher").prop('checked',true):$("#bmgm_1_is_stage_voucher").prop('checked',false);
                    $("#bmgm_1_stage_voucher_amount").val(resp.info.first_stage.voucher_amount);
                    resp.info.first_stage.is_point === '1' ? $("#bmgm_1_is_stage_point").prop('checked',true):$("#bmgm_1_is_stage_point").prop('checked',false);
                    $("#bmgm_1_stage_point").val(resp.info.first_stage.point);
                    
                    $("#bmgm_2_stage_amount").val(resp.info.second_stage.amount);
                    resp.info.second_stage.is_voucher === '1' ? $("#bmgm_2_is_stage_voucher").prop('checked',true):$("#bmgm_2_is_stage_voucher").prop('checked',false);
                    $("#bmgm_2_stage_voucher_amount").val(resp.info.second_stage.voucher_amount);
                    resp.info.second_stage.is_point === '1' ? $("#bmgm_2_is_stage_point").prop('checked',true):$("#bmgm_2_is_stage_point").prop('checked',false);
                    $("#bmgm_2_stage_point").val(resp.info.second_stage.point);
                    
                    $("#bmgm_3_stage_amount").val(resp.info.third_stage.amount);
                    resp.info.third_stage.is_voucher === '1' ? $("#bmgm_3_is_stage_voucher").prop('checked',true):$("#bmgm_3_is_stage_voucher").prop('checked',false);
                    $("#bmgm_3_stage_voucher_amount").val(resp.info.third_stage.voucher_amount);
                    resp.info.third_stage.is_point === '1' ? $("#bmgm_3_is_stage_point").prop('checked',true):$("#bmgm_3_is_stage_point").prop('checked',false);
                    $("#bmgm_3_stage_point").val(resp.info.third_stage.point);
                    
                    resp.info.is_send_notification === '1' ? $("#bmgm_is_push_notification").prop('checked',true):$("#bmgm_is_push_notification").prop('checked',false);
                    
                    $("#load-bmgm").hide();
                }
          })
            
            $("#BYGM_panel").show(); 
        });
        
        $("#bmgm_submit").click(function(){
           
            var is_activate = $("#bmgm_is_activate").prop('checked') == true ? 1: 0; 
            
            var fst_stage_amount = $("#bmgm_1_stage_amount").val();
            var fst_is_stage_voucher = $("#bmgm_1_is_stage_voucher").prop('checked') == true ? 1: 0; 
            var fst_stage_voucher_amount = $("#bmgm_1_stage_voucher_amount").val();
            var fst_is_stage_point = $("#bmgm_1_is_stage_point").prop('checked') == true ? 1: 0; 
            var fst_stage_point = $("#bmgm_1_stage_point").val();
            
            var scd_stage_amount = $("#bmgm_2_stage_amount").val();
            var scd_is_stage_voucher = $("#bmgm_2_is_stage_voucher").prop('checked') == true ? 1: 0; 
            var scd_stage_voucher_amount = $("#bmgm_2_stage_voucher_amount").val();
            var scd_is_stage_point = $("#bmgm_2_is_stage_point").prop('checked') == true ? 1: 0; 
            var scd_stage_point = $("#bmgm_2_stage_point").val();
            
            var trd_stage_amount = $("#bmgm_3_stage_amount").val();
            var trd_is_stage_voucher = $("#bmgm_3_is_stage_voucher").prop('checked') == true ? 1: 0; 
            var trd_stage_voucher_amount = $("#bmgm_3_stage_voucher_amount").val();
            var trd_is_stage_point = $("#bmgm_3_is_stage_point").prop('checked') == true ? 1: 0; 
            var trd_stage_point = $("#bmgm_3_stage_point").val();
            
            var is_push_notification = $("#bmgm_is_push_notification").prop('checked') == true ? 1: 0;
            
            
            $.ajax({
                method: "POST",
                url: "/sysadmin/reward/save",
                dataType:'json',
                data: {
                    
                    'is_activate':is_activate,
                    
                    '1_stage_amount':fst_stage_amount,
                    '1_is_stage_voucher':fst_is_stage_voucher,
                    '1_stage_voucher_amount':fst_stage_voucher_amount,
                    '1_is_stage_point':fst_is_stage_point,
                    '1_stage_point':fst_stage_point,
                    
                    '2_stage_amount':scd_stage_amount,
                    '2_is_stage_voucher':scd_is_stage_voucher,
                    '2_stage_voucher_amount':scd_stage_voucher_amount,
                    '2_is_stage_point':scd_is_stage_point,
                    '2_stage_point':scd_stage_point,
                    
                    '3_stage_amount':trd_stage_amount,
                    '3_is_stage_voucher':trd_is_stage_voucher,
                    '3_stage_voucher_amount':trd_stage_voucher_amount,
                    '3_is_stage_point':trd_is_stage_point,
                    '3_stage_point':trd_stage_point,
                    
                    'is_push_notification':is_push_notification,
                    
                },
                beforeSend: function(){
                    $("#load-bmgm").show();
                },
                success: function(data) {
                    
                    $("#load-bmgm").hide();
                    alert('Successfully saved!');
                }
          })
            
            
        });
        
        

        // BUY MORE GET MORE SECTION //
        
        // START REFERRER REWARD SECTION  //

         $("#thumb_RFRR_panel").click(function(){
            
            $.ajax({
                method: "POST",
                url: "/sysadmin/reward/refrinfo",
                dataType:'json',
                data: {},
                beforeSend: function(){
                    $("#load-rfrr").show();
                },
                success: function(data) {
                    console.log(data.info);
                    
                    var rfrr = data;

                    rfrr.info.activation == '1' ? $("#rfrr_is_activate").prop('checked',true):$("#rfrr_is_activate").prop('checked',false);
                   
                    $("#rfrr_point").val(rfrr.info.point);
                    $("#rfrr_description").val(rfrr.info.description);
                    
                    $("#load-rfrr").hide();
                }
          })
            
            $("#RFRR_panel").show(); 
        });   


        $("#rfrr_submit").click(function(){

            var is_activate = $("#rfrr_is_activate").prop('checked') == true ? 1: 0; 
            
            var rfrr_point = $("#rfrr_point").val();
            var rfrr_description = $("#rfrr_description").val();

            $.ajax({
                method: "POST",
                url: "/sysadmin/reward/rfrrsave",
                dataType:'json',
                data: {
                    
                    'is_activate':is_activate,
                    'rfrr_point':rfrr_point,
                    'rfrr_description':rfrr_description,
                    
                    
                },
                beforeSend: function(){
                    $("#load-rfrr").show();
                },
                success: function(data) {
                    
                    $("#load-rfrr").hide();
                    bootbox.alert("Successfully saved!", function(e){
                                        parent.$.fn.colorbox.close();
                                    });
                   // alert('Successfully saved!');
                }
             })

        });

        
        // END REFERRER REWARD SECTION  //   
        
        // BIRTHDAY REWARD SECTION START //

        $("#thumb_BRTH_panel").click(function(){
            
            $.ajax({
                method: "POST",
                url: "/sysadmin/reward/infobrth",
                dataType:'json',
                data: {},
                beforeSend: function(){
                    $("#load-brth").show();
                },
                success: function(data) {
                    console.log(data.info);
                    
                    var resp = data;
                    
                    resp.info.activation === '1' ? $("#brth_is_activate").prop('checked',true):$("#brth_is_activate").prop('checked',false);
                    
                    $("#brth_1_stage_amount").val(resp.info.first_stage.amount);
                    resp.info.first_stage.is_voucher === '1' ? $("#brth_1_is_stage_voucher").prop('checked',true):$("#brth_1_is_stage_voucher").prop('checked',false);
                    $("#brth_1_stage_voucher_amount").val(resp.info.first_stage.voucher_amount);
                    resp.info.first_stage.is_point === '1' ? $("#brth_1_is_stage_point").prop('checked',true):$("#brth_1_is_stage_point").prop('checked',false);
                    $("#brth_1_stage_point").val(resp.info.first_stage.point);
                    
                    resp.info.is_send_notification == '1' ? $("#brth_is_push_notification").prop('checked',true):$("#brth_is_push_notification").prop('checked',false);
                    
                    $("#load-brth").hide();
                }
          })
            
            $("#BRTH_panel").show(); 
        });

        $("#brth_submit").click(function(){
           
            var is_activate = $("#brth_is_activate").prop('checked') == true ? 1: 0; 
            var fst_stage_amount = $("#brth_1_stage_amount").val();
            var fst_is_stage_voucher = $("#brth_1_is_stage_voucher").prop('checked') == true ? 1: 0; 
            var fst_stage_voucher_amount = $("#brth_1_stage_voucher_amount").val();
            var fst_is_stage_point = $("#brth_1_is_stage_point").prop('checked') == true ? 1: 0; 
            var fst_stage_point = $("#brth_1_stage_point").val();
            
            var is_push_notification = $("#brth_is_push_notification").prop('checked') == true ? 1: 0;
            
            
            $.ajax({
                method: "POST",
                url: "/sysadmin/reward/savebrth",
                dataType:'json',
                data: {
                    
                    'is_activate':is_activate,
                    
                    '1_stage_amount':fst_stage_amount,
                    '1_is_stage_voucher':fst_is_stage_voucher,
                    '1_stage_voucher_amount':fst_stage_voucher_amount,
                    '1_is_stage_point':fst_is_stage_point,
                    '1_stage_point':fst_stage_point,
                    
                    'is_push_notification':is_push_notification,
                    
                },
                beforeSend: function(){
                    $("#load-brth").show();
                },
                success: function(data) {
                    
                    $("#load-brth").hide();
                    alert('Successfully saved!');
                }
          })
            
            
        });

        // END BIRTHDAY REWARD SECTION //
    
        
    });
    
   
    
   
</script>
@stop
@section('script')


@stop