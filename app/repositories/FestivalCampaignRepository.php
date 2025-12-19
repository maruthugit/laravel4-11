<?php

 namespace JocomRepo;
 use DB;
 use Auth;
 use Image;
 use FestivalCampaign;

/**
 * FestivalCampaignRepository
 */
class FestivalCampaignRepository implements CampaignInterface
{
    /*
    * Get all active festivals
    */
    public function getAll()
    {
        return FestivalCampaign::where('status', 1)->get(['id', 'title', 'effect', 'qrcode', 'status']);
    }

    //  Fetch latest active campaign
    public function fetchLatest()
    {
        // return FestivalCampaign::where('status', 1)->latest()->first(['id', 'title', 'effect']);
        $festivals = FestivalCampaign::where('status', 1)->latest()->first(['id', 'title', 'effect', 'from_date', 'end_date']);
        $today = \Carbon\Carbon::now();
        $start = $festivals->from_date;
        $end = $festivals->end_date;
        
        if ($today >= $start && $today <= $end) {
            return $festivals;
        } else {
            return null;
        }
    }

    /*
    * Show festival
    * param $id
    */
    public function find(int $id)
    {
        $campaign = FestivalCampaign::where(['id' => $id])->orderBy('created_at', 'desc')
                                    ->first(['id', 'title', 'description_1', 'description_2', 'greeting_message',
                                            'from_date', 'end_date', 'floating_image', 'featured_image', 'cover_image',
                                            'color_primary', 'color_primary_dark', 'color_accent', 'color_text', 'effect', 'related_effect',
                                            'show_frequent_status as frequently', 'qrcode', 'status']);

        $campaign->floating_image = (!empty($campaign->floating_image) ? url('/campaigns/thumbs').'/'.$campaign->floating_image : "");
        $campaign->featured_image = (!empty($campaign->featured_image) ? url('/campaigns/thumbs').'/'.$campaign->featured_image : "");
        $campaign->cover_image = (!empty($campaign->cover_image) ? url('/campaigns/thumbs').'/'.$campaign->cover_image : "");
        $items = explode(',', $campaign->qrcode);
        $items_data = [];

        foreach ( $items as $k => $value ) {
            for ( $i = $k; $i <= 7; $i++ ) {
                $items_data[$k] = DB::table('jocom_products as jp')->where('qrcode', '=', $value)
                                    ->leftJoin('jocom_product_price as jpp', 'jp.id', '=', 'jpp.product_id')
                                    ->leftJoin('jocom_comments as jc', 'jp.id', '=', 'jc.product_id')
                                    ->first(['jp.sku', 'jp.name', 'jp.img_1', 'jp.qrcode', 'jc.rating as overall_rating', DB::raw('(CASE WHEN jpp.price_promo = 0 THEN jpp.price ELSE jpp.price_promo END) AS price')]);
                if ( !empty($items_data[$k]->img_1) ) {
                    $items_data[$k]->img_1 = url("images/data")."/".$items_data[$k]->img_1;
                }
            }
        }

        if ($campaign->related_effect == 1)
            $campaign->related_effect = true;

        if ($campaign->related_effect == 0)
            $campaign->related_effect = false;

        $campaign->items = $items_data;

        return $campaign;
    }

    public function create(array $params)
    {
        $festival = $this->execSaveUpdate($params);
        return $festival->save();
    }

    public function update(array $params, int $id)
    {
        $festival = $this->execSaveUpdate($params, $id);
        return $festival->save();
    }

    private function execSaveUpdate($params, $id="")
    {
        $festival = new FestivalCampaign;
        if (!empty($id)) $festival = FestivalCampaign::find($id);
        
        $format = 'Y-m-d H:i';
        $start = \DateTime::createFromFormat($format, $params['valid_from']);
        $end = \DateTime::createFromFormat($format, $params['valid_to']);
        $startDate = $start->format('Y-m-d H:i:s');
        $endDate = $end->format('Y-m-d H:i:s');

        $festival->title = $params['title'];
        $festival->description_1 = $params['description_1'];
        $festival->description_2 = $params['description_2'];
        $festival->greeting_message = $params['greet_txt'];
        $festival->from_date = $startDate;
        $festival->end_date = $endDate;
        $festival->color_primary = $params['color_primary'];
        $festival->color_primary_dark = $params['color_primary_dark'];
        $festival->color_accent = $params['color_accent'];
        $festival->color_text = $params['color_text'];
        $festival->effect = $params['effect_type'];
        $festival->related_effect = $params['related_effect'];
        $festival->show_frequent_status = $params['frequent_status'];
        $festival->qrcode = $params['items'];
        $festival->status = $params['status'];
        $festival->created_by = Auth::id();

         // Image Compressions
        $files =[];
        if (!empty($params['floating_img'])) $files['float'] = $params['floating_img'];
        if (!empty($params['featured_img'])) $files['feature'] = $params['featured_img'];
        if (!empty($params['cover_img'])) $files['cover'] = $params['cover_img'];

        $uploadedImages = $this->execImages($files);
        if (!empty($uploadedImages['float'])) $festival->floating_image = $uploadedImages['float'];
        if (!empty($uploadedImages['feature'])) $festival->featured_image = $uploadedImages['feature'];
        if (!empty($uploadedImages['cover'])) $festival->cover_image = $uploadedImages['cover'];

        return $festival;
    }

    private function execImages($files)
    {
        $fileNames = [];
        $imagePath = public_path().'/campaigns/thumbs/';
        foreach ($files as $key => $file) {
            $extension = $file->getClientOriginalExtension();
            if($key == 'float') {
                $fileName = 'JC-FL-' .uniqid() . '-' . date('dmy') . '.' . $extension;
                Image::make($file->getRealPath())->resize(350, 350)->save($imagePath.$fileName);
                $fileNames['float'] = $fileName;
            }
            else if ($key == 'feature') {
                $fileName = 'JC-FE-' .uniqid() . '-' . date('dmy') . '.' . $extension;
                Image::make($file->getRealPath())->resize(512, 512)->save($imagePath.$fileName);
                $fileNames['feature'] = $fileName;
            }
            else {
                $fileName = 'JC-CV-' .uniqid() . '-' . date('dmy') . '.' . $extension;
                Image::make($file->getRealPath())->resize(512, 512)->save($imagePath.$fileName);
                $fileNames['cover'] = $fileName;
            }
        }
        return $fileNames;
    }
}
