@extends('layouts.master')
@section('title') Ticketing Report @stop
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
           <h1 class="page-header">Generate Report</h1>
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
            <h3 class="panel-title"><i class="fa fa-list-alt fa-fw"></i> Task List </h3>
        </div>
        <div class="panel-body" style="padding-top:0px;padding-bottom: 0px;">
            <div class="row" id="v-list">
                <div class="col-md-9  col-xs-12 animated list-panel" style="">
                   <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs"> 
                        <ul class="nav nav-tabs" id="myTabs" role="tablist" style="padding-bottom: 10px;">
                            <a class="btn btn-default btn-sm pull-right" id="reset_record"><i class="fa fa-refresh"></i> Reset</a>
                        </ul> 
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade active in" role="tabpanel" id="report" aria-labelledby="report-tab"> 
                                <div class="col-md-12 main-sub-panel">
                                    <table class="table" id="inbox-list4">
                                        <thead>
                                            <tr>
                                                <th>Task ID</th>
                                                <th>Transaction ID</th>
                                                <th>Task</th>
                                                <th>Assign By</th>
                                                <th>Assign To</th>
                                                <th>Priority</th>
                                                <th>Status</th>
                                                <th>Last Update</th>
                                            </tr> 
                                        </thead> 
                                        <tbody class="data-body"> 
                                          
                                        </tbody> 
                                    </table>
                                </div>
                            </div> 
                        </div> 
                   </div>
                </div>
                <div class="col-md-3  col-xs-12 vue-box" style="padding-bottom: 15px;    bottom: 0px;    top: 0px;" id="FormNewTask2">
                    <!--<div class="ui active inverted dimmer"> <div class="ui medium text loader">Creating..</div></div>-->
                    <div>
                        <h4 title="Report Generator"><i class="fa fa-magic"></i> Opitions</h4>
                        <h4 class="ui horizontal divider header">
                            <i class="tag icon"></i>
                            Select and apply the field
                        </h4>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">Issue Type</label>
                            <div class="col-md-9">
                                <select class="form-control" name="Rcategory">
                                    <option value="Return">Returned</option>
                                    <option value="Pending">Pending</option>
                                    <option value="MissItem">Missing Item</option>
                                    <option value="Damaged">Damaged</option>
                                    <option value="WrongItem">Wrong Item</option>
                                    <option value="Chg.ADD">Change Address</option>
                                    <option value="Chg.MOB">Change Mobile</option>
                                    <option value="Update">Updates</option>
                                    <option value="Tech.Issue">Technical issue</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">Start Date</label>
                            <div class="col-md-9">
                                <div class="input-group" >
                                    <span class="input-group-addon" id="sizing-addonSD"><i class="fa fa-calendar-o"></i></span>
                                    <input type="text"  name="start_date" class="form-control" id="start_date" aria-describedby="sizing-addonSD">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">End Date</label>
                            <div class="col-md-9">
                                <div class="input-group" >
                                    <span class="input-group-addon" id="sizing-addonED"><i class="fa fa-calendar-o"></i></span>
                                    <input type="text"  name="end_date" class="form-control" id="end_date" aria-describedby="sizing-addonED">
                                </div>
                            </div>
                        </div>

                        <a class="btn btn-default pull-right generatereport"><i class="fa fa-link"></i> Generate Report</a>
                        <a class="btn btn-default pull-right previewreport"><i class="fa fa-book"></i> Preview Report</a>
                    </div>
                </div>
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
@stop