<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class Paypal extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;
    
    public $business;
    public $currency;
    public $cursymbol;
    public $location;
    public $returnurl;
    public $returntxt;
    public $cancelurl;
    public $items;
    
    
    function scopeGet_setting() 
    {
        $test = Config::get('constants.ENVIRONMENT');

        $return_url = asset('/') . 'checkout';
        $cancel_url = asset('/') . 'checkout/cancelled';
        $notify_url = asset('/') . 'checkout/notify';

        if ($test == 'test')
        {
            // $return_url = asset('/') . 'checkout';
            // $cancel_url = asset('/') . 'checkout/cancelled';
            // $notify_url = asset('/') . 'checkout/notify';

            $settings = array(
            'business' => 'eugene.lee-facilitator@jocom.my',         //paypal email address
            'currency' => 'MYR',                       //paypal currency
            'cursymbol'=> 'MYR',                   //currency symbol
            'location' => 'MY',                        //location code  (ex GB)

            'returnurl'=> $return_url,//where to go back when the transaction is done.
            
            'returntxt'=> 'Return to Jocom',         //What is written on the return button in paypal

            'notifyurl'=> $notify_url,//For PayPal to notify server.

            'cancelurl'=> $cancel_url,//Where to go if the user cancels.
            
            'shipping' => 0,                           //Shipping Cost
            'custom'   => ''                           //Custom attribute
            );
        }
        else
        {
            $settings = array(
            'business' => 'joshua.sew@gmail.com',         //paypal email address
            'currency' => 'MYR',                       //paypal currency
            'cursymbol'=> 'MYR',                   //currency symbol
            'location' => 'MY',                        //location code  (ex GB)


            'returnurl'=> $return_url,//where to go back when the transaction is done.
            
            'returntxt'=> 'Return to Jocom',         //What is written on the return button in paypal

            'notifyurl'=> $notify_url,//For PayPal to notify server.

            'cancelurl'=> $cancel_url,//Where to go if the user cancels.

            
            // 'returnurl'=> 'http://payment.jocom.com.my/checkout',//where to go back when the transaction is done.
            // //
            // 'returntxt'=> 'Return to Jocom',         //What is written on the return button in paypal
            
            // 'notifyurl'=> 'http://payment.jocom.com.my/checkout/notify',//For PayPal to notify server.

            // 'cancelurl'=> 'http://payment.jocom.com.my/checkout/cancelled',//Where to go if the user cancels.
            
            'shipping' => 0,                           //Shipping Cost
            'custom'   => ''                           //Custom attribute
            );
        }
        
        //overrride default settings
        
        
        //Set the class attributes
        // $this->business  = $settings['business'];
        // $this->currency  = $settings['currency'];
        // $this->cursymbol = $settings['cursymbol'];
        // $this->location  = $settings['location'];
        // $this->returnurl = $settings['returnurl'];
        // $this->returntxt = $settings['returntxt'];
        // $this->cancelurl = $settings['cancelurl'];
        // $this->shipping  = $settings['shipping'];
        // $this->custom    = $settings['custom'];
        // $this->items = array();
        return $settings;
        
    }
    
    
    //=====================================//
    //==> Add a simple item to the cart <==//
    //=====================================//
    // public function scopeAddSimpleItem($item){
    //  if(!empty($item['quantity']) && is_numeric($item['quantity']) && $item['quantity']>0 && !empty($item['name'])) { //And add the item to the cart if it is correct
    //      $items = $this->items;
    //      $items[] = $item;
    //      $this->items = $items;
    //  }
    // }
    
    //=========================================//
    //==> Add an array of items to the cart <==//
    //=========================================//
    // public function scopeAddMultipleItems($items){
    //  if(!empty($items)){
    //      foreach($items as $item){ //lopp through the items
    //          $this->addSimpleItem($item);  //And add them 1 by 1
    //      }
    //  }
    // }
    
    //=====================//
    //==> Checkout Form <==//
    //=====================//
    // public function scopeGetCheckoutForm($test=false){
        
    //  $form='';
        
    //  //==> Variables defining a cart, there shouldn't be a need to change those <==//
    //  $form.='
    //  <input type="hidden" name="cmd" value="_cart" />
    //  <input type="hidden" name="upload" value="1" />         
    //  <input type="hidden" name="no_note" value="0" />                        
    //  <input type="hidden" name="bn" value="PP-BuyNowBF" />                   
    //  <input type="hidden" name="tax" value="0" />            
    //  <input type="hidden" name="rm" value="2" />';
        
    //  //==> Personnalised variables, they get their values from the specified settings nd the class attributes <==//
    //  $form.='
    //  <input type="hidden" name="business" value="'.$this->business.'" />
    //  <input type="hidden" name="handling_cart" value="'.$this->shipping.'" />
    //  <input type="hidden" name="currency_code" value="'.$this->currency.'" />
    //  <input type="hidden" name="lc" value="'.$this->location.'" />
    //  <input type="hidden" name="return" value="'.$this->returnurl.'" />          
    //  <input type="hidden" name="cbt" value="'.$this->returntxt.'" />
    //  <input type="hidden" name="cancel_return" value="'.$this->cancelurl.'" />           
    //  <input type="hidden" name="custom" value="'.$this->custom.'" />';
        
            
    //  return $form;
    // }
    
    
}




?>