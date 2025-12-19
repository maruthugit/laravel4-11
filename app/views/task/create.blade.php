@extends('layouts.master')
@section('title') New Ticketing @stop
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
        margin-bottom: 5px;
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
    }
    
    .main-sub-panel{
        padding: 15px;
    }
    
    .data-body > tr{
        height: 50px;
        min-height: 50px;
    }
    
    .data-body > tr:hover{
        height: 50px;
        min-height: 50px;
       background-color: #f5f5f5;
    }
    
    .tr-row{
        height: 50px;
        min-height: 50px;
        
    }
    
    .tr-row:hover{
        height: 50px;
        min-height: 50px;
       background-color: #f5f5f5;
    }
    
    .msg-row{
        border-bottom: solid 1px #e8e8e8;
        padding-bottom: 10px;
        padding-top: 10px;
        padding-left: 0px;
        padding-right: 0px;
    }
    .msg-row:last-child{
        border-bottom: solid 0px #e8e8e8;
        padding-bottom: 20px;
        padding-top: 10px;
        padding-left: 0px;
        padding-right: 0px;
    }
    
    #v_msg_panel{
        display: none;
    }
    
    .td-1st{
        width:150px;cursor: pointer;min-height: 100px;
    }
    
    .td-2st{
        width:250px;cursor: pointer;min-height: 100px;
    }
    
    .v-details{
        font-size: 12px;
        letter-spacing: 1px;
        background-color: #ef7d7d !important;
    }
    
    .v-details-assign{
        font-size: 12px;
        letter-spacing: 1px;
        background-color: #ef7d7d !important;
    }
    
    .v-details-overview{
        font-size: 12px;
        letter-spacing: 1px;
        background-color: #ef7d7d !important;
    }
    
    .sug-row{
        min-height: 40px;
        background-color: #ffffff;
        padding: 10px;
        border: solid 1px #e8e8e8;
        border-bottom:solid 0px #e8e8e8;
        width: 100%;
        min-width: 250px;
        cursor: pointer;
        margin-right:  15px;
    }
    
    .sug-row:last-child{
        min-height: 40px;
        background-color: #ffffff;
        padding: 10px;
        border: solid 1px #e8e8e8;
        /*border-bottom:solid 0px #e8e8e8;*/
        width: 100%;
        min-width: 250px;
        cursor: pointer;
        margin-right:  15px;
    }
    
    .sug-row:hover{
        min-height: 40px;
        background-color: #f7f7f7;
        padding: 10px;
        border: solid 1px #e8e8e8;
        border-bottom:solid 0px #e8e8e8;
        width: 100%;
        min-width: 250px;
        
    }
    
    .sug_box {
        position: absolute;
        z-index: 10;
        padding-right: 15px;
    }
    
    .loading-board{
            height: 100%;
    background-color: #fffff;
    width: 98%;
    z-index: 10;
    position: absolute;
    height: 500px;
    }
    
    .cls-details{
        border-radius:50px;
    }
    
    .msg-img-box{
        vertical-align: top;
        padding-right: 5px;
        width:50px;
        max-width: 50px;
    }

.msg-img{
    width:40px;
    height: 40px;
    border: solid 2px #ddd !important;
    border-radius: 40px;
}

#v-list{
    background-color: #fbfbfb !important;
}

.list-panel{
    
  box-shadow: 5px 0 5px -5px #a5a5a5;
    z-index: 10;
    padding-top: 10px;
    background-color: #fff;
}

textarea[contenteditable]:empty::before {
  content: "Placeholder still possible";
  color: gray;
}

textarea {
  display: block;
  width: 100%;
  overflow: hidden;
  resize: both;
  min-height: 40px;
  line-height: 20px;
}

.labelinput-hidden{width: 0px; height: 0px; display: inline;}

textarea.form-control {resize: none;}
    
</style>
<div class="loading"><span id="load-message"></span></div>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
           <h1 class="page-header">Create Ticketing</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list-alt fa-fw"></i> </h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3 box-cat" id="thumb_BRTH_panel">
                    <div class="box-con">
                        <div style="display: inline-block">
                            <div><i class="fa fa-list-alt fa-3x"></i></div>
                         </div>
                         <div style="display: inline-block;padding-left: 10px;">
                            <div class="title-cat"><strong>Total Today My Task</strong></div>
                            <div><small> <?php echo $TotalTodayTask; ?></small></div>  
                        </div>
                    </div>
                </div>
                <div class="col-md-3 box-cat" id="thumb_BYGM_panel">
                    <div class="box-con">
                        <div style="display: inline-block">
                            <div><i class="fa fa-exclamation-triangle fa-3x"></i></div>
                         </div>
                         <div style="display: inline-block;padding-left: 10px;">
                            <div class="title-cat"><strong>Total My Pending Task</strong></div>
                            <div><small><?php echo $TotalPendingTask; ?></small></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 box-cat" id="thumb_RFRR_panel">
                    <div class="box-con">
                        <div style="display: inline-block">
                            <div><i class="fa fa-check-square-o fa-3x"></i></div>
                         </div>
                         <div style="display: inline-block;padding-left: 10px;">
                            <div class="title-cat"><strong>Weekly Total Resolved</strong></div>
                            <div><small><?php echo $TotalResolvedTask; ?></small></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 box-cat" id="thumb_NEWS_panel">
                    <div class="box-con">
                        <div style="display: inline-block">
                            <div><i class="fa fa-database fa-3x"></i></div>
                         </div>
                         <div style="display: inline-block;padding-left: 10px;">
                            <div class="title-cat"><strong>Weekly Total Assign Task</strong></div>
                            <div><small><?php echo $TotalAssignedTask; ?></small></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="panel panel-default form-panel" >
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list-alt fa-fw"></i> Create Ticketing </h3>
        </div>
        <div class="panel-body" style="padding-top:0px;padding-bottom: 0px;">
            <div class="row" id="v_msg_panel">
                <div class="col-md-9  col-xs-12 animated " id="task_details" style="border-right: solid 1px #ddd;box-shadow: 5px 0 5px -5px #a5a5a5;z-index: 10;   padding-top: 10px;" >
                   <ul class="nav nav-tabs" id="myTabs" role="tablist"> 
                        <li role="presentation" class="active"><a href="#home" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true"><i class="fa fa-inbox"></i> My Inbox</a></li>
                        <li role="presentation" class=""><a href="#profile" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile" aria-expanded="false"><i class="fa fa-files-o"></i> Created Task</a></li>
                        <li role="presentation" class=""><a href="#overview2" role="tab" id="overview" data-toggle="tab" aria-controls="overview2" aria-expanded="false"><i class="fa fa-files-o"></i> Overview</a></li>
                        <a class="btn btn-default btn-sm pull-right" id="reset_record"><i class="fa fa-refresh"></i> Reset</a>
                    </ul> 
                </div>
            </div>
            <div class="col-md-12 col-xs-12 vue-box" style="padding-bottom: 15px; bottom: 0px; top: 0px;" id="FormNewTask2">
                <form>
                    <div class="ui active inverted dimmer" v-if="saving_task"> <div class="ui medium text loader"></div></div>
                    
                    <div v-if="alertSaveTask">
                        <div class="alert" v-bind:class="[ alertSaveType == 1 ? 'alert-danger' : 'alert-success']" role="alert">@{{alertSaveTaskMSG}}</div>
                    </div>
                    <h4 class="ui horizontal divider header">
                        <i class="tag icon"></i>
                        Fill in all required information 
                    </h4>
                    
                    <div class="form-group row">
                        <label for="" class="col-sm-3 col-form-label">Task Title
                        </label>
                        <div class="col-md-9">
                            <input type="text" v-model="taskTitle" name="taskTitle" class="form-control" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-3 col-form-label">Assign To</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon" id="sizing-addon2"><i class="fa fa-user"></i></span>
                                <input type="text" v-on:keyup="getSuggestion" v-model="assignTo" class="form-control" aria-describedby="sizing-addon2">
                            </div>
                            <div class="sug_box" v-if="sug_box_seen" >
                                <div v-for="itemSug in sug_list" v-on:click="chooseAssignTo(itemSug.id,itemSug.full_name)" class="sug-row col-md-12 col-xs-12"><i class="fa fa-user"></i> @{{itemSug.full_name}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-3 col-form-label">Category</label>
                        <div class="col-md-9">
                            <select class="form-control" v-model="category" name="category" >
                                <option v-for="item in categoryList" v-bind:value="item.id" >@{{item.label}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-3 col-form-label">Due Date</label>
                        <div class="col-md-9">
                            <div class="input-group" >
                                <span class="input-group-addon" id="sizing-addon2"><i class="fa fa-calendar-o"></i></span>
                                <input type="text"  name="dueDate" class="form-control dueDate" placeholder="" id="dueDate" aria-describedby="sizing-addon2">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-3 col-form-label">Description</label>
                        <div class="col-md-9">
                            <textarea class="form-control" v-model="description" name="description" rows="8"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-md-3 col-form-label col-form-label">Set Priority</label>
                        <div class="col-md-9">
                            <select v-model="priority" class="form-control">
                                <option value="1">Low</option>
                                <option value="2">High</option>
                                <option value="3">Urgent</option>
                            </select>
                        </div>
                    </div>
                    <h4 class="ui horizontal divider header">ORDERS</h4>
                    <div class="form-group row">
                        <label for="" class="col-sm-3 col-form-label">Transaction ID
                        <i class="fa fa-question-circle" data-container="body" data-toggle="popover" title="First Stage" data-placement="right" data-content="Set voucher cash amount / JPoint total and tick at the checkbox to set the setting available for reward"></i></label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon" id="sizing-addon2"><i class="fa fa-tag"></i></span>
                                <input type="text" v-model="transaction_id" name="transaction_id" ref="transaction_id" class="form-control" placeholder="" aria-describedby="sizing-addon2">
                            </div>
                        </div>
                    </div>
                    {{-- add search function allow order ID, when usder lick on that will add order ID to above field --}}
                    <div class="form-group row" id="serach_tid" >
                        <label for="" class="col-sm-3 col-form-label">Search Transaction ID</label>
                        <div class="col-md-9" style="position: relative;">
                            <input type="text" v-on:keyup.13="setEnter('')" v-on:keyup.40="moveDownList" v-on:keyup.38="moveUpList" v-model="searchKeyword" class="form-control" placeholder="" aria-describedby="sizing-addon2">
                            <div style="top: 100%; right: 0px; z-index: 1; width: 100%;" class="row-search-box" v-if="sugBox" v-cloak>
                                <div class="row-search" v-for="item in sugList" v-if="gotResult" v-on:click="setEnter(item.id)" v-bind:class="[ navPointer == item.id ? 'active-row' : '']">
                                    <div class="sp-row-data">
                                        <div style="padding-top: 2px;"> <i class="fa fa-cube" style=""></i> @{{item.id}}</div>
                                        <div style="font-size:10px;font-weight: lighter;"><strong>@{{item.buyer_username}}</strong> | @{{item.delivery_name}} <i style="" class="fa fa-bookmark fa-2x " v-bind:class="[item.status == 5 ? 'green-mark' : 'red-mark']"></i></div>
                                    </div>
                                </div>
                                <div class="row-search" v-if="noResult" >
                                    <div class="sp-row-data" style="text-align:left;font-size: 15px;">
                                        <div style="padding-top: 10px;"> <span> - No Record Found - </span> </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                   
                    <div class="form-group row">
                        <label for="" class="col-md-3 col-form-label col-form-label">Set as urgent delivery</label>
                        <div class="col-md-6">
                            <input type="checkbox" v-model="is_urgent" name="is_urgent"  class="" id="" placeholder="">
                            <small style=""></small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-3 col-form-label">Logistic Transaction Remark</label>
                        <div class="col-md-9">
                            <textarea class="form-control" v-model="logisticTransRemark" name="logisticTransRemark"></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php
                            $platform = array_keys($ThirdPatryPlatform);
                            $platform = array_combine($platform, $platform);
                        ?>
                        <label for="" class="col-sm-3">3rd Party Platform</label>
                        <div class="col-md-9">
                            {{ Form::select('platform', $platform, '', ['class' => 'form-control ']) }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php
                            $store = [];
                            foreach ($ThirdPatryPlatform as $platform_name => $stores) {
                                if(!$stores){
                                    $store[$platform_name] = false;
                                    continue;
                                }
                                foreach ($stores as $key => $val) {
                                    if(!isset($store[$platform_name])) $store[$platform_name] = [];
                                    $store[$platform_name][$key] = $val['name'];
                                }
                            }
                        ?>
                        <label for="" class="col-sm-3">Vendor/Store</label>
                        <div class="col-md-9">
                            <select name="store" class="form-control">
                                <option disabled selected>Please Select Vendor</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="" class="col-sm-3 col-form-label">Upload attachment</label>
                        <div class="col-md-9">
                            <input type="file" class="form-control" multiple accept="image/png, image/gif, image/jpeg, application/pdf" v-on:change="function(e){ uploadImage(e) }"/>
                            <div style="padding: 5px;">
                                <span v-for="(image, key) in images" :key="key" class="img-thumbnail" style="margin: 5px; border-radius: 10px;">
                                    <table style="width: 100px; height: 100px;">
                                        <tr>
                                            <td style="vertical-align: middle; text-align: center; font-size: 0px;">
                                                <img v-if="image.file.name.split('.').reverse()[0] !== 'pdf'" :src="image.src" class="preview" style="max-width:100px; max-height: 100px;" :title="image.file.name" :alt="image.file.name"/>
                                                <img v-else src="/logistic/images/acrobatpdf.png" class="preview" style="max-width:100px; max-height: 100px;" :title="image.file.name" :alt="image.file.name"/>
                                            </td>
                                        </tr>
                                    </table>
                                    <p style="max-width: 100px; max-height: 40px; overflow: hidden; margin: 0px;">@{{ image.file.name }}</p>
                                </span>
                            </div>
                        </div>
                    </div>
                    <a class="btn btn-default pull-right" v-on:click="savetask()" ><i class="fa fa-pencil"></i> Create Ticketing</a>
                </form>
            </div>
        </div>
    </div>
</div>

<div  class="modal fade" id="vm1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog " role="document">
    <div class="modal-content">
       
        <div class="modal-body" style="padding: 40px;">
            <div class="ui active inverted dimmer" v-if="savingCompleted" > <div class="ui large text loader"></div></div>
            <div class="row" style="text-align:center;">
                <div class="col-md-12"></div>
                <div class="col-md-12" >
                    <i class="fa fa-check-square-o fa-5x" style="color: #49c77d;"></i>
                </div>
                <div class="col-md-12" style="font-size: 20px;">Are you going to mark @{{taskNumber}} as completed ?</div>
                
                <div class="col-md-12" style="font-size: 25px;margin-top: 15px;">
                    <h4 class="ui horizontal divider header"><i class="fa fa-edit"></i>
                            Put remarks for your action 
                        </h4>
                    <textarea class="form-control" v-model="remarks" placeholder="Your remarks .." rows="8"></textarea></div>
            </div>
        </div>
        <div class="modal-footer" style="background-color: #efefef;">
            
            <!-- /input-group -->
            <button type="button" class="btn btn-default" data-dismiss="modal" style="width: 100px;">Close</button>
            <button type="button" class="btn btn-default" v-on:click="updateStatus(taskNumber,vm1CompletedStatus)" style="width: 100px;">Yes</button>
        </div>
        
    </div>
  </div>
</div>

<div  class="modal fade" id="vm2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog " role="document">
    <div class="modal-content">
       
        <div class="modal-body" style="padding: 40px;">
            <div v-if="savingCompleted" class="ui active inverted dimmer"> <div class="ui large text loader"></div></div>
            <div class="row" style="text-align:center;">
                <div class="col-md-12"></div>
                <div class="col-md-12" >
                    <i class="fa fa-trash-o fa-5x" style="color: #ff6f6f;"></i>
                </div>
                <div class="col-md-12" style="font-size: 25px;">Are you sure to cancel this @{{taskNumber}}?</div>
                
                <div class="col-md-12" style="font-size: 25px;margin-top: 15px;">
                    <h4 class="ui horizontal divider header"><i class="fa fa-edit"></i>
                            Put remarks for your action 
                        </h4>
                    <textarea class="form-control" v-model="remarks" placeholder="Your remarks.." rows="5"></textarea></div>
            </div>
        </div>
        <div class="modal-footer" style="background-color: #efefef;">
            
            <!-- /input-group -->
            <button type="button" class="btn btn-default" data-dismiss="modal" style="width: 100px;">Close</button>
            <button type="button" class="btn btn-default" v-on:click="updateStatus(taskNumber,vm2CompletedStatus)" style="width: 100px;">Yes</button>
        </div>
        
    </div>
  </div>
</div>



@stop
@section('inputjs')
<script>

</script>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>
<script src="https://unpkg.com/vue-bootstrap-datetimepicker"></script>
<script src="/js/asset/task.js" ></script>

@stop
@section('script')
    $('#start_date, #end_date').datetimepicker({
        format: 'YYYY-MM-DD hh:mm:ss'
    });

    $(document).on('input selectionchange propertychange paste DOMContentLoaded', 'textarea', function (e) {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    $(document).on('click', 'a[href="#task"]', function(){
        var observer = new MutationObserver(function(mutations) {
            $('#myTabContent div#task textarea').height('auto');
            $('#myTabContent div#task textarea').each(function( index ) {
                $(this).height((this.scrollHeight) + 'px');
            });
        });
        var target = $('#myTabContent div#task')[0];
        observer.observe(target, {
            attributes: true
        });
    });

    $(document).on('change', 'select[name="platform"]', function(e){
        var temp = {{ json_encode($store) }};
        html = '<option disabled selected>Please Select Vendor</option>';
        $.each(temp[$(this).val()], function(i, o){
            html += '<option value="' + i + '">'+ o +'</option>';
        });
        $('select[name="store"]').html(html);
    });
@stop