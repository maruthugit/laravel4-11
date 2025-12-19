<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class Fees extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;
    /**
     * Table for Fees.
     *
     * @var string
     */
    protected $table = 'jocom_fees';

    public static $rules = array(
        'process_fees'=>'required|numeric',
        'email_activation'=>'required',
        'delivery_charges'=>'required|numeric',
        'gst'=>'required|numeric',
        'currency'=>'required',
        'currency_code'=>'required',
    );

    public static $message = array(
        'process_fees.required'=>'Please enter Process Fees',
        'delivery_charges.required'=>'Please enter correct Delivery Charges',
        'gst.required'=>'Please enter correct GST',
        'currency.required'=>'Please enter correct Currency',
        'currency_code.required'=>'Please enter correct Currency Code(ISO)',
        'email_activation.required'=>'Please choose email activation setup'
    );



    /**
     * Save fees
     * @return [type] [description]
     */
    public function scopeSave_fees()
    {
        if (Input::has('id'))
        {
            $fees_id = Input::get('id');
            $fees = Fees::find($fees_id);
            $fees->process_fees = Input::get('process_fees');
            $fees->delivery_charges = Input::get('delivery_charges');
            $fees->gst = Input::get('gst');
            $fees->gst_status = Input::get('gst_status');
            $fees->currency = Input::get('currency');
            $fees->currency_code = Input::get('currency_code');
            $fees->email_activation = Input::get('email_activation');
            $fees->save();

            return $fees;
        }
        else
        {
            return false;
        }            
        
    }

    public static function get_delivery_charges()
    {
        $delivery_charges = '0';

        $feesrow = Fees::find(1);

        if(count($feesrow)!=0)
        {
            $delivery_charges = $feesrow->delivery_charges;
        }

        return $delivery_charges;
    }

    public static function get_process_fees()
    {
        $process_fees = '0';

        $feesrow = Fees::find(1);

        if(count($feesrow)!=0)
        {
            $process_fees = $feesrow->process_fees;
        }
        
        return $process_fees;              
    }

    public static function get_gst()
    {
        $feesrow = Fees::find(1);

        // if ($feesrow->gst_status == 1){
        //     $gst = $feesrow->gst;
        // }
        // else 
        // {
        //     $gst = 0;
        // }

        $gst = $feesrow->gst;

        return $gst;              
    }

    public static function get_gst_status()
    {
        $feesrow = Fees::find(1);
        
        $gst = $feesrow->gst_status;
        return $gst;              
    }
    
    public static function get_tax_percent($delivery_country_id = 458)
    {
        switch ($delivery_country_id) {
            case 458: // Malaysia
                $feesrow = Fees::where("country_id",$delivery_country_id)->first();
                break;
            case 156: // China
                $feesrow = Fees::where("country_id",$delivery_country_id)->first();
                break;
            default: // Malaysia
                $feesrow = Fees::find(1);
                break;
        }
        
        $gst = $feesrow->gst;
        return $gst;              
    }

    public static function get_currency()
    {
        $feesrow = Fees::find(1);
        
        $currency = $feesrow->currency;
        return $currency;              
    }

    
    // Delivery Fees CMS 2.9.0
    public function GetTotalDelivery($transac_data_detail = [])
    {

        $temp_weight = array();

        // Sum weight by Zone
        foreach ($transac_data_detail as $drow)
        {
            $temp_weight[$drow["zone_id"]] += $drow["total_weight"];
        }

        foreach ($temp_weight as $zone => $weight)
        {
            $temprow = DB::table('jocom_zones')
                ->select('*')
                ->where('id', '=', $zone)
                ->first();

            if (count($temprow)>0)
            {
                if (isset($temprow->init_weight) AND $temprow->init_weight >= 0)
                    $fees += Fees::GetFees($weight, $temprow->init_weight, $temprow->init_price, $temprow->add_weight, $temprow->add_price);
                else
                    $fees += Fees::get_delivery_charges();
            }
        }

        return $fees;
    }

    public function GetFees($weight, $init_weight, $init_price, $add_weight, $add_price)
    {
        $temp_fees = 0;

        if ($weight > 0)
        {
            // init weight
            $temp_fees = $init_price;
            $weight -= $init_weight;

            // additional weight
            if ($weight > 0 AND $add_weight >0 AND $add_price != NULL)
            {
                $temp_fees += Fees::AddFees($weight, $add_weight, $add_price);
            }
        }
        else
            $temp_fees = Fees::get_delivery_charges();

        return $temp_fees;
    }

    public function AddFees($weight, $add_weight, $add_price)
    {
        $multi = floor($weight/$add_weight);

        $remain = $weight%$add_weight;
        if ($remain > 0)
            $multi++;

        return $add_price * $multi;
    }    
    
}
