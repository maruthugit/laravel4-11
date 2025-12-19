<?php
/*
 * Currency Controller 
 */
class CurrencyController extends BaseController
{
    public function index(){
        
        $currency = DB::table('jocom_exchange_rate AS JER')->select(array(
            'JER.id',
            'JER.currency_code_from',
            'JER.amount_from',
            'JER.currency_code_to',
            'JER.amount_to',
           ))
           ->where('JER.id', '=',2)
           ->orderBy('JER.id','ASC')->first();
                
        return View::make('exchange.index')->with("currency",$currency);
        
    }
    
    public function save(){
        
    }
    
    /*
     * @desc : Update the selected exchange rate value
     */
    public function update(){
        
        try{
            DB::beginTransaction();
             
            $id = Input::get("id");
            $amt_from = Input::get("amt_from");
            $amt_to = Input::get("amt_to");

            $ExchangeRate = ExchangeRate::find($id);
            $ExchangeRate->amount_from = $amt_from;
            $ExchangeRate->amount_to = $amt_to;
            $ExchangeRate->updated_by = Session::get("username");
            $ExchangeRate->save();
            
            $ExchangeRateLog = new ExchangeRateLog();
            $ExchangeRateLog->exchange_id = $id;
            $ExchangeRateLog->currency_from_amount = $amt_from;
            $ExchangeRateLog->currency_to_amount = $amt_to;
            $ExchangeRateLog->updated_by = Session::get("username");
            $ExchangeRateLog->save();
        
        } catch (Exception $ex) {
            DB::rollback();
            return false;
        }
        DB::commit();
        return true;
        
    }
    
    public function getInfo($id){
        
        $currency = DB::table('jocom_exchange_rate AS JER')->select(array(
            'JER.id',
            'JER.currency_code_from',
            'JER.amount_from',
            'JER.currency_code_to',
            'JER.amount_to'
           ))->where('JER.id', $id)->first();
        
        return json_encode($currency);
    }
    
    public function getLog(){
        
        $id = Input::get("id");
        
        $currency = DB::table('jocom_exchange_rate AS JER')->select(array(
            'JER.currency_code_from',
            'JER.currency_code_to',
           ))->where('JER.id', $id)->first();
        
        $log = DB::table('jocom_exchange_rate_log AS JERL')->select(array(
            'JERL.id',
            'JERL.currency_from_amount',
            'JERL.currency_to_amount',
            'JERL.updated_at',
            'JERL.updated_by'
           ))->where('JERL.exchange_id', $id)->orderBy("id","desc")->get();
        
        return json_encode(array(
            "from_currency" => $currency->currency_code_from,
            "to_currency" => $currency->currency_code_to,
            "log" => $log,
        ));
    }
    
    public function getList(){
        
        $tasks = DB::table('jocom_exchange_rate AS JER')->select(array(
            'JER.id',
            'JER.currency_code_from',
            'JER.amount_from',
            'JER.currency_code_to',
            'JER.amount_to',
            'JER.activation',
            'JER.created_at',
            'JER.created_by',
            'JER.updated_at',
            'JER.updated_by',
           ))
           ->where('JER.activation', '=',1)
           ->orderBy('JER.id','ASC');
        
        return Datatables::of($tasks)->make(true);
        
        
    }
    
    public static function getExchangeRate($formCurrency , $toCurrency){
        
        $Rate = DB::table('jocom_exchange_rate_log AS JERL')->select(array(
            'JERL.id',
            'JERL.currency_code_from',
            'JERL.amount_from',
            'JERL.currency_code_to',
            'JERL.amount_to',
            'JERL.updated_at',
            'JERL.updated_by'
           ))->where('JERL.currency_code_from', $formCurrency)->where('JERL.currency_code_to', $toCurrency)->first();
           
           return $Rate;
        
    
    }
    
    public static function getRate($formCurrency , $toCurrency,$amount){
        
        $Rate = DB::table('jocom_exchange_rate AS JERL')->select(array(
            'JERL.id',
            'JERL.currency_code_from',
            'JERL.amount_from',
            'JERL.currency_code_to',
            'JERL.amount_to',
            'JERL.updated_at',
            'JERL.updated_by'
           ))->where('JERL.currency_code_from', $formCurrency)->where('JERL.currency_code_to', $toCurrency)->first();
           
        
        $totalAmount = round($amount * $Rate->amount_to, 2);
        
        return $totalAmount;
        
    
    }
    
    
    
}