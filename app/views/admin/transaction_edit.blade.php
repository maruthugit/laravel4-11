@extends('layouts.master')
@section('title', 'Transaction')
@section('content')

@foreach($display_trans as $trans)

<?php
// $tempKey = 'aDmIn';
// Crypt::setKey($tempKey);
$encrypted = Crypt::encrypt($trans->id);
$encrypted = urlencode(base64_encode($encrypted));

$g_po_inv = true;
$tempcoupon = 0;
$tempamount = 0;
$tempcount = 1;
$tempid = $trans->id;

$currency = Config::get('constants.CURRENCY');
$newinvdate = Config::get('constants.NEW_INVOICE_START_DATE');
?>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Transaction Management
                <!-- <span class="pull-right"><a class="btn btn-default" title="" data-toggle="tooltip" href='{{asset('/')}}transaction'}}><i class="fa fa-reply"></i></a></span> -->
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
        @if (Session::has('message'))
            <div class="alert alert-danger">
                <i class="fa fa-exclamation"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
        @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Edit Transaction</h3>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url'=>'transaction/edit/'.$trans->id, 'class' => 'form-horizontal', 'id' => 'main-form')) }}
                            <div class="form-group" >
                            {{ Form::label('transaction_date', 'Transaction Date', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-5">
                                    <p class="form-control-static">{{$trans->transaction_date}}</p>{{Form::input('hidden', 'transaction_date', $trans->transaction_date)}}
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('id', 'Transaction ID', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-5">
                                     <p class="form-control-static">{{$trans->id}}</p>{{Form::input('hidden', 'id', $trans->id)}}
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('invoice', 'Invoice', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-7">
                                    <p class="form-control-static">
                                     <?php
                                        if (isset($trans->invoice_no) && $trans->invoice_no != "")
                                        {
                                            // $file = Config::get('constants.INVOICE_PDF_FILE_PATH') . '/' . urlencode($trans->invoice_no) . '.pdf';
                                            // $encrypted = Crypt::encrypt($file);
                                            // $encrypted = urlencode(base64_encode($encrypted));

                                            $path = Config::get('constants.INVOICE_PDF_FILE_PATH') . '/' . urlencode($trans->invoice_no) . '.pdf';
                                            $file = ($trans->id)."#".($trans->invoice_no)."#".$path;
                                            $encrypted = Crypt::encrypt($file);
                                            $encrypted = urlencode(base64_encode($encrypted));

                                            ?>
                                            {{ HTML::link('transaction/files/'.$encrypted, $trans->invoice_no, array('target'=>'_blank')) }}
                                            <?php
                                        }
                                        else
                                        {
                                            echo "-";
                                        }

                                        echo '|';
                                        //  if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu','sclim'), true ) && in_array($trans->buyer_username, array('lazada','shopee'), true )) { 
                                        //  if (isset($trans->invoice_no) && $trans->invoice_no != "")
                                        // {

                                        //     $path = Config::get('constants.INVOICE_PDF_FILE_PATH') . '/' . urlencode($trans->invoice_no) . '.pdf';
                                        //     $file = ($trans->id)."#".($trans->invoice_no)."#".$path;
                                        //     $encrypted = Crypt::encrypt($file);
                                        //     $encrypted = urlencode(base64_encode($encrypted));

                                        //     ?>
                                            <!-- {{ HTML::link('transaction/agrofiles/'.$encrypted, 'Agro-'.$trans->invoice_no, array('target'=>'_blank')) }} -->
                                             <?php
                                        // }
                                        // else
                                        // {
                                        //     echo "-";
                                        // }
                                        //   echo '|';
                                        //  }
                                        
                                        // $inv = DB::table('jocom_transaction_qoo10')->where('id','=',$trans->id)->first();
                                        // if (isset($inv->invoice_no) && $inv->invoice_no != "")
                                        // {
                                            

                                        //     $path = Config::get('constants.INVOICE_PDF_FILE_PATH') . '/' . urlencode($inv->invoice_no) . '.pdf';
                                        //     $file = ($trans->id)."#".($inv->invoice_no).$path;
                                        //     $encrypted = Crypt::encrypt($file);
                                        //     $encrypted = urlencode(base64_encode($encrypted));

                                        //     ?>
                                           <!--  {{ HTML::link('transaction/files/'.$encrypted, $inv->invoice_no, array('target'=>'_blank')) }} -->
                                             <?php
                                        // }
                                        // else
                                        // {
                                        //     echo "-";
                                        // }
                                        
                                        // echo '|';

                                        if (isset($trans->do_no) && $trans->do_no != "")
                                        {
                                            // $file = Config::get('constants.DO_PDF_FILE_PATH') . '/' . urlencode($trans->do_no) . '.pdf';
                                            // $encrypted = Crypt::encrypt($file);
                                            // $encrypted = urlencode(base64_encode($encrypted));

                                            $file = ($trans->id)."#".($trans->do_no);
                                            $encrypted = Crypt::encrypt($file);
                                            $encrypted = urlencode(base64_encode($encrypted));

                                            ?>
                                            {{ HTML::link('transaction/files/'.$encrypted, $trans->do_no, array('target'=>'_blank')) }}
                                            <?php
                                        }

                                        // Special DO for logistic
                                        if (isset($trans->do_no) && $trans->do_no != "" && file_exists(Config::get('constants.DO_PDF_FILE_PATH') . '/' . urlencode($trans->do_no) . '_logistic.pdf'))
                                        {
                                            echo '|';

                                            $file = Config::get('constants.DO_PDF_FILE_PATH') . '/' . urlencode($trans->do_no) . '_logistic.pdf';
                                            $encrypted = Crypt::encrypt($file);
                                            $encrypted = urlencode(base64_encode($encrypted));
                                            ?>
                                            {{ HTML::link('transaction/files/'.$encrypted, $trans->do_no.'_logistic', array('target'=>'_blank')) }}
                                            <?php
                                        }
                                        
                                        if (isset($trans->customer_po) && $trans->customer_po != "")
                                        {
                                             echo '|';
                                            // $file = Config::get('constants.INVOICE_PDF_FILE_PATH') . '/' . urlencode($trans->invoice_no) . '.pdf';
                                            // $encrypted = Crypt::encrypt($file);
                                            // $encrypted = urlencode(base64_encode($encrypted));

                                            $path = Config::get('constants.INVOICE_PDF_FILE_PATH') . '/' . urlencode($trans->customer_po) . '.pdf';
                                            $file = ($trans->id)."#".($trans->customer_po).$path;
                                       
                                            $encrypted = Crypt::encrypt($file);
                                            $encrypted = urlencode(base64_encode($encrypted));

                                            ?>
                                            {{ HTML::link('transaction/files/'.$encrypted, $trans->customer_po, array('target'=>'_blank')) }}
                                            <?php
                                        }
                                        if (isset($trans->foreign_invoice_no) && $trans->foreign_invoice_no != "")
                                        {
                                             echo '|';
                                        

                                            $path = Config::get('constants.INVOICE_PDF_FILE_PATH') . '/' . urlencode($trans->foreign_invoice_no) . '.pdf';
                                            $file = ($trans->id)."#".($trans->foreign_invoice_no).$path;
                                       
                                            $encrypted = Crypt::encrypt($file);
                                            $encrypted = urlencode(base64_encode($encrypted));

                                            ?>
                                            {{ HTML::link('transaction/files/'.$encrypted, $trans->foreign_invoice_no, array('target'=>'_blank')) }}
                                            <?php
                                        }
                                        
                                        if(isset($purchase_history) && $purchase_history !=""){
                                            echo '|';
                                            $purchase ='PL'.$trans->id;
                                        //     print_r($purchase_history);
                                        // die();
                                            $path = Config::get('constants.PURCHASE_PDF_FILE_PATH') . '/' . urlencode($purchase) . '.pdf';
                                            $file = ($trans->id)."#".'PL'.$path;
                                       
                                            $encrypted = Crypt::encrypt($file);
                                            $encrypted = urlencode(base64_encode($encrypted));

                                            ?>
                                            {{ HTML::link('transaction/files/'.$encrypted, $purchase, array('target'=>'_blank')) }}
                                            <?php

                                        }

                                        $encrypted = Crypt::encrypt($trans->id);
                                        $encrypted = urlencode(base64_encode($encrypted));
                                    ?>
                                    <!-- <span id="gen_po">({{ HTML::link('transaction/newfile/'.$encrypted, 'Generate PO &amp; Invoice &amp; DO') }})</span>  -->
                                    <span id="gen_po"><?php if(Session::get('role_id') == '1' || in_array(Session::get('username'), array("sclim")) || in_array(Session::get('username'), array("winnie")) || in_array(Session::get('username'), array("quenny"))) { ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;({{ HTML::link('transaction/newfile/'.$encrypted, 'Generate PO &amp; Invoice &amp; DO', array('id'=> 'gen_doc', 'onclick'=> 'confirmGen()')) }} | {{ HTML::link('checkout/email/'.$trans->id, 'Send Email', array('id'=> 'sendmail', 'onclick'=> 'confirmMail()')) }})<?php } ?>@if ( ( Permission::CheckAccessLevel(Session::get('role_id'), 14, 7, 'AND') ||  in_array(Session::get('username'), array("sclim")) || in_array(Session::get('username'), array("winnie")) )  && $trans->do_no != '' ) ({{ HTML::link('jlogistic/log/'.$trans->id, 'Log to Logistic App', array('id'=> 'loglogistic', 'onclick'=> 'confirmLogistic()')) }})@endif </span>
                                    </p>
                                </div>
                            </div>
                            <?php 

                            if(in_array(Session::get('username'), Config::get('constants.ACCOUNT_ADMIN'))) {
                            $encryptedCusInv = Crypt::encrypt($trans->id."#CUS");
                            $encryptedCusInv = urlencode(base64_encode($encryptedCusInv));
                            
                            if($display_customer_invoice['isVisible']){ ?>
                            <?php if(!$display_customer_invoice['isGenerated']){ ?>
                            <div class="form-group">
                            {{ Form::label('id', 'Customer Invoice', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-5">
                                     <p class="form-control-static">
                                        <a class="btn btn-primary" href="/transaction/customerinvoice/<?php echo $encryptedCusInv; ?>" onclick="confirmGenCustomer()" id="gen_cus_invoice">Generate Customer Invoice</a>
                                     </p>
                                 </div>
                            </div>
                            <?php }else{ ?>
                            <div class="form-group">
                            {{ Form::label('id', 'Customer Invoice', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-5">
                                     <p class="form-control-static">
                                     {{ HTML::link('transaction/files/'.$encryptedCusInv, $trans->invoice_no, array('target'=>'_blank')) }}
                                     </p>
                                </div>
                            </div>
                            
                            <?php 
                            } 
                            }
                            }
                            ?>
                            @if (count($display_parent_inv) > 0)
                            <div class="form-group" style="display:none" >
                            {{ Form::label('invoice', 'Invoice e37', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-7">
                                    <p class="form-control-static">
                                     <?php

                                        foreach ($display_parent_inv as $parentInv)
                                        {
                                            if (isset($parentInv->parent_inv))
                                            {
                                                

                                                $path = Config::get('constants.INVOICE_PARENT_PDF_FILE_PATH') . '/' . urlencode($parentInv->parent_inv) . '.pdf';
                                                $file = ($parentInv->transaction_id)."#".($parentInv->parent_inv).'#'.$path;
                                                $encrypted = Crypt::encrypt($file);
                                                $encrypted = urlencode(base64_encode($encrypted));

                                                ?>
                                                {{ HTML::link('transaction/files/'.$encrypted, $parentInv->parent_inv, array('target'=>'_blank')) }}
                                                <?php
                                            }
                                            else
                                            {
                                                echo "-";
                                            }

                                            echo '|';
                                        }
                                    ?>
                                    </p>
                                </div>
                            </div>
                            @endif

                            <div class="form-group">
                            {{ Form::label('status', 'Status', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::select('status', array('pending' => 'Pending', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'refund' => 'Refund'), $trans->status, ['class'=>'form-control'])}}
                                    {{Form::input('hidden', 'ori_status', $trans->status)}}
                                    <?php if(isset($trans->status) && $trans->status != "completed"){$g_po_inv = false; }?>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('buyer_username', 'Buyer', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    <p class="form-control-static">{{$trans->buyer_username}}</p>{{Form::input('hidden', 'buyer_username', $trans->buyer_username)}}
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('lang', 'Language', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    <p class="form-control-static">{{$trans->lang}}</p>{{Form::input('hidden', 'lang', $trans->lang)}}
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('delivery_name')) has-error @endif">
                            {{ Form::label('delivery_name', 'Delivery Name', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{ Form::text('delivery_name', $trans->delivery_name, array('required'=>'required', 'class'=> 'form-control')) }}
                                    <p class="help-block" for="inputError">{{$errors->first('delivery_name')}}</p>
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('delivery_contact_no')) has-error @endif">
                            {{ Form::label('delivery_contact_no', 'Delivery Contact', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{ Form::text('delivery_contact_no', $trans->delivery_contact_no, array('required'=>'required', 'class'=> 'form-control')) }}
                                    <p class="help-block" for="inputError">{{$errors->first('delivery_contact_no')}}</p>
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('delivery_addr_1')) has-error @endif">
                            {{ Form::label('delivery_addr_1', 'Delivery Address', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-5">
                                    {{ Form::text('delivery_addr_1', $trans->delivery_addr_1, array('required'=>'required', 'class'=> 'form-control')) }}
                                    <p class="help-block" for="inputError">{{$errors->first('delivery_addr_1')}}</p>
                                    {{ Form::text('delivery_addr_2', $trans->delivery_addr_2, array('required'=>'required', 'class'=> 'form-control')) }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('delivery_postcode')) has-error @endif">
                            {{ Form::label('delivery_postcode', 'Delivery Postcode', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{ Form::text('delivery_postcode', $trans->delivery_postcode, array('required'=>'required', 'class'=> 'form-control')) }}
                                    <p class="help-block" for="inputError">{{$errors->first('delivery_postcode')}}</p>
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('delivery_state')) has-error @endif">
                            {{ Form::label('delivery_city', 'Delivery City', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{ Form::text('delivery_city', $trans->delivery_city, array('required'=>'required', 'class'=> 'form-control')) }}
                                    <p class="help-block" for="inputError">{{$errors->first('delivery_city')}}</p>
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('delivery_state')) has-error @endif">
                            {{ Form::label('delivery_state', 'Delivery State', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{ Form::text('delivery_state', $trans->delivery_state, array('required'=>'required', 'class'=> 'form-control')) }}
                                    <p class="help-block" for="inputError">{{$errors->first('delivery_state')}}</p>
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('delivery_country')) has-error @endif">
                            {{ Form::label('delivery_country', 'Delivery Country', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{ Form::text('delivery_country', $trans->delivery_country, array('required'=>'required', 'class'=> 'form-control')) }}
                                    <p class="help-block" for="inputError">{{$errors->first('delivery_country')}}</p>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group">
                            {{ Form::label('process_fees', "Processing Fees ($currency)", array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-5">
                                     <p class="form-control-static">{{ number_format($trans->process_fees,2) }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('delivery_charges', "Delivery Charges ($currency)", array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-5">
                                     <p class="form-control-static">{{ number_format($trans->delivery_charges,2) }}</p>
                                     <span class="text-danger">* {{$trans->delivery_condition}}</span>
                                </div>
                            </div>
                            <?php if ($trans->invoice_bussines_currency != 'MYR') { ?>
                            <div class="form-group">
                            {{ Form::label('delivery_charges', "Delivery Charges ($trans->invoice_bussines_currency)", array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-5">
                                     <p class="form-control-static">{{ number_format($trans->foreign_delivery_charges,2) }}</p>
                                     <span class="text-danger">* {{$trans->delivery_condition}}</span>
                                </div>
                            </div>
                            <?php } ?>

                            <?php
                                isset($display_coupon->coupon_code) ? $tempcoupon = $display_coupon->coupon_code : 0;
                                isset($display_coupon->coupon_amount) ? $tempamount = $display_coupon->coupon_amount : 0;
                            ?>

                            <div class="form-group">
                            {{ Form::label('coupon_code', 'Coupon Code', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-5">
                                     <p class="form-control-static">({{$tempcoupon}}) {{number_format($tempamount,2)}}</p>
                                </div>
                            </div>

                            @if (isset($display_agent))
                                <div class="form-group">
                                {{ Form::label('display_agent', 'Agent Code', array('class'=> 'col-lg-2 control-label')) }}
                                     <div class="col-lg-5">
                                         <p class="form-control-static">{{ $display_agent }}</p>
                                    </div>
                                </div>
                            @endif

                            <?php $total_points_amount = 0; ?>
                            @foreach ($display_points as $display_point)
                            <div class="form-group">
                                {{ Form::label('', 'Redeemed '.$display_point->type, array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-5">
                                    <p class="form-control-static">({{ $display_point->point }}) {{ number_format($display_point->amount, 2, '.', '') }}</p>
                                    <?php $total_points_amount += $display_point->amount; ?>
                                </div>
                            </div>
                            @endforeach

                            <div class="form-group">
                            {{ Form::label('gst_total', 'Total GST at '.$trans->gst_rate."% ($currency)", array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-5">
                                     <p class="form-control-static">{{$trans->gst_total}}</p>
                                </div>
                            </div>
                            
                            <?php if ($trans->invoice_bussines_currency != 'MYR') { ?>
                            <div class="form-group @if ($errors->has('total_amount')) has-error @endif">
                            {{ Form::label('total_amount', "Total Amount ($trans->invoice_bussines_currency)", array('disabled','disabled','class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{ Form::text('total_amount', number_format(abs($trans->foreign_total_amount), 2, '.', ''), array('required'=>'required','disabled'=>'disabled', 'class'=> 'form-control')) }}
                                  
                                </div>
                            </div>
                            
                            
                            <?php } ?>

                            <div class="form-group @if ($errors->has('total_amount')) has-error @endif">
                            {{ Form::label('total_amount', "Total Amount ($currency)", array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{ Form::text('total_amount', number_format(abs($trans->total_amount-$tempamount+$trans->gst_total-$display_point->amount), 2, '.', ''), array('required'=>'required', 'class'=> 'form-control')) }}
                                    <p class="help-block" for="inputError">{{$errors->first('total_amount')}}</p>
                                    {{Form::input('hidden', 'coupon_amount', $tempamount)}}
                                    {{Form::input('hidden', 'gst_total', $trans->gst_total)}}
                                    {{Form::input('hidden', 'point_amount', $total_points_amount)}}
                                </div>
                            </div>

                            @foreach ($display_earns as $earn)
                                <div class="form-group">
                                    {{ Form::label('', 'Earned '.$earn->type, array('class' => 'col-lg-2 control-label')) }}
                                    <div class="col-lg-3">
                                        <p class="form-control-static">{{ $earn->point }}</p>
                                    </div>
                                </div>
                            @endforeach

                            @if (isset($display_bpoint))
                                <div class="form-group">
                                    {{ Form::label('', 'Earned BPoint', ['class' => 'col-lg-2 control-label']) }}
                                    <div class="col-lg-3">
                                        <p class="form-control-static">{{ $display_bpoint }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                            {{ Form::label('special_msg', 'Special Message', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-5">
                                    {{ Form::textarea('special_msg', $trans->special_msg, array('class'=> 'form-control')) }}
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('remark', 'Remark (Internal Use)', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-5">
                                    <p class="form-control-static">
                                        <?php 
                                         echo nl2br($trans->remark);
                                         ?>
                                    </p>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-5">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editModal" data-id="{{ $trans->id }}" data-remark="" >Add Remark</button>
                                </div>                                
                            </div>
                            
                            <hr />

                            <div class="form-group">
                                {{ Form::label('', 'External Order/Reference Number', ['class' => 'col-lg-2 control-label']) }}
                                <div class="col-lg-3">
                                    <input type="text" class="form-control" value="<?php echo $trans->external_ref_number; ?>" id="external_ref_number" name="external_ref_number" >
                                    <small>External platform order number</small>
                                </div>
                            </div>

                             <hr />

                            <div class="form-group">
                                {{ Form::label('', 'Delivery Status', ['class' => 'col-lg-2 control-label']) }}
                                <div class="col-lg-3">
                                    <p class="form-control-static">{{ $display_delivery_status }}</p>
                                </div>
                            </div>
                            <?php 

                                $status = Parcel::getArray();
                            ?>

                            <div class="form-group">
                                {{ Form::label('parcel_status', 'Parcel Status', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::select('parcel_status', $status, $trans->parcel_status, ['class'=>'form-control']);}} 
                                </div>
                            </div>
                             <hr />

                             <div class="row">
                                <div class="col-lg-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            Details
                                        </div>
                                        <!-- /.panel-heading -->
                                        <div class="panel-body">
                                            <div class="dataTable_wrapper">
                                                <table class="table table-striped table-bordered table-hover" id="dataTables-details">
                                                    <thead>
                                                       <tr>
                                                            <th>#</th>
                                                            <th>Seller</th>
                                                            <th>Product Name</th>
                                                            <th>Label</th>
                                                            <th>SKU<br>Seller SKU</th>
                                                            <th>Qty</th>
                                                            <th>Price</th>
                                                            <!-- Validate New Invoice  -->
                                                             @if ($trans->transaction_date<$newinvdate) 
                                                            <th>Coupon Disc</th>
                                                             @endif
                                                            <th>GST</th>
                                                            <th>Delivery Time</th>
                                                            <th>Total</th>
                                                            <th>Referral Fees</th>
                                                            <th>GST Seller</th>
                                                            <th>PO</th>
                                                            <th>Action</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($display_details as $trans_details)
                                                            <tr class="odd gradeX">
                                                                <td>{{$tempcount++}}</td>
                                                                <td>{{$trans_details->seller_username}}</td>
                                                                <td>{{$trans_details->name}}</td>
                                                                <td>{{$trans_details->price_label}}</td>                                                               
                                                                <td>{{$trans_details->sku}}<br>{{$trans_details->seller_sku}}</td>
                                                                <td>{{$trans_details->unit}}</td>
                                                                <td>{{number_format($trans_details->price,2)}}</td>
                                                                @if ($trans->transaction_date<$newinvdate)
                                                                <td>{{number_format($trans_details->disc,2)}}</td>
                                                                 @endif
                                                                <td>{{number_format($trans_details->gst_amount,2)}}</td>
                                                                <td>{{$trans_details->delivery_time}}</td>
                                                                <td>{{number_format($trans_details->total,2)}}</td>
                                                                <td>{{$trans_details->p_referral_fees}}<?php echo (isset($trans_details->p_referral_fees_type) && $trans_details->p_referral_fees_type == 'N' ? ' Nett/Unit' : ' %');?></td>
                                                                <!-- <td>{{Form::text("gst_seller[$trans_details->id]", number_format($trans_details->gst_seller,2), array('class'=>'form-control'))}}</td> -->
                                                                <td>{{number_format($trans_details->gst_seller,2)}}</td>
                                                                <td>
                                                                    <?php
                                                                        if (isset($trans_details->po_no))
                                                                        {
                                                                            // $file = Config::get('constants.PO_PDF_FILE_PATH') . '/' . urlencode($trans_details->po_no) . '.pdf';
                                                                            // $encrypted = Crypt::encrypt($file);
                                                                            // $encrypted = urlencode(base64_encode($encrypted));

                                                                            $path = Config::get('constants.PO_PDF_FILE_PATH') . '/' . urlencode($trans_details->po_no) . '.pdf';
                                                                            $file = ($trans->id)."#".($trans_details->po_no)."#". $path;
                                                                            $encrypted = Crypt::encrypt($file);
                                                                            $encrypted = urlencode(base64_encode($encrypted));

                                                                            ?>
                                                                            {{ HTML::link('transaction/files/'.$encrypted, $trans_details->po_no, array('target'=>'_blank')) }}
                                                                            <?php
                                                                        }
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                        if (isset($trans_details->parent_po))
                                                                        {
                                                                            // $file = Config::get('constants.PO_PARENT_PDF_FILE_PATH') . '/' . urlencode($trans_details->parent_po) . '.pdf';
                                                                            // $encrypted = Crypt::encrypt($file);
                                                                            // $encrypted = urlencode(base64_encode($encrypted));

                                                                            // $path = Config::get('constants.PO_PARENT_PDF_FILE_PATH') . '/' . urlencode($trans_details->parent_po) . '.pdf';
                                                                            // $file = ($trans->id)."#".($trans_details->parent_po)."#". $path;
                                                                            // $encrypted = Crypt::encrypt($file);
                                                                            // $encrypted = urlencode(base64_encode($encrypted));
                                                                            
                                                                            ?>
                                                                            <!--{{ HTML::link('transaction/files/'.$encrypted, $trans_details->parent_po, array('target'=>'_blank')) }}-->
                                                                            <?php
                                                                        }
                                                                    ?>
                                                                    <input type="hidden" class="form-control" value="<?php echo $trans_details->name; ?>" id="item_productname" name="item_productname" >
                                                                    <a class="btn btn-primary addPurchase" title="" data-toggle="tooltip" href="#" data-id="<?php echo $trans_details->id?>" data-name="<?php echo $trans_details->name?>">
                                                                    <i class="fa fa-plus"></i> Add Purchase </a></td>
                                                                </td>
                                                                <!-- @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 9, 'AND'))
                                                                <td> <a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_details({{$trans_details->id;}});">
                                                                <i class="fa fa-remove"></i></a></td>
                                                                @endif -->
                                                            </tr>
                                                        @endforeach
                                                            <!-- <tr class="odd gradeX">
                                                                <td></td>
                                                                <td>{{Form::text('seller_username', null, array('class'=>'form-control'))}}</td>
                                                                <td>{{Form::text('price_label', null, array('class'=>'form-control'))}}</td>
                                                                <td>{{Form::text('sku', null, array('class'=>'form-control'))}}</td>
                                                                <td>{{Form::text('seller_sku', null, array('class'=>'form-control'))}}</td>
                                                                <td>{{Form::text('unit', null, array('class'=>'form-control'))}}</td>
                                                                <td>{{Form::text('price', null, array('class'=>'form-control'))}}</td>
                                                                <td>{{Form::text('delivery_fees', null, array('class'=>'form-control'))}}</td>
                                                                <td>{{Form::text('delivery_time', null, array('class'=>'form-control'))}}</td>
                                                                <td>{{Form::text('total', null, array('class'=>'form-control'))}}</td>
                                                                @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 3, 'AND'))
                                                                <td colspan="3">
                                                                {{ Form::button('Add', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}
                                                                </td>
                                                                @endif
                                                            </tr>     -->
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- /.table-responsive -->

                                        </div>
                                        <!-- /.panel-body -->
                                    </div>
                                    <!-- /.panel -->
                                </div>
                                <!-- /.col-lg-12 -->
                            </div>
                            <!-- /.row -->
                            <hr />
                            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 3, 'AND'))
                            <div class="form-group">
                                <div class="col-lg-12">
                                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                    {{ Form::button('Save', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'confirmSubmit()'))}}
                                </div>
                            </div>
                            @endif
                        {{ Form::close() }}
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
            <!-- /.panel -->
             <div class="panel panel-default">
                <!--<div class="float-right text-right">-->

                <!--    <a class="btn btn-primary addPurchase" id="add-lnkproduct-btn"  href="#" ><i class="fa fa-plus"></i> Add Purchase List</a>-->
                <!--</div>-->
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Purchase History</h3>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <div class="form-group">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Receipt #</th>
                                        <th>Supplier Name</th>
                                        <th>Product ID</th>
                                        <th>Product Name</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Total Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($purchase_history as $key => $value) { ?>
                                    <tr>
                                        <td>{{$value->date_of_purchase}}</td>
                                        <td>{{$value->ref_no}}</td>
                                        <td>{{$value->seller_company}}</td>
                                        <td>{{$value->product_id}}</td>
                                        <td>{{$value->product_name}}</td>
                                        <td>{{$value->price}}</td>
                                        <td>{{$value->product_qty}}</td>
                                        <td>{{$value->amount}}</td>
                                         <td>
                                            <a class="btn btn-primary editPH" title="Edit" data-toggle="tooltip" data-id="<? echo $value->id ?>" href="#"><i class="fa fa-pencil"></i></a>
                                            <a class="btn btn-danger deletePH" title="status" data-toggle="tooltip" data-id="<? echo $value->id ?>" href="#"><i class="fa fa-times-circle" style="font-size:17px"></i></a>
                                        </td> 
                                    </tr>
                                    <?php }?>
                                    <?php if(!isset($purchase_history)){?>
                                    <tr id="emptyproduct">
                                        <td colspan="4" style="text-align:center;">No Purchase Found.</td>
                                    </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            
            <!-- /.panel -->
            @if ($trans->buyer_username == 'lazada')
             <div class="panel panel-default">
                <div class="float-right text-right">

                    <a class="btn btn-primary addPL" id="add-profit-loss-btn"  href="#" ><i class="fa fa-plus"></i> Add Lazada P&L</a>
                </div>
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Lazada Profit & Loss Information</h3>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <div class="form-group">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Commission</th>
                                        <th>Payment Fee</th>    
                                        <th>LazCoins Disc Promo Fee</th>
                                        <th>Shipping Fee (Paid By Cust)</th>
                                        <th>Item Price Credit</th>
                                        <th>Grand Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        // echo '<pre>';
                                        // print_r($lazada_profitloss);
                                        // echo '</pre>';
                                    foreach ($lazada_profitloss as $key => $value) { 
                                        $total=0;
                                        $grand_total=0;
                                        $total=($value->commission+$value->campaign_fee+$value->lazcoins_discount+$value->shipping_fee_voucher_by_lazada+$value->platform_shippingfee_subsidy_tax+$value->shipping_fee_paid_by_cus+$value->payment_fee+$value->lazcoins_discount_promotion_fee);
                                        $grand_total=$value->item_price_credit + $total;
                                        ?>
                                    <tr>
                                        <td>{{$value->transaction_id}}</td>
                                        <td>{{$value->commission}}</td>
                                        <td>{{$value->payment_fee}}</td>
                                        <td>{{$value->lazcoins_discount_promotion_fee}}</td>
                                        <td>{{$value->shipping_fee_paid_by_cus}}</td>
                                        <td>{{$value->item_price_credit}}</td>
                                        <td>{{$grand_total}}</td>
                                         <td>
                                            <a class="btn btn-primary editPL" title="Edit" data-toggle="tooltip" data-id="{{$value->id}}" href="#"><i class="fa fa-pencil"></i></a>
                                            <a class="btn btn-danger deletePL" title="status" data-toggle="tooltip" data-id="{{$value->id}}" href="#"><i class="fa fa-times-circle" style="font-size:17px"></i></a>
                                        </td> 
                                    </tr>
                                    <?php }?>
                                    <?php if(count($lazada_profitloss) ==0){?>
                                    <tr id="emptyproduct">
                                        <td colspan="8" style="text-align:center;">No Data Found.</td>
                                    </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            @endif 
            
            <!-- /.panel -->
            @if ($trans->buyer_username == 'shopee')
             <div class="panel panel-default">
                <div class="float-right text-right">
                    <a class="btn btn-primary addshopeePL @if(count($shopee_profitloss) >0) disabled @endif" id="add-shopee-profit-loss-btn"  href="#" ><i class="fa fa-plus"></i> Add Shopee P&L</a>
                </div>
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Shopee Profit & Loss Information</h3>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <div class="form-group">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Shipping Subtotal</th>
                                        <th>Commission Fee(Incl.SST)</th> 
                                        <th>Service Fee</th>    
                                        <th>Transaction Fee (Incl. SST)</th>
                                        <th>Fees & Charges</th>
                                        <th>Item Price Credit</th>
                                        <th>Grand Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        // echo '<pre>';
                                        // print_r($lazada_profitloss);
                                        // echo '</pre>';
                                    foreach ($shopee_profitloss as $key => $value) { 
                                        $shipping_subtotal = 0;
                                        $fees = 0;
                                        $total=0;
                                        $grand_total=0;
                                        $shipping_subtotal = ($value->shippingfee_paid_by_buyer + $value->shippingfee_charged_by_logistic_provider + $value->seller_paid_shippingfee);
                                        $fees = ($value->commission + $value->service_fee + $value->transaction_fee + $value->saver_programme_fee+ $value->ams_commission_fee+ $value->other_fee);
                                        $total=($value->product_discount_rebate+$shipping_subtotal+$fees);
                                        $grand_total=$value->item_price_credit + $total;
                                        ?>
                                    <tr>
                                        <td>{{$value->transaction_id}}</td>
                                        <td>{{$shipping_subtotal}}</td>
                                        <td>{{$value->commission}}</td>
                                        <td>{{$value->service_fee}}</td>
                                        <td>{{$value->transaction_fee}}</td>
                                        <td>{{$fees}}</td>
                                        <td>{{$value->item_price_credit}}</td>
                                        <td>{{$grand_total}}</td>
                                         <td>
                                            <a class="btn btn-primary editShopeePL" title="Edit" data-toggle="tooltip" data-id="{{$value->id}}" href="#"><i class="fa fa-pencil"></i></a>
                                            <a class="btn btn-danger deleteShopeePL" title="status" data-toggle="tooltip" data-id="{{$value->id}}" href="#"><i class="fa fa-times-circle" style="font-size:17px"></i></a>
                                        </td> 
                                    </tr>
                                    <?php }?>
                                    <?php if(count($shopee_profitloss) ==0){?>
                                    <tr id="emptyproduct">
                                        <td colspan="9" style="text-align:center;">No Data Found.</td>
                                    </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            @endif 
            
            <!-- /.panel -->
            @if ($trans->buyer_username == 'fntiktok')
             <div class="panel panel-default">
                <div class="float-right text-right">
                    <a class="btn btn-primary addTiktokPL @if(count($tiktok_profitloss) >0) disabled @endif" id="add-tiktok-profit-loss-btn"  href="#" ><i class="fa fa-plus"></i> Add Tiktok P&L</a>
                </div>
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Tiktok Profit & Loss Information</h3>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <div class="form-group">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Total Revenue</th>
                                        <th>Subtotal before Discounts</th> 
                                        <th>Total Fees</th>    
                                        <th>Transaction fee</th>
                                        <th>Commission fee(Tiktok Shop)</th>
                                        <th>Total Settlement amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        // echo '<pre>';
                                        // print_r($lazada_profitloss);
                                        // echo '</pre>';
                                    foreach ($tiktok_profitloss as $key => $value) { 
                                        $shipping_subtotal = 0;
                                        $fees = 0;
                                        $total=0;
                                        $grand_total=0;
                                        $total_revenue = ($value->subtotal_before_discounts + $value->seller_discounts);
                                        $fees = ($value->transaction_fee + $value->commission_fee + $value->actual_shipping_fee + $value->platform_shipping_fee_discount+ $value->customer_shipping_fee_before_discounts+ $value->seller_shipping_fee_discount+ $value->tiktokshop_shipping_fee_discount+ $value->actual_return_shipping_fee+ $value->refunded_customer_shipping_fee+ $value->shipping_subsidy+ $value->affiliate_commission+ $value->bonus_cashback_service_fee+$value->voucher_xtra_service_fee+ $value->other_fees);
                                        $total=($value->product_discount_rebate+$shipping_subtotal+$fees);
                                        $grand_total=$total_revenue + $fees;

                                        ?>
                                    <tr>
                                        <td>{{$value->transaction_id}}</td>
                                        <td>{{$total_revenue}}</td>
                                        <td>{{$value->subtotal_before_discounts}}</td>
                                        <td>{{$fees}}</td>
                                        <td>{{$value->commission_fee}}</td>
                                        <td>{{$value->transaction_fee}}</td>
                                        <td>{{$grand_total}}</td>
                                         <td>
                                            <a class="btn btn-primary editTiktokPL" title="Edit" data-toggle="tooltip" data-id="{{$value->id}}" href="#"><i class="fa fa-pencil"></i></a>
                                            <a class="btn btn-danger deleteTiktokPL" title="status" data-toggle="tooltip" data-id="{{$value->id}}" href="#"><i class="fa fa-times-circle" style="font-size:17px"></i></a>
                                        </td> 
                                    </tr>
                                    <?php }?>
                                    <?php if(count($tiktok_profitloss) ==0){?>
                                    <tr id="emptyproduct">
                                        <td colspan="9" style="text-align:center;">No Data Found.</td>
                                    </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            @endif 
            
            <!-- Panel PayPal Payment Details -->
            @foreach($display_paypal as $trans_paypal)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> PayPal Transaction</h3>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <div class="form-group">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Transaction date</th>
                                        <th>Paypal Transaction ID</th>
                                        <th>Paypal Status</th>
                                        <th>Paypal Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{$trans_paypal->insert_date}}</td>
                                        <td>{{$trans_paypal->txn_id}}</td>
                                        <td>{{$trans_paypal->payment_status}}</td>
                                        <td>{{nl2br($trans_paypal->tran_data)}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            @endforeach
            <!-- /.panel -->

            <!-- Panel MOLPay Payment Details -->
            @foreach($display_molpay as $trans_molpay)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> MOLPay Transaction</h3>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <div class="form-group">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Transaction date</th>
                                        <th>MOLPay Transaction ID</th>
                                        <th>MOLPay Status</th>
                                        <th>MOLPay Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{$trans_molpay->insert_date}}</td>
                                        <td>{{$trans_molpay->txn_id}}</td>
                                        <td>{{$trans_molpay->payment_status}}</td>
                                        <td>{{nl2br($trans_molpay->tran_data)}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            @endforeach
            <!-- /.panel -->
            <!-- Panel RevPay Payment Details -->
            @foreach($display_revpay as $trans_molpay)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> RevPay Transaction</h3>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <div class="form-group">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Transaction date</th>
                                        <th>RevPay Transaction ID</th>
                                        <th>RevPay Status</th>
                                        <th>RevPay Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{$trans_molpay->insert_date}}</td>
                                        <td>{{$trans_molpay->txn_id}}</td>
                                        <td>{{$trans_molpay->payment_status}}</td>
                                        <td>{{nl2br($trans_molpay->tran_data)}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            @endforeach
            <!-- /.panel -->
            <!-- Panel MPay Payment Details -->
            @foreach($display_mpay as $trans_mpay)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Manage Pay Transaction</h3>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <div class="form-group">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Transaction date</th>
                                        <th>ManagePay Transaction ID</th>
                                        <th>ManagePay Status</th>
                                        <th>ManagePay Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{$trans_mpay->insert_date}}</td>
                                        <td>{{$trans_mpay->txn_id}}</td>
                                        <td>{{$trans_mpay->payment_status}}</td>
                                        <td>{{nl2br($trans_mpay->tran_data)}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            @endforeach
            <!-- /.panel -->
            
            <!-- Panel Boost Payment Details -->
            @foreach($display_boost as $trans_boost)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Boost Transaction</h3>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <div class="form-group">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Transaction date</th>
                                        <th>Boost Reference No</th>
                                        <th>Boost Status</th>
                                        <th>Boost Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{$trans_boost->transaction_time}}</td>
                                        <td>{{$trans_boost->boost_refnum}}</td>
                                        <td>{{$trans_boost->transaction_status}}</td>
                                        <td>@foreach(json_decode($trans_boost->json_data, true) as $key => $value)
                                                {{ $key }} - {{ $value }},<br> 
                                            @endforeach</td>
                                        <!--<td>{{nl2br($trans_boost->json_data)}}</td>-->
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            @endforeach
            <!-- /.panel -->
            <!-- Panel GrabPay Payment Details -->
            @foreach($display_grab as $trans_grab)
             <?php //$jsonData = json_decode($trans_boost->json_data, true); ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> GrabPay Transaction</h3>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <div class="form-group">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Transaction date</th>
                                        <th>Grab Transaction No</th>
                                        <th>Payment Status</th>
                                        <th>Grab Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{$trans_grab->created_at}}</td>
                                        <td>{{$trans_grab->txid}}</td>
                                        <td>{{$trans_grab->status}}</td>
                                        <td>@foreach(json_decode($trans_grab->api_response, true) as $key => $value)
                                                {{ $key }} - {{ $value }},<br> 
                                            @endforeach</td>
                                        <!--<td>{{nl2br($jsonData)}}</td>-->
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            @endforeach
            <!-- /.panel -->  
            <!-- Panel PacePay Payment Details -->
            @foreach($display_Pace as $trans_pace)
             <?php //$jsonData = json_decode($trans_boost->json_data, true); ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> PacePay Transaction</h3>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <div class="form-group">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Transaction date</th>
                                        <th>Pace Transaction</th>
                                        <th>Transaction ID</th>
                                        <th>Amount</th>
                                        <th>Payment Status</th>
                                        <th>Pace Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{$trans_pace->creation_date}}</td>
                                        <td>{{$trans_pace->pace_transaction_id}}</td>
                                        <td>{{$trans_pace->transaction_id}}</td>
                                        <td><?php $amount=number_format($trans_pace->amount, 2);?>{{$amount}}</td>
                                        <td>{{$trans_pace->status}}</td>
                                        <td>@foreach(json_decode($trans_pace->json_data, true) as $key => $value)
                                            @if($key!="callback")
                                               <?php if($key=="amount"){
                                               $val=json_decode($value, true);?>
                                               {{ $key }} - <b>{{$val['value']}}</b><br>
                                               {{Currency}} - <b>{{$val['currency']}}</b><br>
                                               {{ActualValue}} - <b>{{$val['actualValue']}}</b>
                                               <?php 
                                               }else{?>
                                                {{ $key }} - <b>{{ $value }}</b><br>
                                                <?php } ?>
                                                @endif
                                               
                                            @endforeach
                                            </br>
                                             Creation Date - <b>{{$trans_pace->creation_date}}</b></br>
                                             Expiry Date - <b>{{$trans_pace->expiry_date}}</b><br>
                                             Updated Date - <b>{{$trans_pace->update_date}}</b><br>
                                            Status - <b>{{$trans_pace->status}}</b><br>
                                            Token - <b>{{$trans_pace->token}}</b><br>
                                            Payment Link - <b>{{$trans_pace->payment_link}}</b><br>
                                             </td>
                                        <!--<td>{{nl2br($jsonData)}}</td>-->
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            @endforeach
            <!-- /.panel -->  
            <!-- Panel FavePay Payment Details -->
            @foreach($display_Fave as $trans_fave)
             <?php //$jsonData = json_decode($trans_boost->json_data, true); ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> FavePay Transaction</h3>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <div class="form-group">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Transaction date</th>
                                        <th>Fave Transaction No</th>
                                        <th>Payment Status</th>
                                        <th>Fave Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{$trans_fave->created_at}}</td>
                                        <td>{{$trans_fave->receipt_id}}</td>
                                        <td>{{$trans_fave->status}}</td>
                                        <td>@foreach(json_decode($trans_fave->json_data, true) as $key => $value)
                                                {{ $key }} - {{ $value }},<br> 
                                            @endforeach</td>
                                        <!--<td>{{nl2br($jsonData)}}</td>-->
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            @endforeach
            <!-- /.panel -->  
            
        </div>
        <!-- /.col-lg-12 -->
    </div>
    </div>

@endforeach


{{ Form::open(array('url'=>'transaction/remove', 'id'=>'remove_frm')) }}
{{Form::input('hidden', 'remove_details_id', null, ['id'=>'remove_details_id'])}}
{{Form::input('hidden', 'transID', $tempid, ['id'=>'remove_details_id'])}}
{{ Form::close() }}

<div class="modal fade" id="warning" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Notice</h4>
            </div>
            <div class="modal-body">
                The points earned from this transaction has been redeemed by the buyer, so the buyer's point will deduct to negative value after refund.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Remark</h4>
            </div>
            <div class="modal-body">
                {{ Form::open(array('url'=>'transaction/addremark/'.$trans->id, 'class' => 'form-horizontal')) }}
                    <input type="hidden" id="addRemarkId" name="remarkId">
                    <div class="form-group">
                        <label for="remark" class="col-sm-2 control-label">Remark</label>
                        <div class="col-sm-10">
                            <!-- <input type="textarea" class="form-control" id="remark" name="remark" placeholder="Remark" required> -->
                            {{ Form::textarea('remark', '', array('class'=> 'form-control', 'placeholder'=> 'Remark', 'id'=> 'remark', 'rows'=> '5', 'required'=> 'required')) }}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary pull-right">Add</button>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addPurchaseModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Purchase List</h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(array('class' => 'form-horizontal')) }}
                          <!-- {{Form::input('hidden', 'item_hidden_id', '')}}  -->
                          <div class="form-group">
                                {{ Form::label('item_name_lbl', 'Product Name', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-7">
                                 {{ Form::label('item_name', '', array('lass'=> 'control-label')) }}
                                </div>
                            </div>
                           <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                            {{ Form::label('item_price', 'Price', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('item_price', null, array('required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('unit_price')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                            {{ Form::label('item_qty', 'Qty', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('item_qty', null, array('required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('unit_price')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                            <!-- {{ Form::label('item_amount', 'Total Amount ($currency)', array('class'=> 'col-lg-4 control-label')) }} -->
                            {{ Form::label('totalamount', "Total Amount ($currency)", array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    <!-- {{Form::text('item_amount', null, array('required'=>'required', 'class'=>'form-control lockinput'))}} -->
                                      <input type="text" readonly class="form-control-plaintext" name="item_amount" id="item_amount" value="">


                                     <p class="help-block" for="inputError">{{$errors->first('unit_price')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                                <hr>
                            </div>
                            
                            <div class="form-group">
                                {{ Form::label('purchase_date', 'Date', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('purchase_date', null, array('id'=>'datepicker','placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                            {{ Form::label('receipt_no', 'Receipt No.', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('receipt_no', null, array('class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('unit_price')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('suppliers')) has-error @endif">
                            {{ Form::label('suppliers', 'Suppliers', array('class'=> 'col-lg-4 control-label')) }}
                            <div class="col-lg-3">
                                    <select class="form-control" id="seller_name2" name="seller_name2">
                                        <option value="">Select Seller Name</option>
                                        <?php foreach ($sellersOptions as $key => $value) { ?>
                                            <option value="<?php echo $key;?>"><?php echo $value;?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="form-group @if ($errors->has('totalamount')) has-error @endif">
                            {{ Form::label('totalamount', "Total Amount ($currency)", array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('totalamount', null, array('class'=>'form-control lockinput'))}}
                                    <p class="help-block" for="inputError">{{$errors->first('totalamount')}}</p>
                                </div>
                                
                            </div> -->
                            <div class="form-group @if ($errors->has('purchase_remark')) has-error @endif">
                            {{ Form::label('purchase_remark', "Remarks", array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-7">
                                     {{ Form::textarea('purchase_remark', null, ['class' => 'form-control', 'rows' => '3']) }}
                                    <p class="help-block" for="inputError">{{$errors->first('purchase_remark')}}</p>
                                </div>
                                
                            </div>
                             
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-10">
                             <input type="hidden" id="trans_id" name="trans_id" value="{{$trans->id}}">
                              <input type="hidden" id="item_hidden_id" name="item_hidden_id" value="">
                                <button type="submit" id="savepurchase"  class="btn btn-primary pull-left">Save</button>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
</div>

<div class="modal fade" id="editPurchaseModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Purchase List</h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(array('class' => 'form-horizontal')) }}
                          <!-- {{Form::input('hidden', 'item_hidden_id', '')}}  -->
                          <div class="form-group">
                                {{ Form::label('item_name_lbl', 'Product Name', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-7">
                                 {{ Form::label('ed_item_name', '', array('lass'=> 'control-label')) }}
                                </div>
                            </div>
                           <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                            {{ Form::label('ed_item_price', 'Price', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('ed_item_price', null, array('required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('unit_price')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                            {{ Form::label('ed_item_qty', 'Qty', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('ed_item_qty', null, array('required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('unit_price')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                            <!-- {{ Form::label('item_amount', 'Total Amount ($currency)', array('class'=> 'col-lg-4 control-label')) }} -->
                            {{ Form::label('totalamount', "Total Amount ($currency)", array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    <!-- {{Form::text('item_amount', null, array('required'=>'required', 'class'=>'form-control lockinput'))}} -->
                                      <input type="text" readonly class="form-control-plaintext font-weight-bold" name="ed_item_amount" id="ed_item_amount" value="">


                                     <p class="help-block" for="inputError">{{$errors->first('unit_price')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                                <hr>
                            </div>
                            
                            <div class="form-group">
                                {{ Form::label('purchase_date', 'Date', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('ed_purchase_date', null, array('id'=>'datepicker1','placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
                                </div>
                            </div>
                            
                            <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                            {{ Form::label('receipt_no', 'Receipt No', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('ed_receipt_no', null, array('id' =>'ed_receipt_no', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('receipt_no')}}</p>
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('suppliers')) has-error @endif">
                            {{ Form::label('suppliers', 'Suppliers', array('class'=> 'col-lg-4 control-label')) }}
                            <div class="col-lg-7">
                                    <select class="form-control" id="ed_seller_name2" name="ed_seller_name2">
                                        <option value="">Select Seller Name</option>
                                        <?php foreach ($sellersOptions as $key => $value) { ?>
                                            <option value="<?php echo $key;?>"><?php echo $value;?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group @if ($errors->has('purchase_remark')) has-error @endif">
                            {{ Form::label('purchase_remark', "Remarks", array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-7">
                                     {{ Form::textarea('ed_purchase_remark', null, ['class' => 'form-control', 'id' => 'ed_purchase_remark', 'rows' => '3']) }}
                                    <p class="help-block" for="inputError">{{$errors->first('purchase_remark')}}</p>
                                </div>
                                
                            </div>
                             
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-10">
                             <input type="hidden" id="ed_trans_id" name="ed_trans_id" value="{{$trans->id}}">
                              <input type="hidden" id="ed_item_hidden_id" name="ed_item_hidden_id" value="">
                              <input type="hidden" id="ed_product_id" name="ed_product_id" value="">
                                <button type="submit" id="editpurchase"  class="btn btn-primary pull-left">Update</button>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
</div>


<div class="modal fade" id="addProfitlossModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Lazada Profit & Loss Information</h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(array('class' => 'form-horizontal')) }}
                          <!-- {{Form::input('hidden', 'item_hidden_id', '')}}  -->
                          <div class="form-group required">
                                {{ Form::label('lazada_commission_lbl', 'Commission', array('class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('lazada_commission', null, array('id'=>'lazada_commission', 'required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('lazada_commission')}}</p>
                                </div>
                            </div>
                            <div class="form-group required @if ($errors->has('receipt_no')) has-error @endif">
                            {{ Form::label('payment_fee', 'Payment Fee', array('class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('lazada_payment_fee', null, array('id'=>'lazada_payment_fee', 'required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('lazada_payment_fee')}}</p>
                                </div>
                            </div>
                           <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                            {{ Form::label('campaign_fee', 'Campaign fee', array('class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('lazada_campaign_fee', null, array('id'=>'lazada_campaign_fee', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('lazada_campaign_fee')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                            {{ Form::label('LazCoins Discount', 'LazCoins Discount', array('id'=>'lazada_payment_fee', 'class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('lazada_lazcoins_discount', null, array('id'=>'lazada_lazcoins_discount', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('lazada_lazcoins_discount')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                            <!-- {{ Form::label('item_amount', 'Total Amount ($currency)', array('class'=> 'col-lg-4 control-label')) }} -->
                            {{ Form::label('shipping_fee_voucher_by_lazada', "Shipping Fee Voucher (by Lazada)", array('class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('lazada_shipping_fee_voucher_by_lazada', null, array('id'=>'lazada_shipping_fee_voucher_by_lazada', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('lazada_shipping_fee_voucher_by_lazada')}}</p>
                                </div>
                            </div>
                           
                            
                            <div class="form-group">
                                {{ Form::label('platform_shipping_fee_subsidy_tax', 'Platform Shipping Fee Subsidy Tax', array('class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('lazada_platform_shipping_fee_subsidy_tax', null, array('id'=>'lazada_platform_shipping_fee_subsidy_tax', 'class'=>'form-control'))}}
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                            {{ Form::label('shipping_fee_paid_by_customer', 'Shipping Fee (Paid By Customer)', array('class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('lazada_shipping_fee_paid_by_customer', null, array('id'=>'lazada_shipping_fee_paid_by_customer', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('unit_price')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('totalamount')) has-error @endif">
                            {{ Form::label('lazada_lazcoins_discount_promotion_fee', "LazCoins Discount Promotion Fee", array( 'class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('lazcoins_discount_promotion_fee', null, array('id'=>'lazcoins_discount_promotion_fee','class'=>'form-control lockinput'))}}
                                    <p class="help-block" for="inputError">{{$errors->first('totalamount')}}</p>
                                </div>
                                
                            </div>
                            <div class="form-group required @if ($errors->has('lazada_item_price_credit')) has-error @endif">
                            {{ Form::label('item_price_credit', "Item Price Credit", array('class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('lazada_item_price_credit', null, array('id'=>'lazada_item_price_credit', 'required'=>'required','class'=>'form-control lockinput'))}}
                                    <p class="help-block" for="inputError">{{$errors->first('totalamount')}}</p>
                                </div>
                                
                            </div>
                                
                        <div class="form-group">
                            <div class="col-sm-offset-6 col-sm-10">
                             <input type="hidden" id="lazada_trans_id" name="lazada_trans_id" value="{{$trans->id}}">
                              <input type="hidden" id="lazada_item_hidden_id" name="lazada_item_hidden_id" value="">
                                <button type="submit" id="savepl"  class="btn btn-primary pull-left">Save</button>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
</div>

<div class="modal fade" id="editProfitlossModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Lazada Profit & Loss Information</h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(array('class' => 'form-horizontal')) }}
                          <!-- {{Form::input('hidden', 'item_hidden_id', '')}}  -->
                          <div class="form-group required">
                                {{ Form::label('lazada_commission_lbl', 'Commission', array('class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('ed_lazada_commission', null, array('id'=>'ed_lazada_commission', 'required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('lazada_commission')}}</p>
                                </div>
                            </div>
                            <div class="form-group required @if ($errors->has('receipt_no')) has-error @endif">
                            {{ Form::label('payment_fee', 'Payment Fee', array('class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('ed_lazada_payment_fee', null, array('id'=>'ed_lazada_payment_fee', 'required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('lazada_payment_fee')}}</p>
                                </div>
                            </div>
                           <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                            {{ Form::label('campaign_fee', 'Campaign fee', array('class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('ed_lazada_campaign_fee', null, array('id'=>'ed_lazada_campaign_fee', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('lazada_campaign_fee')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                            {{ Form::label('LazCoins Discount', 'LazCoins Discount', array('id'=>'lazada_payment_fee', 'class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('ed_lazada_lazcoins_discount', null, array('id'=>'ed_lazada_lazcoins_discount', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('lazada_lazcoins_discount')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                            <!-- {{ Form::label('item_amount', 'Total Amount ($currency)', array('class'=> 'col-lg-4 control-label')) }} -->
                            {{ Form::label('shipping_fee_voucher_by_lazada', "Shipping Fee Voucher (by Lazada)", array('class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('ed_lazada_shipping_fee_voucher_by_lazada', null, array('id'=>'ed_lazada_shipping_fee_voucher_by_lazada', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('lazada_shipping_fee_voucher_by_lazada')}}</p>
                                </div>
                            </div>
                           
                            
                            <div class="form-group">
                                {{ Form::label('platform_shipping_fee_subsidy_tax', 'Platform Shipping Fee Subsidy Tax', array('class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('ed_lazada_platform_shipping_fee_subsidy_tax', null, array('id'=>'ed_lazada_platform_shipping_fee_subsidy_tax', 'class'=>'form-control'))}}
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('receipt_no')) has-error @endif">
                            {{ Form::label('shipping_fee_paid_by_customer', 'Shipping Fee (Paid By Customer)', array('class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('ed_lazada_shipping_fee_paid_by_customer', null, array('id'=>'ed_lazada_shipping_fee_paid_by_customer', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('unit_price')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('totalamount')) has-error @endif">
                            {{ Form::label('lazada_lazcoins_discount_promotion_fee', "LazCoins Discount Promotion Fee", array('class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('ed_lazcoins_discount_promotion_fee', null, array('id'=>'ed_lazcoins_discount_promotion_fee','class'=>'form-control lockinput'))}}
                                    <p class="help-block" for="inputError">{{$errors->first('totalamount')}}</p>
                                </div>
                                
                            </div>
                            <div class="form-group required @if ($errors->has('lazada_item_price_credit')) has-error @endif">
                            {{ Form::label('item_price_credit', "Item Price Credit", array('class'=> 'col-lg-6 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('ed_lazada_item_price_credit', null, array('id'=>'ed_lazada_item_price_credit', 'required'=>'required','class'=>'form-control lockinput'))}}
                                    <p class="help-block" for="inputError">{{$errors->first('totalamount')}}</p>
                                </div>
                                
                            </div>
                                
                        <div class="form-group">
                            <div class="col-sm-offset-6 col-sm-10">
                             <input type="hidden" id="ed_lazada_trans_id" name="ed_lazada_trans_id" value="{{$trans->id}}">
                              <input type="hidden" id="ed_lazada_item_hidden_id" name="ed_lazada_item_hidden_id" value="">
                                <button type="submit" id="editprofit"  class="btn btn-primary pull-left">Update</button>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
</div>

<style>
    .form-section {
        margin-bottom: 30px;
    }

    .form-section-title {
        font-weight: 600;
        font-size: 1.55rem;
        margin-top: 25px;
        margin-bottom: 15px;
        border-bottom: 2px solid #007bff; /* Changed to Bootstrap primary color for nicer look */
        padding-bottom: 8px;
        color: #007bff;
        letter-spacing: 0.03em;
    }
    .modal-title {
        color: #007bff;
    }
</style>

<div class="modal fade" id="addShopeeprofitlossModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Add Shopee Profit & Loss Information</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                {{ Form::open(['class' => 'form']) }}

                <!-- ====================== Shipping Information ====================== -->
                <div class="form-section">
                    <div class="form-section-title">Shipping Information</div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {{ Form::label('shopee_shippingfee_paid_by_buyer', 'Shipping Fee Paid by Buyer (excl. SST)') }}
                            {{ Form::number('shopee_shippingfee_paid_by_buyer', null, ['class' => 'form-control', 'step' => '0.01']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('shopee_shippingfee_charged_by_logistic_provider', 'Shipping Fee Charged by Logistic Provider') }}
                            {{ Form::number('shopee_shippingfee_charged_by_logistic_provider', null, ['class' => 'form-control', 'step' => '0.01']) }}
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {{ Form::label('shopee_seller_paid_shippingfee', 'Seller Paid Shipping Fee SST') }}
                            {{ Form::number('shopee_seller_paid_shippingfee', null, ['class' => 'form-control', 'step' => '0.01']) }}
                        </div>
                        <div class="form-group col-md-6 py-2">&nbsp;</div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6 py-2">&nbsp;</div>
                        <div class="form-group col-md-6 py-2">&nbsp;</div>
                    </div>
                </div>

                <!-- ====================== Vouchers & Rebates ====================== -->
                <div class="form-section">
                    <div class="form-section-title">Vouchers & Rebates</div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {{ Form::label('shopee_product_discount_rebate', 'Product Discount Rebate from Shopee') }}
                            {{ Form::number('shopee_product_discount_rebate', null, ['class' => 'form-control', 'step' => '0.01']) }}
                        </div>
                        <div class="form-group col-md-6 py-2">&nbsp;</div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6 py-2">&nbsp;</div>
                        <div class="form-group col-md-6 py-2">&nbsp;</div>
                    </div>
                </div>

                <!-- ====================== Fees & Charges ====================== -->
                <div class="form-section">
                    <div class="form-section-title">Fees & Charges</div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {{ Form::label('shopee_commission', 'Commission Fee (Incl. SST)') }} <span class="text-danger">*</span>
                            {{ Form::number('shopee_commission', null, ['class' => 'form-control', 'step' => '0.01', 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('shopee_service_fee', 'Service Fee') }} <span class="text-danger">*</span>
                            {{ Form::number('shopee_service_fee', null, ['class' => 'form-control', 'step' => '0.01', 'required']) }}
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {{ Form::label('shopee_transaction_fee', 'Transaction Fee (Incl. SST)') }} <span class="text-danger">*</span>
                            {{ Form::number('shopee_transaction_fee', null, ['class' => 'form-control', 'step' => '0.01', 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('shopee_saver_programme_fee', 'Saver Programme Fee (Incl. SST)') }}
                            {{ Form::number('shopee_saver_programme_fee', null, ['class' => 'form-control', 'step' => '0.01']) }}
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {{ Form::label('shopee_ams_commission_fee', 'AMS Commission Fee') }}
                            {{ Form::number('shopee_ams_commission_fee', null, ['class' => 'form-control', 'step' => '0.01']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('shopee_other_fee', 'Other Fees') }}
                            {{ Form::number('shopee_other_fee', null, ['class' => 'form-control', 'step' => '0.01']) }}
                        </div>
                    </div>
                </div>

                <!-- ====================== Product Price Credit ====================== -->
                <div class="form-section">
                    <div class="form-section-title">Product Price Credit</div>
                    <div class="form-group col-md-6">
                        {{ Form::label('shopee_item_price_credit', 'Product Price Credit') }} <span class="text-danger">*</span>
                        {{ Form::number('shopee_item_price_credit', null, ['class' => 'form-control lockinput', 'step' => '0.01', 'required']) }}
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6 py-2">&nbsp;</div>
                        <div class="form-group col-md-6 py-2">&nbsp;</div>
                    </div>
                </div>

                <!-- ====================== Hidden Fields & Submit ====================== -->
                <div class="text-right">
                    {{ Form::hidden('shopee_trans_id', $trans->id, ['id' => 'shopee_trans_id', 'class' => 'form-control', 'step' => '0.01']) }}
                    {{ Form::hidden('shopee_item_hidden_id', '', ['id' => 'shopee_item_hidden_id', 'class' => 'form-control', 'step' => '0.01']) }}
                    <button type="submit" id="saveshopeepl" class="btn btn-primary">Save</button>
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editShopeeprofitlossModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Edit Shopee Profit & Loss Information</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                {{ Form::open(['class' => 'form']) }}

                <!-- ====================== Shipping Information ====================== -->
                <div class="form-section">
                    <div class="form-section-title">Shipping Information</div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {{ Form::label('ed_shopee_shippingfee_paid_by_buyer', 'Shipping Fee Paid by Buyer (excl. SST)') }}
                            {{ Form::number('ed_shopee_shippingfee_paid_by_buyer', null, ['class' => 'form-control', 'step' => '0.01', 'id' => 'ed_shopee_shippingfee_paid_by_buyer']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('ed_shopee_shippingfee_charged_by_logistic_provider', 'Shipping Fee Charged by Logistic Provider') }}
                            {{ Form::number('ed_shopee_shippingfee_charged_by_logistic_provider', null, ['class' => 'form-control', 'step' => '0.01', 'id' => 'ed_shopee_shippingfee_charged_by_logistic_provider']) }}
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {{ Form::label('ed_shopee_seller_paid_shippingfee', 'Seller Paid Shipping Fee SST') }}
                            {{ Form::number('ed_shopee_seller_paid_shippingfee', null, ['class' => 'form-control', 'step' => '0.01', 'id' => 'ed_shopee_seller_paid_shippingfee']) }}
                        </div>
                        <div class="form-group col-md-6 py-2">&nbsp;</div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6 py-2">&nbsp;</div>
                        <div class="form-group col-md-6 py-2">&nbsp;</div>
                    </div>
                </div>

                <!-- ====================== Vouchers & Rebates ====================== -->
                <div class="form-section">
                    <div class="form-section-title">Vouchers & Rebates</div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {{ Form::label('ed_shopee_product_discount_rebate', 'Product Discount Rebate from Shopee') }}
                            {{ Form::number('ed_shopee_product_discount_rebate', null, ['class' => 'form-control', 'step' => '0.01', 'id' => 'ed_shopee_product_discount_rebate']) }}
                        </div>
                        <div class="form-group col-md-6 py-2">&nbsp;</div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6 py-2">&nbsp;</div>
                        <div class="form-group col-md-6 py-2">&nbsp;</div>
                    </div>
                </div>

                <!-- ====================== Fees & Charges ====================== -->
                <div class="form-section">
                    <div class="form-section-title">Fees & Charges</div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {{ Form::label('ed_shopee_commission', 'Commission Fee (Incl. SST)') }} <span class="text-danger">*</span>
                            {{ Form::number('ed_shopee_commission', null, ['class' => 'form-control', 'step' => '0.01', 'required', 'id' => 'ed_shopee_commission']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('ed_shopee_service_fee', 'Service Fee') }} <span class="text-danger">*</span>
                            {{ Form::number('ed_shopee_service_fee', null, ['class' => 'form-control', 'step' => '0.01', 'required', 'id' => 'ed_shopee_service_fee']) }}
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {{ Form::label('ed_shopee_transaction_fee', 'Transaction Fee (Incl. SST)') }} <span class="text-danger">*</span>
                            {{ Form::number('ed_shopee_transaction_fee', null, ['class' => 'form-control', 'step' => '0.01', 'required', 'id' => 'ed_shopee_transaction_fee']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('ed_shopee_saver_programme_fee', 'Saver Programme Fee (Incl. SST)') }}
                            {{ Form::number('ed_shopee_saver_programme_fee', null, ['class' => 'form-control', 'step' => '0.01', 'id' => 'ed_shopee_saver_programme_fee']) }}
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {{ Form::label('ed_shopee_ams_commission_fee', 'AMS Commission Fee') }}
                            {{ Form::number('ed_shopee_ams_commission_fee', null, ['class' => 'form-control', 'step' => '0.01', 'id' => 'ed_shopee_ams_commission_fee']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('ed_shopee_other_fee', 'Other Fees') }}
                            {{ Form::number('ed_shopee_other_fee', null, ['class' => 'form-control', 'step' => '0.01', 'id' => 'ed_shopee_other_fee']) }}
                        </div>
                    </div>
                </div>

                <!-- ====================== Product Price Credit ====================== -->
                <div class="form-section">
                    <div class="form-section-title">Product Price Credit</div>
                    <div class="form-group col-md-6">
                        {{ Form::label('ed_shopee_item_price_credit', 'Product Price Credit') }} <span class="text-danger">*</span>
                        {{ Form::number('ed_shopee_item_price_credit', null, ['class' => 'form-control lockinput', 'step' => '0.01', 'required', 'id' => 'ed_shopee_item_price_credit']) }}
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6 py-2">&nbsp;</div>
                        <div class="form-group col-md-6 py-2">&nbsp;</div>
                    </div>
                </div>

                <!-- ====================== Hidden Fields & Submit ====================== -->
                <div class="text-right">
                    {{ Form::hidden('ed_shopee_trans_id', $trans->id, ['id' => 'ed_shopee_trans_id']) }}
                    {{ Form::hidden('ed_shopee_item_hidden_id', '', ['id' => 'ed_shopee_item_hidden_id']) }}
                    <button type="submit" id="editshopeepl" class="btn btn-primary">Update</button>
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="addTiktokprofitlossModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add TikTok Profit & Loss Information</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        {{ Form::open(['class' => 'form']) }}

        <!-- Total Revenue Section -->
        <h5 class="form-section-title">Total Revenue</h5>
        <div class="form-row">
          <div class="form-group col-md-6">
            {{ Form::label('tiktok_subtotal_after_discounts', 'Subtotal after Seller Discounts') }} <span class="text-danger">**</span>
            {{ Form::number('tiktok_subtotal_after_discounts', null, ['class' => 'form-control', 'step' => '0.01', 'required']) }}
          </div>
          <div class="form-group col-md-6">
            {{ Form::label('tiktok_subtotal_before_discounts', 'Subtotal before Discounts') }} <span class="text-danger">**</span>
            {{ Form::number('tiktok_subtotal_before_discounts', null, ['class' => 'form-control', 'step' => '0.01', 'required']) }}
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-12">
            {{ Form::label('tiktok_seller_discounts', 'Seller Discounts') }}
            {{ Form::number('tiktok_seller_discounts', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
        </div>

        <!-- Total Fees Section -->
        <h5 class="form-section-title">Total Fees</h5>
        <div class="form-row">
          <div class="form-group col-md-6">
            {{ Form::label('tiktok_transaction_fee', 'Transaction Fee') }} <span class="text-danger">**</span>
            {{ Form::number('tiktok_transaction_fee', null, ['class' => 'form-control', 'step' => '0.01', 'required']) }}
          </div>
          <div class="form-group col-md-6">
            {{ Form::label('tiktok_commission_fee', 'TikTok Shop Commission Fee') }}
            {{ Form::number('tiktok_commission_fee', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            {{ Form::label('tiktok_actual_shipping_fee', 'Actual Shipping Fee') }}
            {{ Form::number('tiktok_actual_shipping_fee', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
          <div class="form-group col-md-6">
            {{ Form::label('tiktok_platform_shipping_fee_discount', 'Platform Shipping Fee Discount') }}
            {{ Form::number('tiktok_platform_shipping_fee_discount', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            {{ Form::label('tiktok_customer_shipping_fee_before_discounts', 'Customer Shipping Fee before Discounts') }}
            {{ Form::number('tiktok_customer_shipping_fee_before_discounts', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
          <div class="form-group col-md-6">
            {{ Form::label('tiktok_seller_shipping_fee_discount', 'Seller Shipping Fee Discount') }}
            {{ Form::number('tiktok_seller_shipping_fee_discount', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            {{ Form::label('tiktok_tiktokshop_shipping_fee_discount', 'TikTok Shop Shipping Fee Discount') }}
            {{ Form::number('tiktok_tiktokshop_shipping_fee_discount', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
          <div class="form-group col-md-6">
            {{ Form::label('tiktok_actual_return_shipping_fee', 'Actual Return Shipping Fee') }}
            {{ Form::number('tiktok_actual_return_shipping_fee', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            {{ Form::label('tiktok_refunded_customer_shipping_fee', 'Refunded Customer Shipping Fee') }}
            {{ Form::number('tiktok_refunded_customer_shipping_fee', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
          <div class="form-group col-md-6">
            {{ Form::label('tiktok_shipping_subsidy', 'Shipping Subsidy') }}
            {{ Form::number('tiktok_shipping_subsidy', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            {{ Form::label('tiktok_affiliate_commission', 'Affiliate Commission') }}
            {{ Form::number('tiktok_affiliate_commission', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
          <div class="form-group col-md-6">
            {{ Form::label('tiktok_bonus_cashback_service_fee', 'Bonus Cashback service fee') }}
            {{ Form::number('tiktok_bonus_cashback_service_fee', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            {{ Form::label('tiktok_voucher_xtra_service_fee', 'Voucher Xtra service fee') }}
            {{ Form::number('tiktok_voucher_xtra_service_fee', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
          <div class="form-group col-md-6">
            {{ Form::label('tiktok_other_fees', 'Other Fees') }}
            {{ Form::number('tiktok_other_fees', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
        </div>

        <!-- Total Settlement Amount Section -->
        <h5 class="form-section-title">Total Settlement Amount</h5>
        <div class="form-row">
          <div class="form-group col-md-12">
            {{ Form::label('tiktok_total_settlement_amount', 'Total Settlement Amount') }}
            {{ Form::number('tiktok_total_settlement_amount', null, ['class' => 'form-control', 'step' => '0.01', 'required']) }}
          </div>
        </div>

        <!-- Submit -->
        <div class="text-right mt-3">
          {{ Form::hidden('tiktok_trans_id', $trans->id, ['id' => 'tiktok_trans_id']) }}
          {{ Form::hidden('tiktok_item_hidden_id', '', ['id' => 'tiktok_item_hidden_id']) }}
          <button type="submit" id="savetiktokpl" class="btn btn-primary">Save</button>
        </div>

        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editTiktokprofitlossModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit TikTok Profit & Loss Information</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        {{ Form::open(['class' => 'form']) }}

        <!-- Total Revenue Section -->
        <h5 class="form-section-title">Total Revenue</h5>
        <div class="form-row">
          <div class="form-group col-md-6">
            {{ Form::label('ed_tiktok_subtotal_after_discounts', 'Subtotal after Seller Discounts') }} <span class="text-danger">**</span>
            {{ Form::number('ed_tiktok_subtotal_after_discounts', null, ['class' => 'form-control', 'step' => '0.01', 'required']) }}
          </div>
          <div class="form-group col-md-6">
            {{ Form::label('ed_tiktok_subtotal_before_discounts', 'Subtotal before Discounts') }} <span class="text-danger">**</span>
            {{ Form::number('ed_tiktok_subtotal_before_discounts', null, ['class' => 'form-control', 'step' => '0.01', 'required']) }}
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-12">
            {{ Form::label('ed_tiktok_seller_discounts', 'Seller Discounts') }}
            {{ Form::number('ed_tiktok_seller_discounts', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
        </div>

        <!-- Total Fees Section -->
        <h5 class="form-section-title">Total Fees</h5>
        <div class="form-row">
          <div class="form-group col-md-6">
            {{ Form::label('ed_tiktok_transaction_fee', 'Transaction Fee') }} <span class="text-danger">**</span>
            {{ Form::number('ed_tiktok_transaction_fee', null, ['class' => 'form-control', 'step' => '0.01', 'required']) }}
          </div>
          <div class="form-group col-md-6">
            {{ Form::label('ed_tiktok_commission_fee', 'TikTok Shop Commission Fee') }}
            {{ Form::number('ed_tiktok_commission_fee', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            {{ Form::label('ed_tiktok_actual_shipping_fee', 'Actual Shipping Fee') }}
            {{ Form::number('ed_tiktok_actual_shipping_fee', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
          <div class="form-group col-md-6">
            {{ Form::label('ed_tiktok_platform_shipping_fee_discount', 'Platform Shipping Fee Discount') }}
            {{ Form::number('ed_tiktok_platform_shipping_fee_discount', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            {{ Form::label('ed_tiktok_customer_shipping_fee_before_discounts', 'Customer Shipping Fee before Discounts') }}
            {{ Form::number('ed_tiktok_customer_shipping_fee_before_discounts', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
          <div class="form-group col-md-6">
            {{ Form::label('ed_tiktok_seller_shipping_fee_discount', 'Seller Shipping Fee Discount') }}
            {{ Form::number('ed_tiktok_seller_shipping_fee_discount', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            {{ Form::label('ed_tiktok_tiktokshop_shipping_fee_discount', 'TikTok Shop Shipping Fee Discount') }}
            {{ Form::number('ed_tiktok_tiktokshop_shipping_fee_discount', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
          <div class="form-group col-md-6">
            {{ Form::label('ed_tiktok_actual_return_shipping_fee', 'Actual Return Shipping Fee') }}
            {{ Form::number('ed_tiktok_actual_return_shipping_fee', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            {{ Form::label('ed_tiktok_refunded_customer_shipping_fee', 'Refunded Customer Shipping Fee') }}
            {{ Form::number('ed_tiktok_refunded_customer_shipping_fee', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
          <div class="form-group col-md-6">
            {{ Form::label('ed_tiktok_shipping_subsidy', 'Shipping Subsidy') }}
            {{ Form::number('ed_tiktok_shipping_subsidy', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            {{ Form::label('ed_tiktok_affiliate_commission', 'Affiliate Commission') }}
            {{ Form::number('ed_tiktok_affiliate_commission', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
          <div class="form-group col-md-6">
            {{ Form::label('ed_tiktok_bonus_cashback_service_fee', 'Bonus Cashback service fee') }}
            {{ Form::number('ed_tiktok_bonus_cashback_service_fee', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            {{ Form::label('ed_tiktok_voucher_xtra_service_fee', 'Voucher Xtra service fee') }}
            {{ Form::number('ed_tiktok_voucher_xtra_service_fee', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
          <div class="form-group col-md-6">
            {{ Form::label('ed_tiktok_other_fees', 'Other Fees') }}
            {{ Form::number('ed_tiktok_other_fees', null, ['class' => 'form-control', 'step' => '0.01']) }}
          </div>
        </div>

        <!-- Total Settlement Amount Section -->
        <h5 class="form-section-title">Total Settlement Amount</h5>
        <div class="form-row">
          <div class="form-group col-md-12">
            {{ Form::label('ed_tiktok_total_settlement_amount', 'Total Settlement Amount') }}
            {{ Form::number('ed_tiktok_total_settlement_amount', null, ['class' => 'form-control', 'step' => '0.01', 'required']) }}
          </div>
        </div>

        <!-- Submit -->
        <div class="text-right mt-3">
          {{ Form::hidden('ed_tiktok_trans_id', $trans->id, ['id' => 'ed_tiktok_trans_id']) }}
          {{ Form::hidden('ed_tiktok_item_hidden_id', '', ['id' => 'ed_tiktok_item_hidden_id']) }}
          <button type="submit" id="edittiktokpl" class="btn btn-primary">Update</button>
        </div>

        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
function confirmSubmit() {
    var form = document.getElementById('main-form');
    var select = document.getElementById('status');
    var status = select.options[select.selectedIndex].value;
    if (status == 'cancelled') {
        if (confirm('Cancelling this transaction will cancell all associated logistic transaction and batch. Proceed?')) {
            form.submit();
        }
    } else {
        form.submit();
    }
}

function delete_details(details_id) {
    if(confirm("Are you sure to delete this transaction")) {


        var tempid = document.getElementById("remove_details_id");
        tempid.value = details_id;

        // var nameValue = document.getElementById("remove_transaction_id").value;
        // alert(nameValue);

        var tempform = document.getElementById("remove_frm");
        tempform.submit();

    }

}

function confirmGen() {

    if (confirm("Are you sure to regenerate document?")) {
        return;
    }
    else
    {
        var tempgen = document.getElementById("gen_doc");
        tempgen.href = "";
    }

}

function confirmGenCustomer() {

    if (confirm("Are you sure to generate customer invoice ?")) {
        return;
    }
    else
    {
        var tempgen = document.getElementById("gen_cus_invoice");
        tempgen.href = "";
    }

}

function confirmMail() {

    if (confirm("Are you sure to resend email?")) {
        return;
    }
    else
    {
        var tempgen = document.getElementById("sendmail");
        tempgen.href = "";
    }

}

function confirmLogistic() {

    if (confirm("Are you sure to log this transaction to Logistic App?")) {
        return;
    }
    else
    {
        var tempgen = document.getElementById("loglogistic");
        tempgen.href = "";
    }

}
</script>

<script type="text/javascript">
<?php if($g_po_inv === false) {?>
document.getElementById("gen_po").innerHTML = "";
<?php }?>

</script>
@stop

@section('script')
    $('#status').change(function () {
        if ($(this).val() == 'refund') {
            $.ajax({
                url: '{{ url('points/customers/refund-check?id='.$trans->id) }}',
                success: function (negative) {
                    if (negative) {
                        $('#warning').modal();
                    }
                }
            });
        }
    });



    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var remarkId = button.data('id');
        var remark = button.data('remark');

        var modal = $(this);
        modal.find('#addRemarkId').val(remarkId);
        modal.find('#remark').val(remark);
    });
    
    $('body').on('click', '.addPurchase', function(){ 
        $("label[for='item_name']").text($(this).attr('data-name'));
        $("input[name='item_hidden_id']").val($(this).attr('data-id'));
        $("#addPurchaseModal").modal('show');
    });
    
    $('body').on('click', '.addPL', function(){ 
        <!-- alert($(this).attr('data-id')); --> 
        $("label[for='item_name']").text($(this).attr('data-name'));
        $("input[name='item_hidden_id']").val($(this).attr('data-id'));
        $("#addProfitlossModal").modal('show');
    });
    
    $('body').on('click', '.editPH', function(){ 
        <!-- alert($(this).attr('data-id')); -->
        var purchaseID = $(this).attr('data-id');
        $("#editPurchaseModal").modal('show');

        $.ajax({
                type: 'GET',
                url: '/transaction/getpurchase/'+purchaseID,
                data: {},
                dataType: "json",
                success: function(resultData) { 
                console.log(resultData);
                    <!-- alert(resultData.remarks); -->
                    $("label[for='ed_item_name']").text(resultData.product_name);
                    $("input[name='ed_item_price']").val(resultData.price);
                    $("input[name='ed_item_qty']").val(resultData.product_qty);
                    $("input[name='ed_item_amount']").val(resultData.price * resultData.product_qty);
                    $("input[name='ed_purchase_date']").val(resultData.date_of_purchase);
                    $("input[name='ed_receipt_no']").val(resultData.ref_no);
                    $("#ed_seller_name2").val(resultData.seller_id);
                    $("input[id='ed_purchase_remark']").val(resultData.remarks);
                    $("input[name='ed_trans_id']").val(resultData.transaction_id);
                    $("input[name='ed_item_hidden_id']").val(resultData.id);
                    $("input[name='ed_product_id']").val(resultData.product_id);
                    $('#ed_purchase_remark').val(resultData.remarks); 
                    
                    <!-- alert($("input[name='ed_trans_id']").val()); -->
                    <!-- alert($("input[name='ed_item_hidden_id']").val()); -->
                }
            });

        

    });

    $('body').on('click', '.deletePH', function(){ 
        var pur_id = $(this).attr('data-id');
        if (pur_id > 0) { 
                if (confirm('Are you sure you want to delete this item?')) {
                  $.ajax({
                    method: "POST",
                    url: "/transaction/deletepurchase",
                    dataType:'json',
                    data: {
                        'id': pur_id,
                    },
                    beforeSend: function(){
                        
                    },
                    error: function(data) {
                      console.log(data);
                    },
                    success: function(data) {
                      <!-- alert('Item has deleted successfully.'); -->
                      location.reload();
                    },
                  });
                }

         }
    });
    
    $('body').on('click', '.deletePL', function(){ 
        var pl_id = $(this).attr('data-id');
        if (pl_id > 0) { 
                if (confirm('Are you sure you want to delete this item?')) {
                  $.ajax({
                    method: "POST",
                    url: "/transaction/deleteprofitloss",
                    dataType:'json',
                    data: {
                        'id': pl_id,
                    },
                    beforeSend: function(){
                        
                    },
                    error: function(data) {
                      console.log(data);
                    },
                    success: function(data) {
                      alert('Item has deleted successfully.');
                      location.reload();
                    },
                  });
                }

         }
    });
    
    $('body').on('click', '.addshopeePL', function(){ 
        <!-- alert($(this).attr('data-id')); --> 
        $("label[for='item_name']").text($(this).attr('data-name'));
        $("input[name='item_hidden_id']").val($(this).attr('data-id'));
        $("#addShopeeprofitlossModal").modal('show');
    });
    

    $('body').on('click', '.editShopeePL', function(){ 
        <!-- alert($(this).attr('data-id')); -->
        var profitID = $(this).attr('data-id');
        $("#editShopeeprofitlossModal").modal('show');
        <!-- alert(profitID); -->
        $.ajax({
                type: 'GET',
                url: '/transaction/getshopeeprofitloss/'+profitID,
                data: {},
                dataType: "json",
                success: function(resultData) { 
                console.log(resultData);
                    <!-- alert(resultData.remarks); -->
                    $("input[name='ed_shopee_shippingfee_paid_by_buyer']").val(resultData.shippingfee_paid_by_buyer);
                    $("input[name='ed_shopee_shippingfee_charged_by_logistic_provider']").val(resultData.shippingfee_charged_by_logistic_provider);
                    $("input[name='ed_shopee_seller_paid_shippingfee']").val(resultData.seller_paid_shippingfee);
                    $("input[name='ed_shopee_product_discount_rebate']").val(resultData.product_discount_rebate);
                    $("input[name='ed_shopee_commission']").val(resultData.commission);                   
                    $("input[name='ed_shopee_service_fee']").val(resultData.service_fee);
                    $("input[name='ed_shopee_transaction_fee']").val(resultData.transaction_fee);
                    $("input[name='ed_shopee_saver_programme_fee']").val(resultData.saver_programme_fee);
                    $("input[name='ed_shopee_ams_commission_fee']").val(resultData.ams_commission_fee);
                    $("input[name='ed_shopee_other_fee']").val(resultData.other_fee);
                    $("input[name='ed_shopee_item_price_credit']").val(resultData.item_price_credit);
                    $("input[name='ed_shopee_item_hidden_id']").val(resultData.id);
                    <!-- alert($("input[name='ed_trans_id']").val()); -->
                    <!-- alert($("input[name='ed_item_hidden_id']").val()); -->
                }
            });

        

    });

    $('body').on('click', '.deleteShopeePL', function(){ 
        var pl_id = $(this).attr('data-id');
        if (pl_id > 0) { 
                if (confirm('Are you sure you want to delete this item?')) {
                  $.ajax({
                    method: "POST",
                    url: "/transaction/deleteshopeeprofitloss",
                    dataType:'json',
                    data: {
                        'id': pl_id,
                    },
                    beforeSend: function(){
                        
                    },
                    error: function(data) {
                      console.log(data);
                    },
                    success: function(data) {
                      alert('Item has deleted successfully.');
                      location.reload();
                    },
                  });
                }

         }
    });
    
    
    $('body').on('click', '.addTiktokPL', function(){ 
        <!-- alert($(this).attr('data-id')); --> 
        $("label[for='item_name']").text($(this).attr('data-name'));
        $("input[name='item_hidden_id']").val($(this).attr('data-id'));
        $("#addTiktokprofitlossModal").modal('show');
    });
    
    $('body').on('click', '.editTiktokPL', function(){ 
        <!-- alert($(this).attr('data-id')); -->
        var profitID = $(this).attr('data-id');
        $("#editTiktokprofitlossModal").modal('show');
        <!-- alert(profitID); -->
        $.ajax({
                type: 'GET',
                url: '/transaction/gettiktokprofitloss/'+profitID,
                data: {},
                dataType: "json",
                success: function(resultData) { 
                console.log(resultData);
                    <!-- alert(resultData.remarks); -->
                    $("input[name='ed_tiktok_subtotal_after_discounts']").val(resultData.subtotal_after_discounts);
                    $("input[name='ed_tiktok_subtotal_before_discounts']").val(resultData.subtotal_before_discounts);
                    $("input[name='ed_tiktok_seller_discounts']").val(resultData.seller_discounts);
                    $("input[name='ed_tiktok_transaction_fee']").val(resultData.transaction_fee);
                    $("input[name='ed_tiktok_commission_fee']").val(resultData.commission_fee);                   
                    $("input[name='ed_tiktok_actual_shipping_fee']").val(resultData.actual_shipping_fee);
                    $("input[name='ed_tiktok_platform_shipping_fee_discount']").val(resultData.platform_shipping_fee_discount);
                    $("input[name='ed_tiktok_customer_shipping_fee_before_discounts']").val(resultData.customer_shipping_fee_before_discounts);
                    $("input[name='ed_tiktok_seller_shipping_fee_discount']").val(resultData.seller_shipping_fee_discount);
                    $("input[name='ed_tiktok_tiktokshop_shipping_fee_discount']").val(resultData.tiktokshop_shipping_fee_discount);
                    $("input[name='ed_tiktok_actual_return_shipping_fee']").val(resultData.actual_return_shipping_fee);
                    $("input[name='ed_tiktok_refunded_customer_shipping_fee']").val(resultData.refunded_customer_shipping_fee);
                    $("input[name='ed_tiktok_shipping_subsidy']").val(resultData.shipping_subsidy);
                    $("input[name='ed_tiktok_affiliate_commission']").val(resultData.affiliate_commission);
                    $("input[name='ed_tiktok_bonus_cashback_service_fee']").val(resultData.bonus_cashback_service_fee);
                    $("input[name='ed_tiktok_voucher_xtra_service_fee']").val(resultData.voucher_xtra_service_fee);
                    $("input[name='ed_tiktok_other_fees']").val(resultData.other_fees);
                    $("input[name='ed_tiktok_total_settlement_amount']").val(resultData.total_settlement_amount);
                    $("input[name='ed_tiktok_item_hidden_id']").val(resultData.id);
                    <!-- alert($("input[name='ed_trans_id']").val()); -->
                    <!-- alert($("input[name='ed_item_hidden_id']").val()); -->
                }
            });

        

    });

    
    $('#item_price').change(function () {
        var item_price       = $("#item_price").val(); 
        var item_qty         = $("#item_qty").val(); 
        var total_amt        = item_price * item_qty;
        $("#item_amount").val(total_amt.toFixed(2));

    });

     $('#item_qty').change(function () {
        var item_price       = $("#item_price").val(); 
        var item_qty         = $("#item_qty").val(); 
        var total_amt        = item_price * item_qty;
        $("#item_amount").val(total_amt.toFixed(2));

    });
    
    $('#ed_item_price').change(function () {
        var item_price       = $("#ed_item_price").val(); 
        var item_qty         = $("#ed_item_qty").val(); 
        var total_amt        = item_price * item_qty;
        $("#ed_item_amount").val(total_amt.toFixed(2));

    });

    $('#ed_item_qty').change(function () {
        var item_price       = $("#ed_item_price").val(); 
        var item_qty         = $("#ed_item_qty").val(); 
        var total_amt        = item_price * item_qty;
        $("#ed_item_amount").val(total_amt.toFixed(2));

    });

    $('#savepurchase').on('click', function() {

            var trans_id        = $("#trans_id").val(); 
            if( $("#datepicker").val() != ''){
            var purchase_date   = $("#datepicker").val(); 
            }
            else {
                var purchase_date = '';
            }
            
            if( $("#seller_name2").val() != ''){
            var seller_name     = $("#seller_name2").find(":selected").val();
            }
            else {
                var seller_name = '';
            }
            
            if( $("#receipt_no").val() != ''){
            var receipt_no      = $("#receipt_no").val();
            }
            else {
                var receipt_no = '';
            }

            
            
            
            var item_price      = $("#item_price").val(); 
            var item_qty      = $("#item_qty").val(); 
            var totalamount     = $("#item_amount").val();     
            var purchase_remark   = $("#purchase_remark").val();
            var trans_item_id   = $("#item_hidden_id").val(); 

            <!-- alert(trans_id+'-'+purchase_date+'-'+seller_name+'-'+receipt_no+'-'+totalamount); -->
           
            console.log(trans_id);
            console.log(purchase_date);
            console.log(seller_name);
            console.log(receipt_no);
            console.log(totalamount);
            console.log(purchase_remark);
            

        if (trans_id.length > 0 && totalamount > 0) {

          $.ajax({
            method: "POST",
            url: "/transaction/purchase",
            dataType:'json',
            data: {
                'transaction_id':trans_id,
                'purchase_date':purchase_date,
                'seller_name':seller_name,
                'receipt_no':receipt_no,
                'trans_item_id':trans_item_id,
                'item_price':item_price,
                'item_qty':item_qty,
                'totalamount':totalamount,
                'purchase_remark':purchase_remark
            },
            beforeSend: function(){
                
            },
            error: function(data) {
              console.log(data);
              return false;
            },
            success: function(data) {

                
              if(data.response == 1){
                $("#savepurchase").submit();
              }
              else
              {
                
              } 
            }
          }); 
        } else {
          if (item_price.length == 0) {
            alert('Please enter item Price');
            return false;
          } else if (item_qty.length == 0) {
            alert('Please enter item Qty');
            return false;
          } 
         
        }
        });
        
        $('#editpurchase').on('click', function() {

            var trans_id                = $("#ed_trans_id").val(); 
            var trans_purchase_item_id  = $("#ed_item_hidden_id").val();
            var item_name               = $("label[for='ed_item_name']").text(); 
            var product_id              = $("#ed_product_id").val(); 
            var item_price              = $("#ed_item_price").val(); 
            var item_qty                = $("#ed_item_qty").val(); 
            var totalamount             = $("#ed_item_amount").val(); 

            var purchase_date   = $("#datepicker1").val();  
            var seller_name     = $("#ed_seller_name2").find(":selected").val();
            var receipt_no      = $("#ed_receipt_no").val();
            var purchase_remark   = $("#ed_purchase_remark").val();

            <!-- alert(trans_id+'-'+purchase_date+'-'+seller_name+'-'+receipt_no+'-'+totalamount); -->
           
            console.log(trans_id);
            console.log(purchase_date);
            console.log(seller_name);
            console.log(receipt_no);
            console.log(totalamount);
            console.log(purchase_remark);

            console.log(trans_id);
            console.log(trans_purchase_item_id);
            console.log($("label[for='ed_item_name']").text());
            console.log(product_id);
            console.log(item_price);
            console.log(item_qty);
            console.log(totalamount);

            <!-- die('ok'); -->

        if (trans_id.length > 0 && totalamount > 0) {

          $.ajax({
            method: "POST",
            url: "/transaction/purchaseupdate",
            dataType:'json',
            data: {
                'trans_id':trans_id,
                'trans_purchase_item_id':trans_purchase_item_id,
                'purchase_date':purchase_date,
                'seller_name':seller_name,
                'receipt_no':receipt_no,
                'product_id':product_id,
                'item_name':item_name,
                'item_price':item_price,
                'item_qty':item_qty,
                'totalamount':totalamount,
                'purchase_remark':purchase_remark
            },
            beforeSend: function(){
                
            },
            error: function(data) {
              console.log(data);
              return false;
            },
            success: function(data) {
            alert('Modified Successfully!!!.');
              if(data == 1){

                $("#editpurchase").submit();
              }
              else
              {
                
              } 
            }
          }); 
        } else {
          if (item_price.length == 0) {
            alert('Please enter item Price');
            return false;
          } else if (item_qty.length == 0) {
            alert('Please enter item Qty');
            return false;
          } 
         
        }
        });
        
        
        $('#savepl').on('click', function() {
                var lazada_trans_id                         = $("#lazada_trans_id").val(); 
                var lazada_commission                       = $("#lazada_commission").val(); 
                var lazada_payment_fee                      = $("#lazada_payment_fee").val();
                var lazada_campaign_fee                     = $("#lazada_campaign_fee").val(); 
                var lazada_lazcoins_discount                = $("#lazada_lazcoins_discount").val(); 
                var lazada_shipping_fee_voucher_by_lazada   = $("#lazada_shipping_fee_voucher_by_lazada").val();     
                var lazada_platform_shipping_fee_subsidy_tax= $("#lazada_platform_shipping_fee_subsidy_tax").val();
                var lazada_shipping_fee_paid_by_customer    = $("#lazada_shipping_fee_paid_by_customer").val(); 
                var lazcoins_discount_promotion_fee         = $("#lazcoins_discount_promotion_fee").val(); 
                var lazada_item_price_credit                = $("#lazada_item_price_credit").val(); 

                console.log(lazada_trans_id);
                console.log(lazada_commission);
                console.log(lazada_payment_fee);
                console.log(lazada_lazcoins_discount);
                console.log(lazada_shipping_fee_voucher_by_lazada);
                console.log(lazcoins_discount_promotion_fee);
                

            if (lazada_trans_id > 0) {
                    
            $.ajax({
                method: "POST",
                url: "/transaction/lazadasaveprofitloss",
                dataType:'json',
                data: {
                    'transaction_id':lazada_trans_id,
                    'lazada_commission':lazada_commission,
                    'lazada_payment_fee':lazada_payment_fee,
                    'lazada_campaign_fee':lazada_campaign_fee,
                    'lazada_lazcoins_discount':lazada_lazcoins_discount,
                    'lazada_shipping_fee_voucher':lazada_shipping_fee_voucher_by_lazada,
                    'lazada_platform_shipping_tax':lazada_platform_shipping_fee_subsidy_tax,
                    'lazada_shipping_paid_by_customer':lazada_shipping_fee_paid_by_customer,
                    'lazcoins_discount_promotion_fee':lazcoins_discount_promotion_fee,
                    'lazada_item_price_credit':lazada_item_price_credit
                },
                beforeSend: function(){
                    
                },
                error: function(data) {
                console.log(data);
                return false;
                },
                success: function(data) {
                    alert('Added Successfully!!!.');
                    
                if(data.response == 1){
                    $("#savepl").submit();
                }
                else
                {
                    
                } 
                }
            }); 
            } else {
            if (item_price.length == 0) {
                alert('Please enter item Price');
                return false;
            } else if (item_qty.length == 0) {
                alert('Please enter item Qty');
                return false;
            } else if (purchase_date.length == 0) {
                alert('Please enter valid date');
                return false;
            } else if (seller_name == 0 || seller_name == NaN) {
                alert('Please enter Supplier');
                return false
            } 
            }
        }); 
        
        $('#editprofit').on('click', function() {
            var lazada_trans_id                         = $("#ed_lazada_trans_id").val(); 
            var lazada_commission                       = $("#ed_lazada_commission").val(); 
            var lazada_payment_fee                      = $("#ed_lazada_payment_fee").val();
            var lazada_campaign_fee                     = $("#ed_lazada_campaign_fee").val(); 
            var lazada_lazcoins_discount                = $("#ed_lazada_lazcoins_discount").val(); 
            var lazada_shipping_fee_voucher_by_lazada   = $("#ed_lazada_shipping_fee_voucher_by_lazada").val();     
            var lazada_platform_shipping_fee_subsidy_tax= $("#ed_lazada_platform_shipping_fee_subsidy_tax").val();
            var lazada_shipping_fee_paid_by_customer    = $("#ed_lazada_shipping_fee_paid_by_customer").val(); 
            var lazcoins_discount_promotion_fee         = $("#ed_lazcoins_discount_promotion_fee").val(); 
            var lazada_item_price_credit                = $("#ed_lazada_item_price_credit").val(); 
            var ed_lazada_item_hidden_id                = $("#ed_lazada_item_hidden_id").val(); 

            console.log(lazada_trans_id);
            console.log(lazada_commission);
            console.log(lazada_payment_fee);
            console.log(lazada_lazcoins_discount);
            console.log(lazada_shipping_fee_voucher_by_lazada);
            console.log(lazcoins_discount_promotion_fee);
           
        if (lazada_trans_id > 0) {
          $.ajax({
            method: "POST",
            url: "/transaction/profitlossupdate",
            dataType:'json',
            data: {
                'transaction_id':lazada_trans_id,
                'lazada_commission':lazada_commission,
                'lazada_payment_fee':lazada_payment_fee,
                'lazada_campaign_fee':lazada_campaign_fee,
                'lazada_lazcoins_discount':lazada_lazcoins_discount,
                'lazada_shipping_fee_voucher':lazada_shipping_fee_voucher_by_lazada,
                'lazada_platform_shipping_tax':lazada_platform_shipping_fee_subsidy_tax,
                'lazada_shipping_paid_by_customer':lazada_shipping_fee_paid_by_customer,
                'lazcoins_discount_promotion_fee':lazcoins_discount_promotion_fee,
                'lazada_item_price_credit':lazada_item_price_credit,
                'ed_lazada_item_hidden_id':ed_lazada_item_hidden_id
            },
            beforeSend: function(){
                
            },
            error: function(data) {
              console.log(data);
              <!-- alert(data); -->
             <!-- alert(JSON.stringify(data)); -->
              return false;

            },
            success: function(data) {
            alert('Modified Successfully!!!.');
              if(data == 1){

                $("#editpl").submit();
              }
              else
              {
                
              } 
            }
          }); 
        } else {
          <!-- alert('Ind');  -->
        }
        });
        
        $('#saveshopeepl').on('click', function() {
                var shopee_trans_id                                 = $("#shopee_trans_id").val(); 
                var shopee_shippingfee_paid_by_buyer                = $("#shopee_shippingfee_paid_by_buyer").val(); 
                var shopee_shippingfee_charged_by_logistic_provider = $("#shopee_shippingfee_charged_by_logistic_provider").val();
                var shopee_seller_paid_shippingfee                  = $("#shopee_seller_paid_shippingfee").val(); 
                var shopee_product_discount_rebate                  = $("#shopee_product_discount_rebate").val(); 
                var shopee_commission                               = $("#shopee_commission").val();     
                var shopee_service_fee                              = $("#shopee_service_fee").val();
                var shopee_transaction_fee                          = $("#shopee_transaction_fee").val(); 
                var shopee_saver_programme_fee                      = $("#shopee_saver_programme_fee").val(); 
                var shopee_ams_commission_fee                       = $("#shopee_ams_commission_fee").val(); 
                var shopee_other_fee                                = $("#shopee_other_fee").val(); 
                var shopee_item_price_credit                        = $("#shopee_item_price_credit").val(); 

                console.log(shopee_trans_id);
                console.log(shopee_commission);
                console.log(shopee_service_fee);
                console.log(shopee_transaction_fee);
                console.log(shopee_item_price_credit);
                

            if (shopee_commission.length > 0 && shopee_service_fee.length > 0 && shopee_transaction_fee.length > 0 && shopee_item_price_credit.length > 0 && shopee_item_price_credit > 0) {
                    
            $.ajax({
                method: "POST",
                url: "/transaction/shopeesaveprofitloss",
                dataType:'json',
                data: {
                    'transaction_id':shopee_trans_id,
                    'shopee_shippingfee_paid_by_buyer':shopee_shippingfee_paid_by_buyer,
                    'shopee_shippingfee_charged_by_logistic_provider':shopee_shippingfee_charged_by_logistic_provider,
                    'shopee_seller_paid_shippingfee':shopee_seller_paid_shippingfee,
                    'shopee_product_discount_rebate':shopee_product_discount_rebate,
                    'shopee_commission':shopee_commission,
                    'shopee_service_fee':shopee_service_fee,
                    'shopee_transaction_fee':shopee_transaction_fee,
                    'shopee_saver_programme_fee':shopee_saver_programme_fee,
                    'shopee_ams_commission_fee':shopee_ams_commission_fee,
                    'shopee_other_fee':shopee_other_fee,
                    'shopee_item_price_credit':shopee_item_price_credit
                },
                beforeSend: function(){
                    
                },
                error: function(data) {
                console.log(data);
                return false;
                },
                success: function(data) {
                    alert('Added Successfully!!!.');
                    
                if(data.response == 1){
                    $("#saveshopeepl").submit();
                }
                else
                {
                    
                } 
                }
            }); 
            } else {
                /* if (shopee_item_price_credit.length == 0) {
                    alert('Please enter item Price');
                    return false;
                } else if (shopee_commission.length == 0) {
                    alert('Please enter commission');
                    return false;
                }  */
            }
        });

        $('#editshopeepl').on('click', function() {
                var shopee_trans_id                                 = $("#ed_shopee_trans_id").val(); 
                var shopee_shippingfee_paid_by_buyer                = $("#ed_shopee_shippingfee_paid_by_buyer").val(); 
                var shopee_shippingfee_charged_by_logistic_provider = $("#ed_shopee_shippingfee_charged_by_logistic_provider").val();
                var shopee_seller_paid_shippingfee                  = $("#ed_shopee_seller_paid_shippingfee").val(); 
                var shopee_product_discount_rebate                  = $("#ed_shopee_product_discount_rebate").val(); 
                var shopee_commission                               = $("#ed_shopee_commission").val();     
                var shopee_service_fee                              = $("#ed_shopee_service_fee").val();
                var shopee_transaction_fee                          = $("#ed_shopee_transaction_fee").val(); 
                var shopee_saver_programme_fee                      = $("#ed_shopee_saver_programme_fee").val(); 
                var shopee_ams_commission_fee                       = $("#ed_shopee_ams_commission_fee").val(); 
                var shopee_other_fee                                = $("#ed_shopee_other_fee").val(); 
                var shopee_item_price_credit                        = $("#ed_shopee_item_price_credit").val(); 
                var ed_shopee_item_hidden_id                        = $("#ed_shopee_item_hidden_id").val();

                console.log(shopee_trans_id);
                console.log(shopee_commission);
                console.log(shopee_service_fee);
                console.log(shopee_transaction_fee);
                console.log(shopee_item_price_credit);
           
        if (shopee_trans_id > 0) {
          $.ajax({
            method: "POST",
            url: "/transaction/shopeeprofitlossupdate",
            dataType:'json',
            data: {
                    'transaction_id':shopee_trans_id,
                    'shopee_shippingfee_paid_by_buyer':shopee_shippingfee_paid_by_buyer,
                    'shopee_shippingfee_charged_by_logistic_provider':shopee_shippingfee_charged_by_logistic_provider,
                    'shopee_seller_paid_shippingfee':shopee_seller_paid_shippingfee,
                    'shopee_product_discount_rebate':shopee_product_discount_rebate,
                    'shopee_commission':shopee_commission,
                    'shopee_service_fee':shopee_service_fee,
                    'shopee_transaction_fee':shopee_transaction_fee,
                    'shopee_saver_programme_fee':shopee_saver_programme_fee,
                    'shopee_ams_commission_fee':shopee_ams_commission_fee,
                    'shopee_other_fee':shopee_other_fee,
                    'shopee_item_price_credit':shopee_item_price_credit,
                    'ed_shopee_item_hidden_id':ed_shopee_item_hidden_id
            },
            beforeSend: function(){
                
            },
            error: function(data) {
              console.log(data);
              <!-- alert(data); -->
             <!-- alert(JSON.stringify(data)); -->
              return false;

            },
            success: function(data) {
            alert('Modified Successfully!!!.');
              if(data == 1){

                $("#editShopeePL").submit();
              }
              else
              {
                
              } 
            }
          }); 
        } else {
          <!-- alert('Ind');  -->
        }
        });
        
        $('#savetiktokpl').on('click', function() {
                var tiktok_trans_id                                 = $("#tiktok_trans_id").val(); 
                var tiktok_subtotal_after_discounts                 = $("#tiktok_subtotal_after_discounts").val(); 
                var tiktok_subtotal_before_discounts                = $("#tiktok_subtotal_before_discounts").val();
                var tiktok_seller_discounts                         = $("#tiktok_seller_discounts").val(); 
                var tiktok_transaction_fee                          = $("#tiktok_transaction_fee").val(); 
                var tiktok_commission_fee                           = $("#tiktok_commission_fee").val();     
                var tiktok_actual_shipping_fee                      = $("#tiktok_actual_shipping_fee").val();
                var tiktok_platform_shipping_fee_discount           = $("#tiktok_platform_shipping_fee_discount").val(); 
                var tiktok_customer_shipping_fee_before_discounts   = $("#tiktok_customer_shipping_fee_before_discounts").val(); 
                var tiktok_seller_shipping_fee_discount             = $("#tiktok_seller_shipping_fee_discount").val(); 
                var tiktok_tiktokshop_shipping_fee_discount         = $("#tiktok_tiktokshop_shipping_fee_discount").val(); 
                var tiktok_actual_return_shipping_fee               = $("#tiktok_actual_return_shipping_fee").val(); 
                var tiktok_refunded_customer_shipping_fee           = $("#tiktok_refunded_customer_shipping_fee").val(); 
                var tiktok_shipping_subsidy                         = $("#tiktok_shipping_subsidy").val(); 
                var tiktok_affiliate_commission                     = $("#tiktok_affiliate_commission").val(); 
                var tiktok_bonus_cashback_service_fee               = $("#tiktok_bonus_cashback_service_fee").val(); 
                var tiktok_voucher_xtra_service_fee                 = $("#tiktok_voucher_xtra_service_fee").val();
                var tiktok_other_fees                               = $("#tiktok_other_fees").val(); 
                var tiktok_total_settlement_amount                  = $("#tiktok_total_settlement_amount").val(); 
                
                console.log(tiktok_trans_id);
                console.log(tiktok_subtotal_after_discounts);
                console.log(tiktok_subtotal_before_discounts);
                console.log(tiktok_transaction_fee);
                console.log(tiktok_total_settlement_amount);
                

            if (tiktok_subtotal_after_discounts.length > 0 && tiktok_subtotal_before_discounts.length > 0 && tiktok_total_settlement_amount.length > 0) {
                   
            $.ajax({
                method: "POST",
                url: "/transaction/tiktoksaveprofitloss",
                dataType:'json',
                data: {
                    'transaction_id':tiktok_trans_id,
                    'tiktok_subtotal_after_discounts':tiktok_subtotal_after_discounts,
                    'tiktok_subtotal_before_discounts':tiktok_subtotal_before_discounts,
                    'tiktok_seller_discounts':tiktok_seller_discounts,
                    'tiktok_transaction_fee':tiktok_transaction_fee,
                    'tiktok_commission_fee':tiktok_commission_fee,
                    'tiktok_actual_shipping_fee':tiktok_actual_shipping_fee,
                    'tiktok_platform_shipping_fee_discount':tiktok_platform_shipping_fee_discount,
                    'tiktok_customer_shipping_fee_before_discounts':tiktok_customer_shipping_fee_before_discounts,
                    'tiktok_seller_shipping_fee_discount':tiktok_seller_shipping_fee_discount,
                    'tiktok_tiktokshop_shipping_fee_discount':tiktok_tiktokshop_shipping_fee_discount,
                    'tiktok_actual_return_shipping_fee':tiktok_actual_return_shipping_fee,
                    'tiktok_refunded_customer_shipping_fee':tiktok_refunded_customer_shipping_fee,
                    'tiktok_shipping_subsidy':tiktok_shipping_subsidy,
                    'tiktok_affiliate_commission':tiktok_affiliate_commission,
                    'tiktok_bonus_cashback_service_fee':tiktok_bonus_cashback_service_fee,
                    'tiktok_voucher_xtra_service_fee':tiktok_voucher_xtra_service_fee,
                    'tiktok_other_fees':tiktok_other_fees,
                    'tiktok_total_settlement_amount':tiktok_total_settlement_amount
                },
                beforeSend: function(){
                    
                },
                error: function(data) {
                console.log(data);
                return false;
                },
                success: function(data) {
                    alert('Added Successfully!!!.');
                    
                if(data.response == 1){
                    $("#savetiktokpl").submit();
                }
                else
                {
                    
                } 
                }
            }); 
            } else {
                /*alert('Please enter item Price');
                return false; */
           

            }
        });

        $('#edittiktokpl').on('click', function() {
             
                var tiktok_trans_id                                 = $("#ed_tiktok_trans_id").val(); 
                var tiktok_subtotal_after_discounts                 = $("#ed_tiktok_subtotal_after_discounts").val(); 
                var tiktok_subtotal_before_discounts                = $("#ed_tiktok_subtotal_before_discounts").val();
                var tiktok_seller_discounts                         = $("#ed_tiktok_seller_discounts").val(); 
                var tiktok_transaction_fee                          = $("#ed_tiktok_transaction_fee").val(); 
                var tiktok_commission_fee                           = $("#ed_tiktok_commission_fee").val();     
                var tiktok_actual_shipping_fee                      = $("#ed_tiktok_actual_shipping_fee").val();
                var tiktok_platform_shipping_fee_discount           = $("#ed_tiktok_platform_shipping_fee_discount").val(); 
                var tiktok_customer_shipping_fee_before_discounts   = $("#ed_tiktok_customer_shipping_fee_before_discounts").val(); 
                var tiktok_seller_shipping_fee_discount             = $("#ed_tiktok_seller_shipping_fee_discount").val(); 
                var tiktok_tiktokshop_shipping_fee_discount         = $("#ed_tiktok_tiktokshop_shipping_fee_discount").val(); 
                var tiktok_actual_return_shipping_fee               = $("#ed_tiktok_actual_return_shipping_fee").val(); 
                var tiktok_refunded_customer_shipping_fee           = $("#ed_tiktok_refunded_customer_shipping_fee").val(); 
                var tiktok_shipping_subsidy                         = $("#ed_tiktok_shipping_subsidy").val(); 
                var tiktok_affiliate_commission                     = $("#ed_tiktok_affiliate_commission").val();
                var tiktok_bonus_cashback_service_fee               = $("#ed_tiktok_bonus_cashback_service_fee").val(); 
                var tiktok_voucher_xtra_service_fee                 = $("#ed_tiktok_voucher_xtra_service_fee").val();
                var tiktok_other_fees                               = $("#ed_tiktok_other_fees").val(); 
                var tiktok_total_settlement_amount                  = $("#ed_tiktok_total_settlement_amount").val(); 
                var ed_tiktok_item_hidden_id                        = $("#ed_tiktok_item_hidden_id").val(); 
                
        if (tiktok_trans_id > 0) {
          $.ajax({
            method: "POST",
            url: "/transaction/tiktokprofitlossupdate",
            dataType:'json',
            data: {
                    'transaction_id':tiktok_trans_id,
                    'tiktok_subtotal_after_discounts':tiktok_subtotal_after_discounts,
                    'tiktok_subtotal_before_discounts':tiktok_subtotal_before_discounts,
                    'tiktok_seller_discounts':tiktok_seller_discounts,
                    'tiktok_transaction_fee':tiktok_transaction_fee,
                    'tiktok_commission_fee':tiktok_commission_fee,
                    'tiktok_actual_shipping_fee':tiktok_actual_shipping_fee,
                    'tiktok_platform_shipping_fee_discount':tiktok_platform_shipping_fee_discount,
                    'tiktok_customer_shipping_fee_before_discounts':tiktok_customer_shipping_fee_before_discounts,
                    'tiktok_seller_shipping_fee_discount':tiktok_seller_shipping_fee_discount,
                    'tiktok_tiktokshop_shipping_fee_discount':tiktok_tiktokshop_shipping_fee_discount,
                    'tiktok_actual_return_shipping_fee':tiktok_actual_return_shipping_fee,
                    'tiktok_refunded_customer_shipping_fee':tiktok_refunded_customer_shipping_fee,
                    'tiktok_shipping_subsidy':tiktok_shipping_subsidy,
                    'tiktok_affiliate_commission':tiktok_affiliate_commission,
                    'tiktok_bonus_cashback_service_fee':tiktok_bonus_cashback_service_fee,
                    'tiktok_voucher_xtra_service_fee':tiktok_voucher_xtra_service_fee,
                    'tiktok_other_fees':tiktok_other_fees,
                    'tiktok_total_settlement_amount':tiktok_total_settlement_amount,
                    'ed_tiktok_item_hidden_id':ed_tiktok_item_hidden_id
            },
            beforeSend: function(){
                
            },
            error: function(data) {
              console.log(data);
              <!-- alert(data); -->
             <!-- alert(JSON.stringify(data)); -->
              return false;

            },
            success: function(data) {
            alert('Modified Successfully!!!.');
              if(data == 1){

                $("#editTiktokPL").submit();
              }
              else
              {
                
              } 
            }
          }); 
        } else {
          <!-- alert('Ind');  -->
        }
        });
        
        $('body').on('click', '.deleteTiktokPL', function(){ 
          
        var pl_id = $(this).attr('data-id');
        if (pl_id > 0) { 
                if (confirm('Are you sure you want to delete this item?')) {
                  $.ajax({
                    method: "POST",
                    url: "/transaction/deletetiktokprofitloss",
                    dataType:'json',
                    data: {
                        'id': pl_id,
                    },
                    beforeSend: function(){
                        
                    },
                    error: function(data) {
                      console.log(data);
                    },
                    success: function(data) {
                      alert('Item has deleted successfully.');
                      location.reload();
                    },
                  });
                }

         }
    });

         $(function() {
                var date = $('#datepicker1').datepicker({ dateFormat: 'yy-mm-dd'}).val();
          });
    
@stop