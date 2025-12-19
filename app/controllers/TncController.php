<?php 

class TncController extends BaseController {

    
    /**
     * Default index for apps feed
     * @return [type] [description]
     */
    public function anyIndex()
    {
        
            return View::make('checkout.tnc');
    }

}
?>