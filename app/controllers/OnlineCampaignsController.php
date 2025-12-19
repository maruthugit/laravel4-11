<?php

/**
 * Online Campaigns Controller
 */
class OnlineCampaignsController extends BaseController
{


	public function anyIndex()
    {
        echo "Under development...";
    }


   
    /* Function: anyOnlinecampaignUsers
        Description : View list of all online campaign users
    */

    public function anyCampaignusers(){
    	
    	return View::make('onlinecampaigns.campaignusers');

    }

    /* Function: anyUserlists
        Description : Get all users
    */

    public function anyUserlists(){

    		try{
    				$userlist = OnlineCampaignUsers::select('id','campaign_id','name','email','created_at','status_activation')
    				                 ->whereNotIn('status_activation',[2])
    								 ->groupBy('email');
    				

    				return Datatables::of($userlist)
    									->make(true); 


    		} catch (Exception $ex) {

                echo $ex->getMessage();
            }


    }

    /* Function: anyEditcampaign
        Description : Active / DeActive online user 
        
        _INPUT_ :
		
		 ID : Campaign user ID  
    */

    public function anyEditcampaign($id){


    		$user  = OnlineCampaignUsers::findOrFail($id);

		        return View::make('onlinecampaigns.campaignedit', [
		            'user'         => $user,
		            'statusOptions' => [
		                0 => 'Inactive',
		                1 => 'Active',
	           		 ],
		        ]);


    }


    /* Function: anyUpdatecampaign
        Description : Generated Coupon code for the user. 
  
        _INPUT_ :
		 ID : Campaign user ID  
    */



    public function anyUpdatecampaign($id){
    	 try{
    	 		
    	 	 		// $couponCode = 'OCU'.date("h").strtoupper(substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 3)), 0, 3)).date("s");
    	 	 		$size = 6;
					$couponCode = 'OCU'.date("h").strtoupper(substr(md5(time().rand(10000,99999)), 0, $size)).date("s");
                    // die();
               

                    $email = Input::get('c_email');
                    $c_id = Input::get('c_id');
                    $c_name = Input::get('c_name');
                    $amount = Input::get('amount');
                    $valid_to = Input::get('valid_to');
                    
                    $Coupon = new Coupon;
                    $Coupon->coupon_code = $couponCode;
                    $Coupon->name = 'ONLINE CAMPAIGN VOUCHER';
                    $Coupon->amount = Input::get('amount');
                    $Coupon->amount_type = Input::get('amount_type');
                    $Coupon->min_purchase = Input::get('min_purchase');
                    $Coupon->valid_from = Input::get('valid_from');
                    $Coupon->valid_to = Input::get('valid_to');
                    $Coupon->type = 'all';
                    $Coupon->cqty = 1;
                    $Coupon->c_limit = 'Yes';
                    $Coupon->free_delivery = 0;
                    $Coupon->free_process = 0;
                    $Coupon->delivery_discount = 0;
                    $Coupon->status = 1;
                    $Coupon->insert_by = 'CMS';
                    $Coupon->save();
                    
                    $CouponID = $Coupon->id ;

                    $OnlineCUser = OnlineCampaignUsers::find($c_id);
					$OnlineCUser->status_activation = 1;
					$OnlineCUser->save();


					$OnlineCUserTransaction = new OnlineCampaignUsersTransaction;
					$OnlineCUserTransaction->u_id = $c_id;
					$OnlineCUserTransaction->coupon_id = $CouponID;
					$OnlineCUserTransaction->coupon_code = $couponCode;
					$OnlineCUserTransaction->amount = $amount;
					$OnlineCUserTransaction->amount_type = Input::get('amount_type');
					$OnlineCUserTransaction->valid_from = Input::get('valid_from');
					$OnlineCUserTransaction->valid_to = Input::get('valid_to');
					$OnlineCUserTransaction->created_by = 'CMS';
					$OnlineCUserTransaction->save();

                    
                    $data['coupon_code'] = $couponCode;
                    $data['wording_text'] = 'You have rewarded with voucher RM'.$amount.' ! Please redeem the code on any purchase in JOCOM APP before '.$valid_to." .";
                    
                    $subject = 'Enjoy your reward from JOCOM';
                    Mail::send('emails.couponreward', $data, function($message) use ($email,$c_name,$subject)
                    {
                        $message->from('payment@jocom.my', 'JOCOM');
                        $message->to($email, $c_name)->subject($subject);
                    });	
                return Redirect::to('onlinecampaign/campaignusers')->with('success', 'Coupon(ID: '.$CouponID.') added successfully');

	    	 } catch (Exception $ex) {
	            echo $ex;
	        }

    }

    /* Function: anyDeletecampaign
        Description : Delete the User

        _INPUT_ :
		 ID : Campaign user ID 
		

    */

    public function anyDeletecampaign($id){
    	
    	$user = OnlineCampaignUsers::find($id);
        $user->status_activation = '2';
        $user->updated_by = Session::get('username');
        $user->updated_at = date('Y-m-d H:i:s');
        
        $user->save();

        return Redirect::to('onlinecampaign/campaignusers')->with('success', 'Campaign (ID: '.$id.') deleted successfully');


    }
    

}