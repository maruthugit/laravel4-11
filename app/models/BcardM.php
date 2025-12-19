<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class BcardM extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;
    /**
     * Table for bcard.
     *
     * @var string
     */
    protected $table = 'bcard';

    /**
     * Listing for coupon
     * @return [type] [description]
     */
    public static function new_card()
    {
        // $num = DB::table('bcard_list')->select('bcard')->where('status', '=', '0')->orderBy('id', 'asc')->first();
        $num = BcardList::select('id', 'bcard')->where('status', '=', '0')->orderBy('id', 'asc')->first();

        if (count($num) > 0)
        {
            $card = array_merge(Config::get('points.bcard'), [
                    'Card'          => $num->bcard,
                    'FullName'      => trim(Input::get('fullname')),
                    'IC'            => trim(Input::get('ic')),
                    'BirthDate'     => trim(Input::get('birthdate')),
                    'Gender'        => strtoupper(trim(Input::get('gender'))),
                    'Race'          => strtoupper(trim(Input::get('race'))),
                    'Nationality'   => strtoupper(trim(Input::get('nationality'))),
                    // 'OwnCar'        => strtoupper(trim(Input::get('owncar'))),
                    // 'OwnCreditCard' => strtoupper(trim(Input::get('owncreditcard'))),
                    'HomeAddress1'  => strtoupper(trim(Input::get('homeaddress1'))),
                    'HomeAddress2'  => strtoupper(trim(Input::get('homeaddress2'))),
                    'HomeAddress3'  => strtoupper(trim(Input::get('homeaddress3'))),
                    'HomeCity'      => strtoupper(trim(Input::get('homecity'))),
                    'HomeState'     => strtoupper(trim(Input::get('homestate'))),
                    'HomeCountry'   => strtoupper(trim(Input::get('homecountry'))),
                    'HomeZip'       => trim(Input::get('homezip')),
                    'HomeEmail'     => trim(Input::get('homeemail')),
                    'MobilePhone'   => trim(Input::get('mobilephone')),
                    'MSISDN'        => '6'.trim(Input::get('mobilephone')),
                ]);

            $response = Bcard::api('RegisterMember', $card);

            $result   = json_decode(json_encode($response), true);

            // var_dump($result);exit;
            // var_dump($response);exit;
            // echo ($result['message']);exit;
            // echo ($response->message);exit;

            // if (is_array($result))
            if ($response->message == '#1901')
            {
                $list               = BcardList::find($num->id);
                $list->remark       = json_encode($card);
                $list->status       = 1;
                $list->updated_at   = date('Y-m-d H:i:s');
                $list->save();

                $count = BcardList::where('status', '=', '0')->count();

                if ($count < 50)
                {
                    $subject = "tmGrocer BCard List Below Treshold";
                    $body = "Your new BCard listing is below 50, urgently obtain more cards!";

                    $data = array('notify_body' => $body);

                    $test = Config::get('constants.ENVIRONMENT');
                    $testmail = Config::get('constants.TEST_MAIL');
                    if ($test == 'test')
                    {
                        $mail = $testmail;
                    }
                    else
                    {
                        $mail = ['joshua.sew@jocom.my', 'johnny.lin@jocom.my', 'webdev@jocom.my'];
                    }

                    Mail::send('emails.notification', $data, function($message) use ($subject, $mail)
                    {
                        $message->from('customersupport@tmgrocer.com', 'tmGrocer');
                        $message->to($mail, '')->subject($subject);
                    }
                    );
                }

                $update = BcardM::update_card($num->bcard, trim(Input::get('username')));

                // $check = BcardM::where('username', '=', trim(Input::get('username')))->first();

                // if (count($check) > 0)
                // {
                //     // update existing
                //     $check->bcard = $num->bcard;
                //     $check->save();
                // }
                // else
                // {
                //     // insert new card
                //     $BcardM             = new BcardM;
                //     $BcardM->username   = trim(Input::get('username'));
                //     $BcardM->bcard      = $num->bcard;
                //     $BcardM->save();
                // }
                

                $data = array(
                    'status'     => '1',
                    'message' => $response->message,
                    // message : xxxx
                    'bcard' => $num->bcard,
                );
            }
            else
            {
                $data = array(
                    'status'     => '0',
                    'message' => $response->message,
                    // message : xxxx
                    'bcard' => '',
                );
            }

        }
        else
        {
            $data = array(
                'status'     => '0',
                'message' => '#1902',
                // message : xxxx
                'bcard' => '',
            );
        }

        return $data;
    }

    public static function update_card($bcard, $username)
    {
        $check = BcardM::where('username', '=', $username)->first();

        if (count($check) > 0)
        {
            if ($bcard == "")
            {
                $check->delete();
            }
            else
            {
                // update existing
                $check->bcard = $bcard;
                $check->save();
            }            
        }
        else
        {
            if ($bcard != "")
            {
                // insert new card
                $BcardM             = new BcardM;
                $BcardM->username   = $username;
                $BcardM->bcard      = $bcard;
                $BcardM->save();
            }            
        }

        return "done";
    }

    
    
}
