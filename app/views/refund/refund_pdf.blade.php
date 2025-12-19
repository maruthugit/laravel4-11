
<page backtop="45mm" backbottom="10mm" backleft="0mm" backright="0mm">
<page_header>
    <table style="width: 100%;" border="0">
        <tr>
            <td style="width: 100%; text-align: center">
                <img src="img/tmgrocer_refund_icon.png" />
                <!--<img src="public/img/jocom_refund_icon.jpg" />-->
            </td>
        </tr>
        <tr>
            <td style="width: 100%; text-align: center">
                <h3>REFUND REQUEST FORM</h3>
            </td>
        </tr>
    </table>
</page_header>

<page_footer>
    <table style="padding: 10px;">
        <tr>
            <td>
                <br><br>
                This is a computer generated document. No signature required.
            </td>
        </tr>
    </table>     
</page_footer>

<table style="width: 100%;">
    <tr>
        <td style="width: 20%; padding: 10px;">
            <strong>Customer Name :</strong>
        </td>
        <td colspan="3" style="width: 80%; padding: 10px; border-bottom: 1px solid #000;" valign="center">
            {{ $display_details['customer_name'] }}
        </td>
    </tr>
    <tr>
        <td style="width: 20%; padding: 10px;">
            <strong>I/C No :</strong>
        </td>
        <td style="width: 30%; padding: 10px; border-bottom: 1px solid #000;">
            {{ $display_details['ic_no'] }}
        </td>
        <td style="width: 20%; padding: 10px;">
            <strong>H/P No :</strong>
        </td>
        <td style="width: 30%; padding: 10px; border-bottom: 1px solid #000;">
            {{$display_details['hp_no']}}
        </td>
    </tr>
    <tr>
        <td style="width: 20%; padding: 10px;">
            <strong>Email :</strong>
        </td>
        <td colspan="3" style="width: 80%; padding: 10px; border-bottom: 1px solid #000;">
            {{ $display_details['email'] }}
        </td>
    </tr>
    <tr>
        <td style="width: 20%; padding: 10px;">
            <strong>Address :</strong>
        </td>
        <td colspan="3" style="width: 80%; padding: 10px; border-bottom: 1px solid #000;">
            {{ $display_details['address'] }}
        </td>
    </tr>
    <tr>
        <td style="width: 20%; padding: 10px;">
            <strong>Post Code :</strong>
        </td>
        <td colspan="3" style="width: 80%; padding: 10px; border-bottom: 1px solid #000;">
            {{ $display_details['postcode'] }} 
            {{-- <div style="border-bottom: 1px solid #000;"> {{ $display_details['postcode'] }} </div> --}}
        </td>
    </tr>
    <tr>
        <td style="width: 20%; padding: 10px;">
            <strong>Bank Name :</strong>
        </td>
        <td colspan="3" style="width: 80%; padding: 10px; border-bottom: 1px solid #000;">
            {{ $display_details['bank_name'] }}
        </td>
    </tr>
    <tr>
        <td style="width: 20%; padding: 10px;">
            <strong>Bank Account No :</strong>
        </td>
        <td colspan="3" style="width: 80%; padding: 10px; border-bottom: 1px solid #000;">
            {{ $display_details['bank_account'] }}
        </td>
    </tr>
    <tr>
        <td style="width: 20%; padding: 10px;">
            <strong>Transaction ID :</strong>
        </td>
        <td colspan="3" style="width: 80%; padding: 10px; border-bottom: 1px solid #000;">
            {{ $display_details['trans_id'] }}
        </td>
    </tr>
    <tr>
        <td style="width: 20%; padding: 10px;">
            <strong>Order No :</strong>
        </td>
        <td colspan="3" style="width: 80%; padding: 10px; border-bottom: 1px solid #000;">
            {{ $display_details['order_no'] }}
        </td>
    </tr>
    <tr>
        <td style="width: 20%; padding: 10px;">
            <strong>Platform Store :</strong> 
        </td>
        <td colspan="3" style="width: 80%; padding: 10px; border-bottom: 1px solid #000;">
            {{ $display_details['platform_store'] }}
        </td>
    </tr>
    <tr>
        <td style="width: 20%; padding: 10px;">
            <strong>Total (RM) :</strong>
        </td>
        <td colspan="3" style="width: 80%; padding: 10px; border-bottom: 1px solid #000;">
            RM {{ $display_details['total'] }}
        </td>
    </tr>
    <tr>
        <td style="width: 20%; padding: 10px;">
            <strong>Remarks :</strong>
        </td>
        <td colspan="3" style="width: 80%; padding: 10px; border-bottom: 1px solid #000;">
            {{ nl2br($display_details['remarks']) }}
            {{-- {{ Form::textarea('remark', $display_details['remarks'], ['class' => 'form-control', 'rows' => '5']) }} --}}
        </td>
    </tr>
    <tr>
        <td style="width: 20%; padding: 10px;">
            <strong>Supporting Documents :</strong>
        </td>
        <td colspan="3" style="width: 80%; padding: 10px; border-bottom: 1px solid #000;">
            <br>{{ $display_details['supp_docs'] }}
        </td>
    </tr>
</table><br><br><br>


<table style="width: 100%;">
    <tr>
        <td style="width: 20%; padding: 10px;">
            <strong>Request By :</strong>
        </td>
        <td style="width: 30%; padding: 10px; border-bottom: 1px solid #000;">
            {{ $display_details['request_by'] }}
        </td>
        <td style="width: 20%; padding: 10px;">
            <strong>Approved By :</strong>
        </td>
        <td style="width: 30%; padding: 10px; border-bottom: 1px solid #000;">
            {{ $display_details['approve_by'] }}
        </td>
    </tr>
    <tr>
        <td style="width: 20%; padding: 10px;">
            <strong>Date :</strong>
        </td>
        <td style="width: 30%; padding: 10px; border-bottom: 1px solid #000;">
            {{ $display_details['date_request'] }}
        </td>
        <td style="width: 20%; padding: 10px;">
            <strong>Date :</strong>
        </td>
        <td style="width: 30%; padding: 10px; border-bottom: 1px solid #000;">
            {{ $display_details['date_approve'] }}
        </td>
    </tr>
</table>
<br><br>

<hr style="border-top: 3px dashed;">
<br><br>
<table style="width: 100%;">
    <tr>
        <td style="width: 20%; padding: 10px;">
            <strong>Invoice No :</strong>
        </td>
        <td style="width: 30%; padding: 10px; border-bottom: 1px solid #000;">
            {{ $display_details['invoice_no'] }}
        </td>
        <td style="width: 20%; padding: 10px;">
            <strong>Date of Credit Note :</strong> 
        </td>
        <td style="width: 30%; padding: 10px; border-bottom: 1px solid #000;">
            {{ $display_details['date_credit_note'] }}
        </td>
    </tr>
    <tr>
        <td style="width: 20%; padding: 10px;">
            <strong>Prepared By:</strong>
        </td>
        <td style="width: 30%; padding: 10px; border-bottom: 1px solid #000;">
            {{ $display_details['finance_prepared_by'] }}
        </td>
        <td style="width: 20%; padding: 10px;">
            <strong>Approved By :</strong>
        </td>
        <td style="width: 30%; padding: 10px; border-bottom: 1px solid #000;">
            {{ $display_details['finance_approve_by'] }}
        </td>
    </tr>
</table>
</page>