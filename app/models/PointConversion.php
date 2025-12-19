<?php

class PointConversion extends Eloquent
{
    public function getInboundRate($type, $point = null)
    {
        $typeId = PointConversion::getTypeId($type);

        if ( ! $typeId) {
            return '#1902';
        }

        $rate = PointConversionRate::from($typeId)->to(PointType::JOPOINT)->active()->first();

        if ( ! $rate) {
            return '#1902';
        }

        $result = [
            'rate'    => (double) $rate->rate,
            'charges' => (double) $rate->charges,
            'minimum' => $rate->minimum,
        ];

        if (is_numeric($point)) {
            if ($point >= $rate->minimum) {
                return array_merge($result, [
                    'point' => floor($point * ($rate->rate - $rate->charges)),
                ]);
            } else {
                return '#1902';
            }
        } elseif ( ! empty($point)) {
            return '#1902';
        } else {
            return $result;
        }
    }

    /**
     * @param $typeId        (int)        Third party point type ID
     * @param $identifier    (string)     Third party point identifier
     * @param $transactionId (string)     Transaction ID
     * @param $amount        (decimal)    Third party point value
     * @param $point         (int)        Third party point amount
     * @return               (array/null) Return array (success) or null (failed)
     */
    public function getOutboundConfig($typeId, $identifier, $transactionId, $amount, $point)
    {
        $config = null;

        switch ($typeId) {
            case PointType::BCARD:
                $config = array_merge(Config::get('points.bcard'), [
                    'Card'        => $identifier,
                    'TranxDate'   => date('Y-m-d\TH:i:s'),
                    'BillNo'      => $transactionId,
                    'TotalAmount' => $amount,
                    'TotalPoint'  => $point,
                ]);
                break;
        }

        return $config;
    }

    /**
     * Get outbound conversion rate
     * @param  type  (string)       Third party point type name
     * @param  point (int)          JoPoint amount (optional)
     * @return       (array/string) Return array (success) or error message (failed)
     */
    public function getOutboundRate($type, $point = null)
    {
        $typeId = PointConversion::getTypeId($type);

        if ( ! $typeId) {
            return '#1902';
        }

        $rate = PointConversionRate::from(PointType::JOPOINT)->to($typeId)->active()->first();

        if ( ! $rate) {
            return '#1902';
        }

        $result = [
            'rate'    => (double) $rate->rate,
            'charges' => (double) $rate->charges,
            'minimum' => $rate->minimum,
        ];

        if (is_numeric($point)) {
            if ($point >= $rate->minimum) {
                return array_merge($result, [
                    'point' => floor($point * ($rate->rate - $rate->charges)),
                ]);
            } else {
                return '#1902';
            }
        } elseif ( ! empty($point)) {
            return '#1902';
        } else {
            return $result;
        }
    }

    /**
     * Convert JoPoint to other point
     * @param username      (string)       Username
     * @param password      (string)       Password
     * @param type          (string)       Third party point type name
     * @param identifier    (string)       Third party point identifier
     * @param joPoint       (int)          JoPoint amount
     * @return              (array/string) Return array (success) or error message (failed)
     */
    public function outbound($username, $password, $type, $identifier, $joPoint)
    {
        if ($password !== false) {
            if (Customer::check_login($username, $password) != 'yes') {
                return '#1902';
            }
        }

        $joPointUser = PointUser::username($username)
            ->pointType(PointType::JOPOINT)
            ->active()
            ->first();

        if ( ! $joPointUser) {
            return '#1902';
        }

        if ($joPointUser->point < $joPoint) {
            return '#1902';
        }

        $typeId = PointConversion::getTypeId($type);

        if ( ! $typeId) {
            return '#1902';
        }

        $rateResult = PointConversion::getOutboundRate($type, $joPoint);

        if ( ! is_array($rateResult)) {
            return $rateResult;
        }

        $rate              = array_get($rateResult, 'rate');
        $charges           = array_get($rateResult, 'charges');
        $thirdPartyPoint   = array_get($rateResult, 'point');
        $joPointType       = PointType::findOrFail(PointType::JOPOINT);
        $joPointEarnRate   = $joPointType->earn_rate;
        $joPointRedeemRate = $joPointType->redeem_rate;
        $joPointFromCash   = $joPoint / $joPointEarnRate;
        $amount            = $joPointFromCash * (1 - $charges);
        $conversionId      = PointConversion::insertGetId([
            'point_user_id' => $joPointUser->id,
            'type_from'     => PointType::JOPOINT,
            'type_to'       => $typeId,
            'point_from'    => $joPoint,
            'point_to'      => $thirdPartyPoint,
            'rate'          => $rate,
            'charges'       => $charges,
            'status'        => 0,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        $config   = PointConversion::getOutboundConfig($typeId, $identifier, 'C'.$conversionId, $amount, $thirdPartyPoint);
        $response = Bcard::api('Reward', $config);

        if (is_object($response) && object_get($response, 'status', 0) > 0) {
            $bcardTransaction            = new BcardTransaction;
            $bcardTransaction->action    = 'Convert';
            $bcardTransaction->api       = 'Reward';
            $bcardTransaction->point     = $thirdPartyPoint;
            $bcardTransaction->request   = json_encode($config);
            $bcardTransaction->response  = json_encode($response);
            $bcardTransaction->reward_id = object_get($response, 'RewardID');
            $bcardTransaction->bill_no   = 'C'.$conversionId;
            $bcardTransaction->save();
        }

        $result = json_decode(json_encode($response), true);

        if (is_array($result) && array_get($result, 'status') > 0) {
            $joPointUser->point -= $joPoint;
            $joPointUser->save();

            $conversion         = PointConversion::findOrFail($conversionId);
            $conversion->remark = json_encode($response);
            $conversion->status = 1;
            $conversion->save();

            PointTransaction::insert([
                'point_user_id'   => $joPointUser->id,
                'point'           => -($joPoint),
                'rate'            => $joPointRedeemRate,
                'balance'         => $joPointUser->point,
                'remark'          => "Conversion ID: {$conversionId}",
                'point_action_id' => PointAction::CONVERT,
                'code'            => hash('sha256', "{$joPointUser->id}|".date('Y-m-d H:i:s')."|".-($joPoint)."|{$joPointUser->point}"),
                'created_at'      => date('Y-m-d H:i:s'),
            ]);
        } else {
            $conversion         = PointConversion::findOrFail($conversionId);
            $conversion->remark = $response;
            $conversion->status = 2;
            $conversion->save();
        }

        return $result;
    }

    protected function getTypeId($type)
    {
        $typeId = null;

        switch (strtolower($type)) {
            case 'bcard':
                $typeId = PointType::BCARD;
                break;
            case 'cash':
                $typeId = PointType::CASH;
                break;
        }

        return $typeId;
    }
}
