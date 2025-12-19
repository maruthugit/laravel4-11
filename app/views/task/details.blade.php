@extends('layouts.master')
@section('title') Task Details @stop
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

#task_details2 .alert{margin: 10px 0px;}
    
</style>
<div class="loading"><span id="load-message"></span></div>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
           <h1 class="page-header">Task Details</h1>
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
            <h3 class="panel-title"><i class="fa fa-list-alt fa-fw"></i> Task Info </h3>
        </div>
        <div class="panel-body" style="padding-top:0px;padding-bottom: 0px;">
             <div class="row" id="v_msg_panel" data-details="{{ $id }}" data-allowsave="{{ $save_allow && $_GET['editing'] ? $save_allow : false }}">
                <div class="col-md-9  col-xs-12 animated " id="task_details" style="border-right: solid 1px #ddd;box-shadow: 5px 0 5px -5px #a5a5a5;z-index: 10;   padding-top: 10px;" >

                    <form>
                        <div class="form-group row" v-if="mainBoardIsPrepared">
                            <div class="col-md-12">
                                <div><h4><i class="fa fa-caret-right"></i> <strong>@{{taskInfo.task_id_number}}</strong> <span class="label label-info">@{{taskInfo.status}}</span></h4></div>
                                @if($save_allow)
                                <div class="pull-right">
                                    <a href="{{ !$_GET['editing'] ? Request::url() . '?editing=1' : 'javascript:void(0)' }}" class="btn btn-default btn-act-edit" {{ !$_GET['editing'] ? '' : 'onclick="return false" aria-disabled="true" disabled' }}>Edit</a>
                                    <div class="input-group-btn" style="display: inline-block; vertical-align: top; width: auto;">
                                        <div class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Assign</div>
                                        <ul class="dropdown-menu" style="right: 0px; left: auto; max-height: 400px; overflow-y: auto;">
                                            @foreach($AdminUser as $val)
                                            <li><a href="#" class="v-action-assign" data-assign="{{ $val['id'] . '/' . $id }}"><i class="fa fa-user"></i> {{ $val['full_name'] }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="input-group-btn" style="display: inline-block; vertical-align: top; width: auto;">
                                        <div class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</div>
                                        <ul class="dropdown-menu" style="right: 0px; left: auto;">
                                            <li><a href="#" v-bind:target-id="taskInfo.task_id_number" class="v-action-resolved" ref="actresolve"><i class="fa fa-check"></i> Set as Resolved</a></li>
                                            <li><a href="#" v-bind:target-id="taskInfo.task_id_number" class="v-action-cancel" ref="actcancel"><i class="fa fa-trash-o"></i> Cancel Issue</a></li>
                                        </ul>
                                    </div>
                                </div>
                                @endif

                                <div><small> <i class="fa fa-calendar-o"></i> @{{taskInfo.created_at}}</small></div>
                                <div><small> <i class="fa fa-user"></i> Created By : @{{taskInfo.CreatedByName}} </small></div>
                                <div><small> <i class="fa fa-user"></i> Assign By : @{{taskInfo.AssignByName}} </small></div>
                                <div><small> <i class="fa fa-user"></i> Assign To : @{{taskInfo.AssignToName}} </small></div>
                            </div>
                        </div>

                        <hr v-if="mainBoardIsPrepared">
                       
                        <div class="bs-example bs-example-tabs main-board" data-example-id="togglable-tabs"> 
                            <div class="loading-board" v-if="mainBoardloading">
                                <div class="ui active inverted dimmer"> <div class="ui large text loader">Preparing Display</div></div>
                            </div>
                           
                            <ul class="nav nav-tabs" id="myTabs" role="tablist" v-if="mainBoardIsPrepared"> 
                                <li v-if="isTransaction" role="presentation" class="" v-bind:class="[ isTransaction  ? 'active' : '']"><a href="#message" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile" aria-expanded="false"><i class="fa fa-files-o"></i> Order Information</a></li>
                                <li role="presentation" class="" v-bind:class="[ !isTransaction  ? 'active' : '']"><a href="#task" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true"><i class="fa fa-inbox"></i> Logistic Transaction</a></li>
                            </ul> 
                            <div class="tab-content" id="myTabContent" v-if="mainBoardIsPrepared"> 
                               
                                <div v-if="isTransaction" class="tab-pane fade in" v-bind:class="[ isTransaction  ? 'active' : '']" role="tabpanel" id="message" aria-labelledby="home-tab">
                                    <div class="col-md-12 main-sub-panel" style="background-color: #fbfbfb;">
                                        <div class="form-group row">
                                            <label for="" class="col-sm-2 col-form-label">Status</label>
                                            <div class="col-md-4">
                                                <span class="label label-success" style="text-transform: uppercase;">@{{orderInfo.status}}</span>
                                            </div>
                                            <label for="" class="col-sm-2 col-form-label"></label>
                                            <div class="col-md-4"></div>
                                        </div>
                                        <h4 class="ui horizontal divider header"><i class="fa fa-cubes"></i> Order Information</h4>
                                        <div class="form-group row">
                                            <label for="" class="col-sm-2 col-form-label">Transaction ID</label>
                                            <div class="col-md-4">
                                                @{{orderInfo.transactionID}}
                                            </div>
                                            <label for="" class="col-sm-2 col-form-label">Transaction Date</label>
                                            <div class="col-md-4">
                                                @{{orderInfo.transactionDate}}
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="" class="col-sm-2 col-form-label">Buyer</label>
                                            <div class="col-md-4">
                                                @{{orderInfo.buyer}}
                                            </div>
                                            <label for="" class="col-sm-2 col-form-label">Phone</label>
                                            <div class="col-md-4">
                                                @{{orderInfo.phone}}
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="" class="col-sm-2 col-form-label">Invoice No</label>
                                            <div class="col-md-4">
                                                @{{orderInfo.invoice_no}}
                                            </div>
                                            <label for="" class="col-sm-2 col-form-label">DO Number</label>
                                            <div class="col-md-4">
                                                @{{orderInfo.do_no}}
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="" class="col-sm-2 col-form-label">Coupon Code</label>
                                            <div class="col-md-4">
                                                @{{orderInfo.couponCode}}
                                            </div>
                                            <label for="" class="col-sm-2 col-form-label"></label>
                                            <div class="col-md-4"></div>
                                        </div>
                                        <h4 class="ui horizontal divider header"><i class="fa fa-cubes"></i> Order Item</h4>
                                        <div class="form-group row" > 
                                            <div class="col-md-12 col-xs-12">
                                                <table class="table table-striped table-bordered table-hover" id="dataTables-details">
                                                    <thead>
                                                        <tr style="background-color: #ececec;">
                                                            <th>#</th>
                                                            <th>Product Name</th>
                                                            <th>Label</th>
                                                            <th>SKU</th>
                                                            <th>Qty</th>
                                                            <th>Price</th>
                                                            <th>GST</th>
                                                            <th>Delivery Time</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="item in orderItemList" class="odd gradeX">
                                                            <td>@{{item.numbering}}</td>
                                                            <td>@{{item.ProductName}}</td>
                                                            <td>@{{item.ProductLabel}}</td>
                                                            <td>@{{item.ProductSKU}}<br></td>
                                                            <td>@{{item.qty}}</td>
                                                            <td>@{{item.price}}</td>
                                                            <td>@{{item.gst}}</td>
                                                            <td>@{{item.deliveryTime}}</td>
                                                            <td>@{{item.totalAmount}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                        </div>
                                        <h4 class="ui horizontal divider header"><i class="fa fa-truck"></i> Shipping </h4>
                                        <div class="form-group row">
                                            <label for="" class="col-sm-2 col-form-label" >Logistic Status</label>
                                            <div class="col-md-4">
                                                <span class="label label-warning" style="text-transform:uppercase;">@{{orderLogInfo.status}}</span>
                                            </div>
                                            <label for="" class="col-sm-2 col-form-label"></label>
                                            <div class="col-md-4"></div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="" class="col-sm-2 col-form-label">Recipient</label>
                                            <div class="col-md-4">
                                               @{{orderLogInfo.recipient}}
                                            </div>
                                            <label for="" class="col-sm-2 col-form-label">Recipient Phone</label>
                                            <div class="col-md-4">
                                                @{{orderLogInfo.recipientPhone}}
                                            </div>
                                        </div>
                                       
                                        <div class="form-group row">
                                            <label for="" class="col-sm-2 col-form-label">Delivery Address</label>
                                            <div class="col-md-4">
                                                @{{orderLogInfo.address}}
                                            </div>
                                            <label for="" class="col-sm-2 col-form-label">Special Message</label>
                                            <div class="col-md-4">
                                                @{{orderLogInfo.message}}
                                            </div>
                                        </div>
                                        <h4 class="ui horizontal divider header"><i class="fa fa-truck"></i> Shipping Batch </h4>
                                        <div class="form-group row" > 
                                            <div class="col-md-12 col-xs-12">
                                                <table class="table table-striped table-bordered table-hover" id="dataTables-details">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Created at</th>
                                                            <th>Logistic Courier</th>
                                                            <th>Tracking Number</th>
                                                            <th>Driver</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                            <tr v-for="batch in orderBatchList" class="odd gradeX">
                                                                <td>@{{batch.numbering}}</td>
                                                                <td>@{{batch.created_at}} </td>
                                                                <td>@{{batch.logisticCourier}}</td>
                                                                <td>@{{batch.trackingNumber}}<br></td>
                                                                <td>@{{batch.driver}}</td>
                                                                <td style="text-transform: uppercase;">@{{batch.status}}</td>
                                                            </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <h4 class="ui horizontal divider header"><i class="fa fa-picture-o"></i> Logistic Transaction Image </h4>
                                        <div class="form-group row">
                                            <span v-for="(image, key) in LogiImage" style="display: inline-block; vertical-align: top;">
                                                <a v-if="image.mime === 'application/pdf'" :key="key" v-bind:href="'/download/' + image.attachment" target="_blank" data-toggle="tooltip" class="img-thumbnail">
                                                    <table style="width: 140px; height: 140px;">
                                                        <tr>
                                                            <td style="vertical-align: middle; text-align: center; font-size: 0px;">
                                                                <img style="max-width: 140px; max-height: 140px; cursor: pointer;" src="/logistic/images/acrobatpdf.png" alt="">
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </a>
                                                <a v-else :key="key" v-bind:href="'/logistic/images/' + image.attachment" target="_blank" data-toggle="tooltip" :title="image.attachment" class="img-thumbnail">
                                                    <table style="width: 140px; height: 140px;">
                                                        <tr>
                                                            <td style="vertical-align: middle; text-align: center; font-size: 0px;">
                                                                <img style="max-width: 150px; max-height: 150px; cursor: pointer;" v-bind:src="'/logistic/images/' + image.attachment" alt="">
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </a>
                                            </span>
                                        </div>
                                        <h4 class="ui horizontal divider header"><i class="fa fa-picture-o"></i> Driver Image </h4>
                                        <div class="form-group row">
                                            <span v-for="(image, key) in LogiDriver" style="display: inline-block; vertical-align: top;">
                                                <a :key="key" v-bind:href="image.filename" target="_blank" data-toggle="tooltip" :title="image.filename" class="img-thumbnail">
                                                    <table style="width: 140px; height: 140px;">
                                                        <tr>
                                                            <td style="vertical-align: middle; text-align: center; font-size: 0px;">
                                                                <img style="max-width: 150px; max-height: 150px; cursor: pointer;" v-bind:src="image.filename" alt="">
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade in" role="tabpanel" v-bind:class="[ !isTransaction  ? 'active' : '']" id="task" aria-labelledby="home-tab">
                                    <div class="col-md-12 main-sub-panel" style="background-color: #fbfbfb;">    
                                        <div v-if="alertUpdateTask" class="alert" v-bind:class="[ alertUpdateType == 1 ? 'alert-danger' : 'alert-success']" role="alert">@{{alertUpdateTaskMSG}}</div>
                                        <div class="ui active inverted dimmer" v-if="updatingTask"> <div class="ui large text loader"></div></div>
                                        <div class="form-group row">
                                            <label for="" class="col-md-2 col-form-label col-form-label">Remark</label>
                                            <div class="col-md-10">
                                                <p style="margin-bottom: 0px;" v-for="remark_text in orderLogInfo.remark.split(/\r?\n/)">@{{ remark_text }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="" class="col-md-2 col-form-label col-form-label">Add Remark</label>
                                            <div class="col-md-5">
                                                <textarea class="form-control" name="addremarks"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="" class="col-md-2 col-form-label col-form-label">Preview Attachment</label>
                                            <div class="col-md-10">
                                                <span v-for="(image, key) in LogiImage" style="display: inline-block; vertical-align: top;">
                                                    <a v-if="image.mime === 'application/pdf'" :key="key" v-bind:href="'/download/' + image.attachment" target="_blank" data-toggle="tooltip" class="img-thumbnail">
                                                        <table style="width: 100px; height: 100px;">
                                                            <tr>
                                                                <td style="vertical-align: middle; text-align: center; font-size: 0px;">
                                                                    <img style="max-width: 100px; max-height: 100px; cursor: pointer;" src="/logistic/images/acrobatpdf.png" alt="">
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </a>
                                                    <a v-else :key="key" v-bind:href="'/logistic/images/' + image.attachment" target="_blank" data-toggle="tooltip" :title="image.attachment" class="img-thumbnail">
                                                        <table style="width: 100px; height: 100px;">
                                                            <tr>
                                                                <td style="vertical-align: middle; text-align: center; font-size: 0px;">
                                                                    <img style="max-width: 100px; max-height: 100px; cursor: pointer;" v-bind:src="'/logistic/images/' + image.attachment" alt="">
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="" class="col-md-2 col-form-label col-form-label">Upload Attachment</label>
                                            <div class="col-md-10">
                                                <input type="file" class="form-control" multiple accept="image/png, image/gif, image/jpeg, application/pdf" v-on:change="function(e){ uploadAttach(e) }"/>
                                                <p style="color: red;">Please dont upload more than 1 MB file</p>
                                                <div style="padding: 5px;">
                                                    <span v-for="(image, key) in uploadAttch" :key="key" class="img-thumbnail" style="margin: 5px; border-radius: 10px; vertical-align: top;">
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
                                        <div class="form-group row">
                                            <label for="" class="col-md-2 col-form-label col-form-label">Preview Logistic Image (Driver Image)</label>
                                            <div class="col-md-10">
                                                <span v-for="(image, key) in LogiDriver" style="display: inline-block; vertical-align: top;">
                                                    <a :key="key" v-bind:href="image.attachment" target="_blank" data-toggle="tooltip" :title="image.attachment" class="img-thumbnail">
                                                        <table style="width: 100px; height: 100px;">
                                                            <tr>
                                                                <td style="vertical-align: middle; text-align: center; font-size: 0px;">
                                                                    <img style="max-width: 100px; max-height: 100px; cursor: pointer;" v-bind:src="image.attachment" alt="">
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <a class="btn btn-default pull-right" id="addremarks">Update Logistic Transaction</a>
                                        </div>
                                    </div>          
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-3 col-xs-12 animated fadeIn">
                    <div id="task_details2">
                        <div v-if="alertUpdateTask" class="alert" v-bind:class="[ alertUpdateType == 1 ? 'alert-danger' : 'alert-success']" role="alert">@{{alertUpdateTaskMSG}}</div>
                        <div class="ui active inverted dimmer" v-if="updatingTask"> <div class="ui large text loader"></div></div>
                        <h4 class="ui horizontal divider header">Task Info</h4>
                        <div class="form-group row">
                            <label for="" class="col-sm-4 col-form-label">Task Title
                            </label>
                            <div class="col-md-8">
                                <input type="text" v-model="taskInfo.title" class="form-control" placeholder="" aria-describedby="basic-addon1" :disabled="!saveAllowed">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-4 col-form-label">Assign To</label>
                            <div class="col-md-8">
                                <input type="text" v-on:keyup="getSuggestion" v-model="taskInfo.AssignToName" class="form-control" placeholder="" :disabled="!saveAllowed">
                                <div class="sug_box" v-if="sug_box_seen">
                                    <div v-for="itemSug in sugList" v-on:click="chooseAssignTo(itemSug.id, itemSug.full_name)" class="sug-row col-md-12 col-xs-12"><i class="fa fa-user"></i> @{{itemSug.full_name}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-4 col-form-label">Due Date
                            </label>
                            <div class="col-md-8">
                                <div class="input-group" >
                                    <span class="input-group-addon" id="sizing-addon2"><i class="fa fa-calendar-o"></i></span>
                                    <input type="text" name="dueDateUpdate" v-model="taskInfo.due_date" class="form-control" id="dueDateUpdate" aria-describedby="sizing-addon2" :disabled="!saveAllowed">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-4 col-form-label">Description
                            </label>
                            <div class="col-md-8">
                                <textarea class="form-control" v-model="taskInfo.description" :disabled="!saveAllowed" contenteditable></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-md-4 col-form-label col-form-label">Resolve Action Remark</label>
                            <div class="col-md-8">
                                <textarea class="form-control" :disabled="!saveAllowed" v-model="taskInfo.remarks" :disabled="!saveAllowed"></textarea>
                            </div>
                        </div>
                        <h4 class="ui horizontal divider header">ORDER</h4>
                        <div class="form-group row">
                            <label for="" class="col-sm-4 col-form-label">Order ID
                            </label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" v-model="taskInfo.transaction_id" placeholder="" aria-describedby="basic-addon1" :disabled="!saveAllowed">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-md-4 col-form-label col-form-label">Set Priority</label>
                            <div class="col-md-8">
                                <select v-model="taskInfo.priority" class="form-control" :disabled="!saveAllowed">
                                    <option value="1">Low</option>
                                    <option value="2">High</option>
                                    <option value="3">Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-md-4 col-form-label col-form-label">Set as urgent Delivery</label>
                            <div class="col-md-8">
                                <input type="checkbox" v-model="taskInfo.is_urgent" name="is_urgent_delivery" v-bind:checked="taskInfo.is_urgent === '1'" class="" id="" :disabled="!saveAllowed">
                                <small style=""></small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-md-4 col-form-label col-form-label">3rd Party Platform</label>
                            <div class="col-md-8">
                                <?php
                                    $platform = array_keys($ThirdPatryPlatform);
                                    $platform = array_combine($platform, $platform);
                                ?>
                                <select v-model="taskInfo.platform" v-on:change="updateVendor()" class="form-control" ref="platform" :disabled="!saveAllowed">
                                    @foreach($platform as $plat_val)
                                         <option value="{{ $plat_val }}">{{ $plat_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-md-4 col-form-label col-form-label">Vendor/Store</label>
                            <div class="col-md-8">
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
                                <input type="hidden" ref="vendorlist" value="{{ htmlentities(json_encode($store), ENT_QUOTES) }}">
                                <select v-model="taskInfo.vendor" class="form-control" ref="vendor" :disabled="!saveAllowed">
                                    <option disabled selected>Please Select Vendor</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" v-if="saveAllowed">
                            <a class="btn btn-default pull-right" v-on:click="updateTask(taskInfo.id)"><i class="fa fa-pencil"></i> Save Task</a>
                        </div>
                    </div>
                    <form>
                        <div id="task-message-pane">
                        <div class="row msg-sbm">
                            <div class="col-md-12" style="background-color: #eaeaea;padding: 5px;padding-bottom: 10px;">
                                <div v-if="saveCommentloading" class="ui active inverted dimmer"> <div class="ui medium text loader"></div></div>
                                <h4 class="ui horizontal divider header"><i class="tag icon"></i>write your message</h4>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <textarea class="form-control" v-model="comments" placeholder="write your message .."></textarea>
                                    </div>
                                </div>
                                <div class="preview-image">
                                    <img style="max-width: 100%; max-height: 100%;" :src="preview" />
                                </div>

                                <a class="btn btn-default pull-right" v-on:click="saveMessage(taskID)"><i class="fa fa-location-arrow"></i> Send Message</a>
                                <label class="btn btn-default pull-right">
                                    <div><i class="fa fa-image"></i> <span v-if="!image">Upload Image</span><span v-else>Change Image</span></div>
                                    {{-- https://stackoverflow.com/questions/55720466/typeerror-cannot-read-property-target-of-undefined --}}
                                    <input class="labelinput-hidden" type="file" v-on:change="function(e){ onFileChange(e) }" accept=".jpg, .png">
                                </label>
                           </div>
                       </div>
                        <div class="message-list">
                            <div class="col-md-12 col-xs-12 msg-row " v-for="msg in messagesData">
                                <table style="width:100%;">
                                    <tr>
                                        <td class="msg-img-box">
                                            <img v-bind:src="msg.image" alt="..." class="img-circle msg-img">
                                        </td>
                                        <td> <div style="font-style:italic;"><strong>@{{msg.name}}</strong></div>
                                            <div style="font-size: 10px;"><small>@{{msg.date}}</small></div>
                                            <div><small>@{{msg.comment}}</small></div>
                                        </td>
                                    </tr>
                                    <tr v-if="msg.attach_path">
                                        <td class="msg-img-box"></td>
                                        <td>
                                            <img v-bind:src="msg.attach_path" style="max-width: 100%; max-height: 100%;">
                                        </td>
                                    </tr>
                                    <!--<tr>-->
                                    <!--    <td> </td>-->
                                    <!--    <td> -->
                                    <!--        <a class="btn btn-default btn-xs pull-right"><i class="fa fa-trash-o"></i></a>-->
                                    <!--        <a class="btn btn-default btn-xs pull-right" style="margin-right:5px;"><i class="fa fa-thumbs-o-up"></i></a>-->
                                    <!--    </td>-->
                                    <!--</tr>-->
                                </table>
                            </div>
                        </div>
                        </div>
                    </form>
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