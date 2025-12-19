<?php

class ApiV2ConversionController extends BaseController
{
    /**
     * Get inbound conversion rate
     * @var    type  (string)  Cash / third party point type name
     * @var    point (int)     Cash / third party point amount (optional)
     * @return       (json)
     */
    public function getInboundrate()
    {
        $type   = Input::get('type');
        $point  = Input::get('point');
        $result = PointConversion::getInboundRate($type, $point);

        if ( ! is_array($result)) {
            $result = ['status' => 0, 'message' => $result];
        } else {
            $result = array_merge($result, ['status' => 1, 'message' => '#1901']);
        }

        return json_encode($result);
    }

    /**
     * Get outbound conversion rate
     * @var    type  (string)  Third party point type name
     * @var    point (int)     JoPoint amount (optional)
     * @return       (json)
     */
    public function getOutboundrate()
    {
        $type   = Input::get('type');
        $point  = Input::get('point');
        $result = PointConversion::getOutboundRate($type, $point);

        if ( ! is_array($result)) {
            $result = ['status' => 0, 'message' => $result];
        } else {
            $result = array_merge($result, ['status' => 1, 'message' => '#1901']);
        }

        return json_encode($result);
    }

    /**
     * Convert JoPoint to other point
     * @var    username      (string)  Username
     * @var    type          (string)  Third party point type name
     * @var    identifier    (string)  Third party point identifier
     * @var    point         (int)     JoPoint amount
     * @return               (json)
     */
    public function postOutbound()
    {
        $username   = Input::get('username');
        $password   = Input::get('password');
        $type       = Input::get('type');
        $identifier = Input::get('identifier');
        $point      = Input::get('point');

        $result = PointConversion::outbound($username, $password, $type, $identifier, $point);

        return json_encode($result);
    }
}
