<?php

class Automate extends Eloquent
{
    public static function bcardCampaign()
    {
        $process = 0;
        $date   = date('Ymd');
        $weekday = array('1','2','3','4','5');        
        $weekend = array('6','7');

        if (in_array(date('N'), $weekday))
        {
            $point = PointType::find(PointType::BCARD);

            if (count($point) > 0)
            {
                $point->earn_rate = 2;
                $point->save();

                $process = 1;
            }
        }

        if (in_array(date('N'), $weekend))
        {
            $point = PointType::find(PointType::BCARD);

            if (count($point) > 0)
            {
                $point->earn_rate = 5;
                $point->save();

                $process = 1;
            }
        }

        return $process;
    }

    public static function superSunday()
    {
        $process = 0;
        $date   = date('Ymd');

        // Sunday discount
        if ($date == "20160313")
        {
            $price1 = Price::find('9335');
            $price1->price_promo = 2.9;
            $price1->p_referral_fees = 0.4;
            $price1->timestamps = false;
            $price1->save();

            $price2 = Price::find('9336');
            $price2->price_promo = 17.9;
            $price2->p_referral_fees = 1;
            $price2->timestamps = false;
            $price2->save();

            $price3 = Price::find('8816');
            $price3->price_promo = 5.9;
            $price3->p_referral_fees = 0.19;
            $price3->timestamps = false;
            $price3->save();

            $price4 = Price::find('8911');
            $price4->price_promo = 26.9;
            $price4->p_referral_fees = 1.7;
            $price4->timestamps = false;
            $price4->save();

            $cat = Category::find('752');
            $cat->status = 1;
            $cat->timestamps = false;
            $cat->save();


            $process = 1;
        }

        // Monday revert back discount
        if ($date == "20160314")
        {
            $price1 = Price::find('9335');
            $price1->price_promo = 0;
            $price1->p_referral_fees = 0.8;
            $price1->timestamps = false;
            $price1->save();

            $price2 = Price::find('9336');
            $price2->price_promo = 0;
            $price2->p_referral_fees = 3;
            $price2->timestamps = false;
            $price2->save();

            $price3 = Price::find('8816');
            $price3->price_promo = 0;
            $price3->p_referral_fees = 0.89;
            $price3->timestamps = false;
            $price3->save();

            $price4 = Price::find('8911');
            $price4->price_promo = 0;
            $price4->p_referral_fees = 2.8;
            $price4->timestamps = false;
            $price4->save();

            $cat = Category::find('752');
            $cat->status = 0;
            $cat->timestamps = false;
            $cat->save();


            $process = 1;
        }

        return $process;
    }

    public static function refreshQuota()
    {
        $process = 0;
        $date   = date('Ymd');

        // first day of the month
        if (date('j') == 1)
        {
            DB::statement('UPDATE jocom_charity_product SET qty = quota where quota > 0');
            $process = 1;
        }

        return $process;
    }

    public static function activateGST()
    {
        $process = 0;
        $date   = date('Ymd');

        // first of March
        if (date('j') == 1 AND date('n') == 3)
        {
            // e37
            $e37 = Seller::find(69);
            $e37->gst_reg_num = '000569913344';
            $e37->non_gst = 0;
            $e37->modify_by = 'system';
            $e37->modify_date = date("Y-m-d H:i:s");
            $e37->timestamps = false;
            $e37->save();

            // mshopping
            $mshopping = Seller::find(75);
            $mshopping->gst_reg_num = '001077620736';
            $mshopping->non_gst = 0;
            $mshopping->modify_by = 'system';
            $mshopping->modify_date = date("Y-m-d H:i:s");
            $mshopping->timestamps = false;
            $mshopping->save();

            $fees = Fees::find(1);
            $fees->gst_status = 1;
            $fees->save();

            $process = 1;
        }

        return $process;
    }

    public static function activateJpoint()
    {
        $process = 0;
        $date   = date('Ymd');

        // first of April
        if (date('j') == 1 AND date('n') == 4)
        {
            $jpoint = PointType::find(1);
            $jpoint->status = 1;
            $jpoint->save();

            $process = 1;
        }

        return $process;
    }
}
