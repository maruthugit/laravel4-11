<?php

class PointAction extends Eloquent
{
    const EARN     = 1;
    const REDEEM   = 2;
    const CONVERT  = 3;
    const CASH_BUY = 4;
    const CASH_OUT = 5;
    const REVERSAL = 6;
    const REFUND   = 7;

    public $timestamps = false;
}
