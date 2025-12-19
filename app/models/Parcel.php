<?php

class Parcel extends Eloquent
{
    const Select = "";
    const Sending     = "Sending";
    const Received     = "Received";
    const Returned     = "Returned";
    const Processing     = "Processing";

    public $timestamps = false;

    public static function getArray()
	{
		return $options = array(Parcel::Select=> Parcel::Select, Parcel::Sending => Parcel::Sending, Parcel::Received => Parcel::Received, Parcel::Returned => Parcel::Returned, Parcel::Processing => Parcel::Processing);

	}
}

