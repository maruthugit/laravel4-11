<?php

class BannerTemplateController extends BaseController {

    public function __construct()
    {
        
        $this->beforeFilter('auth');
        // echo "<br>check authentication ";
    }

    /**
     * Display a listing of the banner.
     *
     * @return Response
     */

    public function anyLayout(){

        $regions = DB::table('jocom_region')->select('*')->where('status',1)->get();

        return View::make('bannertemplate.banner_layout')->with('regions',$regions);

    }

    public function anyLayoutupdate(){

            $region_id = Input::get('region_id');
            $type = Input::get('type');
            $list = array_chunk($type, 2);

            foreach ($list as $key => $value) {
                $seq = $value[0];
                $type2= $value[1];
                if (!empty($seq)) {

                    $id = DB::table('jocom_managebanners_new')
                    ->insertGetId(array(
                        'type'=>$type2, 
                        'seq'=>$seq, 
                        'region_id'=>$region_id,
                        'device'=>'all',
                        'insert_by'=>Session::get('username'),
                        'insert_date'=>date('Y-m-d H:i:s'),
                        'active_status'=>0,
                    ));  
                }else{

                    $id = DB::table('jocom_managebanners_new')
                    ->insertGetId(array(
                        'type'=>$type2, 
                        'seq'=>'1', 
                        'region_id'=>$region_id,
                        'device'=>'all',
                        'insert_by'=>Session::get('username'),
                        'insert_date'=>date('Y-m-d H:i:s'),
                        'active_status'=>0,
                    ));  
                }

                $result   = BannerTemplate::scopeLayoutUpdate($id,$type2);  

            }
            return $result;
        
    }

    public function anyLayoutdelete($id)
    {
        DB::table('jocom_managebanners_new')->where('id',$id)->delete();

        DB::table('jocom_managebanners_images_new')->where('banner_id', $id)->delete();
        
        Session::flash('message', 'Successfully deleted.');
        return Redirect::back();
    }

    public function anyTemplate(){

        $region1  = BannerTemplate::scopeTemplateList(1);
        $region2  = BannerTemplate::scopeTemplateList(2);
        $region3  = BannerTemplate::scopeTemplateList(3);
        $region4  = BannerTemplate::scopeTemplateList(4);
        $region5  = BannerTemplate::scopeTemplateList(5);

        $status  = array('0' => 'Inactive', '1'=> 'Active');

        return View::make('bannertemplate.banner_template')
                    ->with('status', $status)
                    ->with('region1', $region1)
                    ->with('region2', $region2)
                    ->with('region3', $region3)
                    ->with('region4', $region4)
                    ->with('region5', $region5);
    }

    public function anyTemplateupdate(){
        //hq
        $B001_hq    = Input::get('B001_hq');  
        $bannerID1  = Input::get('bannerID1');    
        $seq1       = Input::get('seq1'); 
        $B002_hq    = Input::get('B002_hq');
        $bannerID2  = Input::get('bannerID2'); 
        $seq2       = Input::get('seq2');        
        $B003_hq    = Input::get('B003_hq'); 
        $bannerID3  = Input::get('bannerID3');  
        $seq3       = Input::get('seq3');      
        $B004_hq    = Input::get('B004_hq'); 
        $bannerID4  = Input::get('bannerID4'); 
        $seq4       = Input::get('seq4');   
        $B005_hq    = Input::get('B005_hq'); 
        $bannerID13 = Input::get('bannerID13'); 
        $seq13      = Input::get('seq13');         
        //jb
        $B001_jb    = Input::get('B001_jb'); 
        $bannerID5  = Input::get('bannerID5');  
        $seq5       = Input::get('seq5');      
        $B002_jb    = Input::get('B002_jb');
        $bannerID6  = Input::get('bannerID6'); 
        $seq6       = Input::get('seq6');       
        $B003_jb    = Input::get('B003_jb');
        $bannerID7  = Input::get('bannerID7'); 
        $seq7       = Input::get('seq7');       
        $B004_jb    = Input::get('B004_jb');  
        $bannerID8  = Input::get('bannerID8'); 
        $seq8       = Input::get('seq8');   
        $B005_jb    = Input::get('B005_jb');  
        $bannerID14 = Input::get('bannerID14'); 
        $seq14      = Input::get('seq14');   
        //png
        $B001_png   = Input::get('B001_png');  
        $bannerID9  = Input::get('bannerID9');  
        $seq9       = Input::get('seq9');     
        $B002_png   = Input::get('B002_png'); 
        $bannerID10 = Input::get('bannerID10');    
        $seq10      = Input::get('seq10');    
        $B003_png   = Input::get('B003_png');
        $bannerID11 = Input::get('bannerID11'); 
        $seq11      = Input::get('seq11');        
        $B004_png   = Input::get('B004_png');  
        $bannerID12 = Input::get('bannerID12');     
        $seq12      = Input::get('seq12');  
        $B005_png   = Input::get('B005_png');  
        $bannerID15 = Input::get('bannerID15'); 
        $seq15      = Input::get('seq15');   

        //CHINA
        $B001_chq   = Input::get('B001_chq');  
        $bannerID16  = Input::get('bannerID16');  
        $seq16       = Input::get('seq16');     
        $B002_chq   = Input::get('B002_chq'); 
        $bannerID17 = Input::get('bannerID17');    
        $seq17      = Input::get('seq17');    
        $B003_chq   = Input::get('B003_chq');
        $bannerID18 = Input::get('bannerID18'); 
        $seq18      = Input::get('seq18');        
        $B004_chq   = Input::get('B004_chq');  
        $bannerID19 = Input::get('bannerID19');     
        $seq19      = Input::get('seq19');  
        $B005_chq   = Input::get('B005_chq');  
        $bannerID20 = Input::get('bannerID20'); 
        $seq20      = Input::get('seq20'); 

        //AUS
        $B001_azhq   = Input::get('B001_azhq');  
        $bannerID21  = Input::get('bannerID21');  
        $seq21       = Input::get('seq21');     
        $B002_azhq   = Input::get('B002_azhq'); 
        $bannerID22 = Input::get('bannerID22');    
        $seq22      = Input::get('seq22');    
        $B003_azhq   = Input::get('B003_azhq');
        $bannerID23 = Input::get('bannerID23'); 
        $seq23      = Input::get('seq23');        
        $B004_azhq   = Input::get('B004_azhq');  
        $bannerID24 = Input::get('bannerID24');     
        $seq24      = Input::get('seq24');  
        $B005_azhq   = Input::get('B005_azhq');  
        $bannerID25 = Input::get('bannerID25'); 
        $seq25      = Input::get('seq25'); 

        //hq
        $image1 = Input::file('image1');
        $image2 = Input::file('image2');
        $image3 = Input::file('image3');
        $image4 = Input::file('image4');
        $image5 = Input::file('image5');
        $image6 = Input::file('image6');
        $image7 = Input::file('image7');
        $image8 = Input::file('image8');
        $image9 = Input::file('image9');
        $image10 = Input::file('image10');
        $image11 = Input::file('image11');
        $image12 = Input::file('image12');
        $image37 = Input::file('image37');
        //jb
        $image13 = Input::file('image13');
        $image14 = Input::file('image14');
        $image15 = Input::file('image15');
        $image16 = Input::file('image16');
        $image17 = Input::file('image17');
        $image18 = Input::file('image18');
        $image19 = Input::file('image19');
        $image20 = Input::file('image20');
        $image21 = Input::file('image21');
        $image22 = Input::file('image22');
        $image23 = Input::file('image23');
        $image24 = Input::file('image24');
        $image38 = Input::file('image38');
        //png
        $image25 = Input::file('image25');
        $image26 = Input::file('image26');
        $image27 = Input::file('image27');
        $image28 = Input::file('image28');
        $image29 = Input::file('image29');
        $image30 = Input::file('image30');
        $image31 = Input::file('image31');
        $image32 = Input::file('image32');
        $image33 = Input::file('image33');
        $image34 = Input::file('image34');
        $image35 = Input::file('image35');
        $image36 = Input::file('image36');    
        $image39 = Input::file('image39');
        //china
        $image40 = Input::file('image40');
        $image41 = Input::file('image41');
        $image42 = Input::file('image42');
        $image43 = Input::file('image43');
        $image44 = Input::file('image44');
        $image45 = Input::file('image45');
        $image46 = Input::file('image46');
        $image47 = Input::file('image47');
        $image48 = Input::file('image48');
        $image49 = Input::file('image49');
        $image50 = Input::file('image50');
        $image51 = Input::file('image51');    
        $image52 = Input::file('image52');

        //Australia

        $image53 = Input::file('image53');
        $image54 = Input::file('image54');
        $image55 = Input::file('image55');
        $image56 = Input::file('image56');
        $image57 = Input::file('image57');
        $image58 = Input::file('image58');
        $image59 = Input::file('image59');
        $image60 = Input::file('image60');
        $image61 = Input::file('image61');
        $image62 = Input::file('image62');
        $image63 = Input::file('image63');
        $image64 = Input::file('image64');    
        $image65 = Input::file('image65');


        //hq
        $qrcode1 = Input::get('qrcode1');
        $id1     =  Input::get('id1');
        $qrcode2 = Input::get('qrcode2'); 
        $id2     =  Input::get('id2');       
        $qrcode3 = Input::get('qrcode3');  
        $id3     =  Input::get('id3');      
        $qrcode4 = Input::get('qrcode4');   
        $id4     =  Input::get('id4');    
        $qrcode5 = Input::get('qrcode5'); 
        $id5     =  Input::get('id5');      
        $qrcode6 = Input::get('qrcode6');  
        $id6     =  Input::get('id6');      
        $qrcode7 = Input::get('qrcode7'); 
        $id7     =  Input::get('id7');        
        $qrcode8 = Input::get('qrcode8');
        $id8     =  Input::get('id8');        
        $qrcode9 = Input::get('qrcode9');  
        $id9     =  Input::get('id9');      
        $qrcode10 = Input::get('qrcode10');  
        $id10     =  Input::get('id10');      
        $qrcode11 = Input::get('qrcode11');  
        $id11     =  Input::get('id11');       
        $qrcode12 = Input::get('qrcode12');  
        $id12     =  Input::get('id12');   
        $qrcode37 = Input::get('qrcode37'); 
        $id37     =  Input::get('id37');     
        //jb
        $qrcode13 = Input::get('qrcode13');  
        $id13     =  Input::get('id13');      
        $qrcode14 = Input::get('qrcode14');
        $id14     =  Input::get('id14');       
        $qrcode15 = Input::get('qrcode15');
        $id15     =  Input::get('id15');        
        $qrcode16 = Input::get('qrcode16');
        $id16     =  Input::get('id16');        
        $qrcode17 = Input::get('qrcode17');
        $id17     =  Input::get('id17');        
        $qrcode18 = Input::get('qrcode18');
        $id18     =  Input::get('id18');        
        $qrcode19 = Input::get('qrcode19'); 
        $id19     =  Input::get('id19');       
        $qrcode20 = Input::get('qrcode20'); 
        $id20     =  Input::get('id20');       
        $qrcode21 = Input::get('qrcode21');  
        $id21     =  Input::get('id21');      
        $qrcode22 = Input::get('qrcode22');  
        $id22     =  Input::get('id22');      
        $qrcode23 = Input::get('qrcode23');  
        $id23     =  Input::get('id23');     
        $qrcode24 = Input::get('qrcode24');   
        $id24     =  Input::get('id24'); 
        $qrcode38 = Input::get('qrcode38'); 
        $id38     =  Input::get('id38');     
        //png
        $qrcode25 = Input::get('qrcode25');  
        $id25     =  Input::get('id25');      
        $qrcode26 = Input::get('qrcode26');   
        $id26     =  Input::get('id26');     
        $qrcode27 = Input::get('qrcode27');  
        $id27     =  Input::get('id27');      
        $qrcode28 = Input::get('qrcode28'); 
        $id28     =  Input::get('id28');       
        $qrcode29 = Input::get('qrcode29');  
        $id29     =  Input::get('id29');      
        $qrcode30 = Input::get('qrcode30');    
        $id30     =  Input::get('id30');    
        $qrcode31 = Input::get('qrcode31');   
        $id31     =  Input::get('id31');     
        $qrcode32 = Input::get('qrcode32'); 
        $id32     =  Input::get('id32');       
        $qrcode33 = Input::get('qrcode33');  
        $id33     =  Input::get('id33');      
        $qrcode34 = Input::get('qrcode34'); 
        $id34     =  Input::get('id34');       
        $qrcode35 = Input::get('qrcode35');  
        $id35     =  Input::get('id35');      
        $qrcode36 = Input::get('qrcode36'); 
        $id36     =  Input::get('id36');     
        $qrcode39 = Input::get('qrcode39'); 
        $id39     =  Input::get('id39');  
        //china
        $qrcode40 = Input::get('qrcode40');  
        $id40     =  Input::get('id40');      
        $qrcode41 = Input::get('qrcode41');   
        $id41     =  Input::get('id41');     
        $qrcode42 = Input::get('qrcode42');  
        $id42     =  Input::get('id42');      
        $qrcode43 = Input::get('qrcode43'); 
        $id43     =  Input::get('id43');       
        $qrcode44 = Input::get('qrcode44');  
        $id44     =  Input::get('id44');      
        $qrcode45 = Input::get('qrcode45');    
        $id45     =  Input::get('id45');    
        $qrcode46 = Input::get('qrcode46');   
        $id46     =  Input::get('id46');     
        $qrcode47 = Input::get('qrcode47'); 
        $id47     =  Input::get('id47');       
        $qrcode48 = Input::get('qrcode48');  
        $id48     =  Input::get('id48');      
        $qrcode49 = Input::get('qrcode49'); 
        $id49     =  Input::get('id49');       
        $qrcode50 = Input::get('qrcode50');  
        $id50     =  Input::get('id50');      
        $qrcode51 = Input::get('qrcode51'); 
        $id51     =  Input::get('id51');     
        $qrcode52 = Input::get('qrcode52'); 
        $id52     =  Input::get('id52');      

        //Australia
        $qrcode53 = Input::get('qrcode53');  
        $id53     =  Input::get('id53');      
        $qrcode54 = Input::get('qrcode54');   
        $id54     =  Input::get('id54');     
        $qrcode55 = Input::get('qrcode55');  
        $id55     =  Input::get('id55');      
        $qrcode56 = Input::get('qrcode56'); 
        $id56     =  Input::get('id56');       
        $qrcode57 = Input::get('qrcode57');  
        $id57     =  Input::get('id57');      
        $qrcode58 = Input::get('qrcode58');    
        $id58     =  Input::get('id58');    
        $qrcode59 = Input::get('qrcode59');   
        $id59     =  Input::get('id59');     
        $qrcode60 = Input::get('qrcode60'); 
        $id60     =  Input::get('id60');       
        $qrcode61 = Input::get('qrcode61');  
        $id61     =  Input::get('id61');      
        $qrcode62 = Input::get('qrcode62'); 
        $id62     =  Input::get('id62');       
        $qrcode63 = Input::get('qrcode63');  
        $id63     =  Input::get('id63');      
        $qrcode64 = Input::get('qrcode64'); 
        $id64     =  Input::get('id64');     
        $qrcode65 = Input::get('qrcode65'); 
        $id65     =  Input::get('id65');   



        $unique = time();
        $path = Config::get('constants.NEW_BANNER_FILE_PATH');

        //STATUS
        if ($B001_hq != '') {
            foreach($B001_hq as $indx => $value) {
                $status = $B001_hq[$indx];
                $id = $bannerID1[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);    
            } 
        }

        if ($seq1 != '') {
            foreach($seq1 as $indx => $value) {
                $seq = $seq1[$indx];
                $id = $bannerID1[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }

        if ($B002_hq != '') {
            foreach($B002_hq as $indx => $value) {
                $status = $B002_hq[$indx];
                $id = $bannerID2[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }

        if ($seq2 != '') {
            foreach($seq2 as $indx => $value) {
                $seq = $seq2[$indx];
                $id = $bannerID2[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }

        if ($B003_hq != '') {
            foreach($B003_hq as $indx => $value) {
                $status = $B003_hq[$indx];
                $id = $bannerID3[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);    
            } 
        }
        if ($seq3 != '') {
            foreach($seq3 as $indx => $value) {
                $seq = $seq3[$indx];
                $id = $bannerID3[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }
        if ($B004_hq != '') { 
            foreach($B004_hq as $indx => $value) {
                $status = $B004_hq[$indx];
                $id = $bannerID4[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq4 != '') {
            foreach($seq4 as $indx => $value) {
                $seq = $seq4[$indx];
                $id = $bannerID4[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }
        if ($B001_jb != '') {  
            foreach($B001_jb as $indx => $value) {
                $status = $B001_jb[$indx];
                $id = $bannerID5[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq5 != '') {
            foreach($seq5 as $indx => $value) {
                $seq = $seq5[$indx];
                $id = $bannerID5[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }
        if ($B002_jb != '') { 
            foreach($B002_jb as $indx => $value) {
                $status = $B002_jb[$indx];
                $id = $bannerID6[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq6 != '') {
            foreach($seq6 as $indx => $value) {
                $seq = $seq6[$indx];
                $id = $bannerID6[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }
        if ($B003_jb != '') {   
            foreach($B003_jb as $indx => $value) {
                $status = $B003_jb[$indx];
                $id = $bannerID7[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq7 != '') {
            foreach($seq7 as $indx => $value) {
                $seq = $seq7[$indx];
                $id = $bannerID7[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }
        if ($B004_jb != '') { 
            foreach($B004_jb as $indx => $value) {
                $status = $B004_jb[$indx];
                $id = $bannerID8[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq8 != '') {
            foreach($seq8 as $indx => $value) {
                $seq = $seq8[$indx];
                $id = $bannerID8[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }
        if ($B001_png != '') {  
            foreach($B001_png as $indx => $value) {
                $status = $B001_png[$indx];
                $id = $bannerID9[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);  
            } 
        }
        if ($seq9 != '') {
            foreach($seq9 as $indx => $value) {
                $seq = $seq9[$indx];
                $id = $bannerID9[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }
        if ($B002_png != '') {
            foreach($B002_png as $indx => $value) {
                $status = $B002_png[$indx];
                $id = $bannerID10[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq10 != '') {
            foreach($seq10 as $indx => $value) {
                $seq = $seq10[$indx];
                $id = $bannerID10[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }
        if ($B003_png != '') {
            foreach($B003_png as $indx => $value) {
                $status = $B003_png[$indx];
                $id = $bannerID11[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq11 != '') {
            foreach($seq11 as $indx => $value) {
                $seq = $seq11[$indx];
                $id = $bannerID11[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }
        if ($B004_png != '') {  
            foreach($B004_png as $indx => $value) {
                $status = $B004_png[$indx];
                $id = $bannerID12[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq12 != '') {
            foreach($seq12 as $indx => $value) {
                $seq = $seq12[$indx];
                $id = $bannerID12[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }

        if ($B005_hq != '') {  
            foreach($B005_hq as $indx => $value) {
                $status = $B005_hq[$indx];
                $id = $bannerID13[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq13 != '') {
            foreach($seq13 as $indx => $value) {
                $seq = $seq13[$indx];
                $id = $bannerID13[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }

        if ($B005_jb != '') {  
            foreach($B005_jb as $indx => $value) {
                $status = $B005_jb[$indx];
                $id = $bannerID14[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq14 != '') {
            foreach($seq14 as $indx => $value) {
                $seq = $seq14[$indx];
                $id = $bannerID14[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }

        if ($B005_png != '') {  
            foreach($B005_png as $indx => $value) {
                $status = $B005_png[$indx];
                $id = $bannerID15[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq15 != '') {
            foreach($seq15 as $indx => $value) {
                $seq = $seq15[$indx];
                $id = $bannerID15[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }

        if ($B001_chq != '') {  
            foreach($B001_chq as $indx => $value) {
                $status = $B001_chq[$indx];
                $id = $bannerID16[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);  
            } 
        }
        if ($seq16 != '') {
            foreach($seq16 as $indx => $value) {
                $seq = $seq16[$indx];
                $id = $bannerID16[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }
        if ($B002_chq != '') {
            foreach($B002_chq as $indx => $value) {
                $status = $B002_chq[$indx];
                $id = $bannerID17[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq17 != '') {
            foreach($seq17 as $indx => $value) {
                $seq = $seq17[$indx];
                $id = $bannerID17[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }
        if ($B003_chq != '') {
            foreach($B003_chq as $indx => $value) {
                $status = $B003_chq[$indx];
                $id = $bannerID18[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq18 != '') {
            foreach($seq18 as $indx => $value) {
                $seq = $seq18[$indx];
                $id = $bannerID18[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }
        if ($B004_chq != '') {  
            foreach($B004_chq as $indx => $value) {
                $status = $B004_chq[$indx];
                $id = $bannerID19[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq19 != '') {
            foreach($seq19 as $indx => $value) {
                $seq = $seq19[$indx];
                $id = $bannerID19[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }
        if ($B005_chq != '') {  
            foreach($B005_chq as $indx => $value) {
                $status = $B005_chq[$indx];
                $id = $bannerID20[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq20 != '') {
            foreach($seq20 as $indx => $value) {
                $seq = $seq20[$indx];
                $id = $bannerID20[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }

        //AUS
        if ($B001_azhq != '') {  
            foreach($B001_azhq as $indx => $value) {
                $status = $B001_azhq[$indx];
                $id = $bannerID21[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);  
            } 
        }
        if ($seq21 != '') {
            foreach($seq21 as $indx => $value) {
                $seq = $seq21[$indx];
                $id = $bannerID21[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }
        if ($B002_azhq != '') {
            foreach($B002_azhq as $indx => $value) {
                $status = $B002_azhq[$indx];
                $id = $bannerID22[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq22 != '') {
            foreach($seq22 as $indx => $value) {
                $seq = $seq22[$indx];
                $id = $bannerID22[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }
        if ($B003_azhq != '') {
            foreach($B003_azhq as $indx => $value) {
                $status = $B003_azhq[$indx];
                $id = $bannerID23[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq23 != '') {
            foreach($seq23 as $indx => $value) {
                $seq = $seq23[$indx];
                $id = $bannerID23[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }
        if ($B004_azhq != '') {  
            foreach($B004_azhq as $indx => $value) {
                $status = $B004_azhq[$indx];
                $id = $bannerID24[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq24 != '') {
            foreach($seq24 as $indx => $value) {
                $seq = $seq24[$indx];
                $id = $bannerID24[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }
        if ($B005_azhq != '') {  
            foreach($B005_azhq as $indx => $value) {
                $status = $B005_azhq[$indx];
                $id = $bannerID25[$indx];
                BannerTemplate::scopeUpdateTemplateStatus($status,$id);     
            } 
        }
        if ($seq25 != '') {
            foreach($seq25 as $indx => $value) {
                $seq = $seq25[$indx];
                $id = $bannerID25[$indx];
                BannerTemplate::scopeUpdateTemplateSeq($seq,$id);    
            } 
        }

        //STATUS

        //IMAGE & QRCDOE HQ

        if (!empty($image1)) {
            foreach($image1 as $indx => $value) {
                $image = $image1[$indx];
                if (!empty($image)) {
                    $id = $id1[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }                   
            } 
        }
        if ($qrcode1!='') {
            foreach($qrcode1 as $indx => $value) {
                $qrcode = $qrcode1[$indx];
                $id = $id1[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            }                       
        }
        if (!empty($image2)) {           
            foreach($image2 as $indx => $value) {
                $image = $image2[$indx];
                if (!empty($image)) {
                    $id = $id2[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);                
                }   
            } 
        }
        if ($qrcode2!='') {
            foreach($qrcode2 as $indx => $value) {
                $qrcode = $qrcode2[$indx];
                $id = $id2[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            }
        }
        if (!empty($image3)) {
            foreach($image3 as $indx => $value) {
                $image = $image3[$indx];
                if (!empty($image)) {
                    $id = $id3[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode3!='') {
            foreach($qrcode3 as $indx => $value) {
                $qrcode = $qrcode3[$indx];
                $id = $id3[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            }
        }
        if (!empty($image4)) {
            foreach($image4 as $indx => $value) {
                $image = $image4[$indx];
                if (!empty($image)) {
                    $id = $id4[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode4!='') {
            foreach($qrcode4 as $indx => $value) {
                $qrcode = $qrcode4[$indx];
                $id = $id4[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            }
        }
        if (!empty($image5)) {
            foreach($image5 as $indx => $value) {
                $image = $image5[$indx];
                if (!empty($image)) {
                    $id = $id5[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension(); 
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode5!='') {
            foreach($qrcode5 as $indx => $value) {
                $qrcode = $qrcode5[$indx];
                $id = $id5[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            }
        }
        if (!empty($image6)) {
            foreach($image6 as $indx => $value) {                
                $image = $image6[$indx];
                if (!empty($image)) {
                    $id = $id6[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode6!='') {
            foreach($qrcode6 as $indx => $value) {
                $qrcode = $qrcode6[$indx];
                $id = $id6[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            }
        }
        if (!empty($image7)) {
            foreach($image7 as $indx => $value) {
                $image = $image7[$indx];
                if (!empty($image)) {
                    $id = $id7[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode7!='') {  
            foreach($qrcode7 as $indx => $value) {
                $qrcode = $qrcode7[$indx];
                $id = $id7[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            }
        }
        if (!empty($image8)) {
            foreach($image8 as $indx => $value) {
                $image = $image8[$indx];
                if (!empty($image)) {
                    $id = $id8[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode8!='') {
            foreach($qrcode8 as $indx => $value) {
                $qrcode = $qrcode8[$indx];
                $id = $id8[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            }
        }
        if (!empty($image9)) {
            foreach($image9 as $indx => $value) {
                $image = $image9[$indx];
                if (!empty($image)) {
                    $id = $id9[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode9!='') {
            foreach($qrcode9 as $indx => $value) {
                $qrcode = $qrcode9[$indx];
                $id = $id9[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            }
        }
        if (!empty($image10)) {
            foreach($image10 as $indx => $value) {                
                $image = $image10[$indx];
                if (!empty($image)) {
                    $id = $id10[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode10!='') {
            foreach($qrcode10 as $indx => $value) {
                $qrcode = $qrcode10[$indx];
                $id = $id10[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            }
        }
        if (!empty($image11)) {
            foreach($image11 as $indx => $value) {
                $image = $image11[$indx];
                if (!empty($image)) {
                    $id = $id11[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode11!='') {
            foreach($qrcode11 as $indx => $value) {
                $qrcode = $qrcode11[$indx];
                $id = $id11[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            }
        }
        if (!empty($image12)) {
            foreach($image12 as $indx => $value) {
                $image = $image12[$indx];
                if (!empty($image)) {
                    $id = $id12[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }   
            } 
        }
        if ($qrcode12!='') {
            foreach($qrcode12 as $indx => $value) {
                $qrcode = $qrcode12[$indx];
                $id = $id12[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            }
        }
        // // END HQ
        if (!empty($image13)) {
            foreach($image13 as $indx => $value) {
                $image = $image13[$indx];
                if (!empty($image)) {
                    $id = $id13[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode13!='') {
            foreach($qrcode13 as $indx => $value) {
                $qrcode = $qrcode13[$indx];
                $id = $id13[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            }
        }
        if (!empty($image14)) {
            foreach($image14 as $indx => $value) {
                $image = $image14[$indx];
                if (!empty($image)) {
                    $id = $id14[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode14!='') {
            foreach($qrcode14 as $indx => $value) {
                $qrcode = $qrcode14[$indx];
                $id = $id14[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            }
        }
        if (!empty($image15)) {
            foreach($image15 as $indx => $value) {
                $image = $image15[$indx];
                if (!empty($image)) {
                    $id = $id15[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode15!='') {
            foreach($qrcode15 as $indx => $value) {
                $qrcode = $qrcode15[$indx];
                $id = $id15[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            }
        }

        if (!empty($image16)) {
            foreach($image16 as $indx => $value) {
                $image = $image16[$indx];
                if (!empty($image)) {
                    $id = $id16[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode16!='') {  
            foreach($qrcode16 as $indx => $value) {
                $qrcode = $qrcode16[$indx];
                $id = $id16[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            }           
        }
        if (!empty($image17)) {           
            foreach($image17 as $indx => $value) {
                $image = $image17[$indx];
                if (!empty($image)) {
                    $id = $id17[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode17!='') {
            foreach($qrcode17 as $indx => $value) {
                $qrcode = $qrcode17[$indx];
                $id = $id17[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }
        if (!empty($image18)) {
            foreach($image18 as $indx => $value) {
                $image = $image18[$indx];
                if (!empty($image)) {
                    $id = $id18[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode18!='') { 
            foreach($qrcode18 as $indx => $value) {
                $qrcode = $qrcode18[$indx];
                $id = $id18[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }
        if (!empty($image19)) {
            foreach($image19 as $indx => $value) {
                $image = $image19[$indx];
                if (!empty($image)) {
                    $id = $id19[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode19!='') {
            foreach($qrcode19 as $indx => $value) {
                $qrcode = $qrcode19[$indx];
                $id = $id19[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }
        if (!empty($image20)) {
            foreach($image20 as $indx => $value) {
                $image = $image20[$indx];
                if (!empty($image)) {
                    $id = $id20[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode20!='') { 
            foreach($qrcode20 as $indx => $value) {
                $qrcode = $qrcode20[$indx];
                $id = $id20[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }
        if (!empty($image21)) {
            foreach($image21 as $indx => $value) {
                $image = $image21[$indx];
                if (!empty($image)) {
                    $id = $id21[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode21!='') {
            foreach($qrcode21 as $indx => $value) {
                $qrcode = $qrcode21[$indx];
                $id = $id21[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }
        if (!empty($image22)) {
            foreach($image22 as $indx => $value) {
                $image = $image22[$indx];
                if (!empty($image)) {
                    $id = $id22[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode22!='') {  
            foreach($qrcode22 as $indx => $value) {
                $qrcode = $qrcode22[$indx];
                $id = $id22[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }
        if (!empty($image23)) {
            foreach($image23 as $indx => $value) {
                $image = $image23[$indx];
                if (!empty($image)) {
                    $id = $id23[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode23!='') {
            foreach($qrcode23 as $indx => $value) {
                $qrcode = $qrcode23[$indx];
                $id = $id23[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }
        if (!empty($image24)) {
            foreach($image24 as $indx => $value) {
                $image = $image24[$indx];
                if (!empty($image)) {
                    $id = $id24[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode24!='') {
            foreach($qrcode24 as $indx => $value) {
                $qrcode = $qrcode24[$indx];
                $id = $id24[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }
        if (!empty($image25)) {
            foreach($image25 as $indx => $value) {
                $image = $image25[$indx];
                if (!empty($image)) {
                    $id = $id25[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }

        if ($qrcode25!='') {
            foreach($qrcode25 as $indx => $value) {
                $qrcode = $qrcode25[$indx];
                $id = $id25[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }
        if (!empty($image26)) {
            foreach($image26 as $indx => $value) {
                $image = $image26[$indx];
                if (!empty($image)) {
                    $id = $id26[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode26!='') {
            foreach($qrcode26 as $indx => $value) {
                $qrcode = $qrcode26[$indx];
                $id = $id26[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }
        if (!empty($image27)) {
            foreach($image27 as $indx => $value) {
                $image = $image27[$indx];
                if (!empty($image)) {
                    $id = $id27[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode27!='') {
            foreach($qrcode27 as $indx => $value) {
                $qrcode = $qrcode27[$indx];
                $id = $id27[$indx]; 
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }
        // // END HQ
        if (!empty($image28)) {
            foreach($image28 as $indx => $value) {
                $image = $image28[$indx];
                if (!empty($image)) {
                    $id = $id28[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode28!='') {
            foreach($qrcode28 as $indx => $value) {
                $qrcode = $qrcode28[$indx];
                $id = $id28[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }
        if (!empty($image29)) {
            foreach($image29 as $indx => $value) {
                $image = $image29[$indx];
                if (!empty($image)) {
                    $id = $id29[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode29!='') {  
            foreach($qrcode29 as $indx => $value) {
                $qrcode = $qrcode29[$indx];
                $id = $id29[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }
        if (!empty($image30)) {
            foreach($image30 as $indx => $value) {
                $image = $image30[$indx];
                if (!empty($image)) {
                    $id = $id30[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode30!='') {       
            foreach($qrcode30 as $indx => $value) {
                $qrcode = $qrcode30[$indx];
                $id = $id30[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }

        if (!empty($image31)) {
            foreach($image31 as $indx => $value) {
                $image = $image31[$indx];
                if (!empty($image)) {
                    $id = $id31[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);

                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }

        if ($qrcode31!='') {
            foreach($qrcode31 as $indx => $value) {
                $qrcode = $qrcode31[$indx];
                $id = $id31[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }

        if (!empty($image32)) {
            foreach($image32 as $indx => $value) {
                $image = $image32[$indx];
                if (!empty($image)) {
                    $id = $id32[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }

        if ($qrcode32!='') {
            foreach($qrcode32 as $indx => $value) {
                $qrcode = $qrcode32[$indx];
                $id = $id32[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }
        if (!empty($image33)) {
            foreach($image33 as $indx => $value) {
                $image = $image33[$indx];
                if (!empty($image)) {
                    $id = $id33[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode33!='') {    
            foreach($qrcode33 as $indx => $value) {
                $qrcode = $qrcode33[$indx];
                $id = $id33[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }

        if (!empty($image34)) {
            foreach($image34 as $indx => $value) {
                $image = $image34[$indx];
                if (!empty($image)) {
                    $id = $id34[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }

        if ($qrcode34!='') {
            foreach($qrcode34 as $indx => $value) {
                $qrcode = $qrcode34[$indx];
                $id = $id34[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }
        if (!empty($image35)) {
            foreach($image35 as $indx => $value) {
                $image = $image35[$indx];
                if (!empty($image)) {
                    $id = $id35[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode35!='') {  
            foreach($qrcode35 as $indx => $value) {
                $qrcode = $qrcode35[$indx];
                $id = $id35[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);     
            } 
        }

        if (!empty($image36)) {
            foreach($image36 as $indx => $value) {
                $image = $image36[$indx];
                if (!empty($image)) {
                    $id = $id36[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode36!='') { 
            foreach($qrcode36 as $indx => $value) {
                $qrcode = $qrcode36[$indx];
                $id = $id36[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image37)) {
            foreach($image37 as $indx => $value) {
                $image = $image37[$indx];
                if (!empty($image)) {
                    $id = $id37[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode37!='') { 
            foreach($qrcode37 as $indx => $value) {
                $qrcode = $qrcode37[$indx];
                $id = $id37[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image38)) {
            foreach($image38 as $indx => $value) {
                $image = $image38[$indx];
                if (!empty($image)) {
                    $id = $id38[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode38!='') { 
            foreach($qrcode38 as $indx => $value) {
                $qrcode = $qrcode38[$indx];
                $id = $id38[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image39)) {
            foreach($image39 as $indx => $value) {
                $image = $image39[$indx];
                if (!empty($image)) {
                    $id = $id39[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode39!='') { 
            foreach($qrcode39 as $indx => $value) {
                $qrcode = $qrcode39[$indx];
                $id = $id39[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image40)) {
            foreach($image40 as $indx => $value) {
                $image = $image40[$indx];
                if (!empty($image)) {
                    $id = $id40[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode40!='') { 
            foreach($qrcode40 as $indx => $value) {
                $qrcode = $qrcode40[$indx];
                $id = $id40[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image41)) {
            foreach($image41 as $indx => $value) {
                $image = $image41[$indx];
                if (!empty($image)) {
                    $id = $id41[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode41!='') { 
            foreach($qrcode41 as $indx => $value) {
                $qrcode = $qrcode41[$indx];
                $id = $id41[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image42)) {
            foreach($image42 as $indx => $value) {
                $image = $image42[$indx];
                if (!empty($image)) {
                    $id = $id42[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode42!='') { 
            foreach($qrcode42 as $indx => $value) {
                $qrcode = $qrcode42[$indx];
                $id = $id42[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image43)) {
            foreach($image43 as $indx => $value) {
                $image = $image43[$indx];
                if (!empty($image)) {
                    $id = $id43[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode43!='') { 
            foreach($qrcode43 as $indx => $value) {
                $qrcode = $qrcode43[$indx];
                $id = $id43[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image44)) {
            foreach($image44 as $indx => $value) {
                $image = $image44[$indx];
                if (!empty($image)) {
                    $id = $id44[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode44!='') { 
            foreach($qrcode44 as $indx => $value) {
                $qrcode = $qrcode44[$indx];
                $id = $id44[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image45)) {
            foreach($image45 as $indx => $value) {
                $image = $image45[$indx];
                if (!empty($image)) {
                    $id = $id45[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode45!='') { 
            foreach($qrcode45 as $indx => $value) {
                $qrcode = $qrcode45[$indx];
                $id = $id45[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image46)) {
            foreach($image46 as $indx => $value) {
                $image = $image46[$indx];
                if (!empty($image)) {
                    $id = $id46[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode46!='') { 
            foreach($qrcode46 as $indx => $value) {
                $qrcode = $qrcode46[$indx];
                $id = $id46[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image47)) {
            foreach($image47 as $indx => $value) {
                $image = $image47[$indx];
                if (!empty($image)) {
                    $id = $id47[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode47!='') { 
            foreach($qrcode47 as $indx => $value) {
                $qrcode = $qrcode47[$indx];
                $id = $id47[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image48)) {
            foreach($image48 as $indx => $value) {
                $image = $image48[$indx];
                if (!empty($image)) {
                    $id = $id48[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode48!='') { 
            foreach($qrcode68 as $indx => $value) {
                $qrcode = $qrcode48[$indx];
                $id = $id48[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image49)) {
            foreach($image49 as $indx => $value) {
                $image = $image49[$indx];
                if (!empty($image)) {
                    $id = $id49[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode49!='') { 
            foreach($qrcode49 as $indx => $value) {
                $qrcode = $qrcode49[$indx];
                $id = $id49[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image50)) {
            foreach($image50 as $indx => $value) {
                $image = $image50[$indx];
                if (!empty($image)) {
                    $id = $id50[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode50!='') { 
            foreach($qrcode50 as $indx => $value) {
                $qrcode = $qrcode50[$indx];
                $id = $id50[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image51)) {
            foreach($image51 as $indx => $value) {
                $image = $image51[$indx];
                if (!empty($image)) {
                    $id = $id51[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode51!='') { 
            foreach($qrcode51 as $indx => $value) {
                $qrcode = $qrcode51[$indx];
                $id = $id51[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image52)) {
            foreach($image52 as $indx => $value) {
                $image = $image52[$indx];
                if (!empty($image)) {
                    $id = $id52[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode52!='') { 
            foreach($qrcode52 as $indx => $value) {
                $qrcode = $qrcode52[$indx];
                $id = $id52[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }
        //Australia
        if (!empty($image53)) {
            foreach($image53 as $indx => $value) {
                $image = $image53[$indx];
                if (!empty($image)) {
                    $id = $id53[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode53!='') { 
            foreach($qrcode53 as $indx => $value) {
                $qrcode = $qrcode53[$indx];
                $id = $id53[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image54)) {
            foreach($image54 as $indx => $value) {
                $image = $image54[$indx];
                if (!empty($image)) {
                    $id = $id54[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode54!='') { 
            foreach($qrcode54 as $indx => $value) {
                $qrcode = $qrcode54[$indx];
                $id = $id54[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image55)) {
            foreach($image55 as $indx => $value) {
                $image = $image55[$indx];
                if (!empty($image)) {
                    $id = $id55[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode55!='') { 
            foreach($qrcode55 as $indx => $value) {
                $qrcode = $qrcode55[$indx];
                $id = $id55[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image56)) {
            foreach($image56 as $indx => $value) {
                $image = $image56[$indx];
                if (!empty($image)) {
                    $id = $id56[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode56!='') { 
            foreach($qrcode56 as $indx => $value) {
                $qrcode = $qrcode56[$indx];
                $id = $id56[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image57)) {
            foreach($image57 as $indx => $value) {
                $image = $image57[$indx];
                if (!empty($image)) {
                    $id = $id57[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode57!='') { 
            foreach($qrcode57 as $indx => $value) {
                $qrcode = $qrcode57[$indx];
                $id = $id57[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image58)) {
            foreach($image58 as $indx => $value) {
                $image = $image58[$indx];
                if (!empty($image)) {
                    $id = $id58[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode58!='') { 
            foreach($qrcode58 as $indx => $value) {
                $qrcode = $qrcode58[$indx];
                $id = $id58[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image59)) {
            foreach($image59 as $indx => $value) {
                $image = $image59[$indx];
                if (!empty($image)) {
                    $id = $id59[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode59!='') { 
            foreach($qrcode59 as $indx => $value) {
                $qrcode = $qrcode59[$indx];
                $id = $id59[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image60)) {
            foreach($image60 as $indx => $value) {
                $image = $image60[$indx];
                if (!empty($image)) {
                    $id = $id60[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode60!='') { 
            foreach($qrcode60 as $indx => $value) {
                $qrcode = $qrcode60[$indx];
                $id = $id60[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image61)) {
            foreach($image61 as $indx => $value) {
                $image = $image61[$indx];
                if (!empty($image)) {
                    $id = $id61[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode61!='') { 
            foreach($qrcode61 as $indx => $value) {
                $qrcode = $qrcode61[$indx];
                $id = $id61[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image62)) {
            foreach($image62 as $indx => $value) {
                $image = $image62[$indx];
                if (!empty($image)) {
                    $id = $id62[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode62!='') { 
            foreach($qrcode62 as $indx => $value) {
                $qrcode = $qrcode62[$indx];
                $id = $id62[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image63)) {
            foreach($image63 as $indx => $value) {
                $image = $image63[$indx];
                if (!empty($image)) {
                    $id = $id63[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode63!='') { 
            foreach($qrcode63 as $indx => $value) {
                $qrcode = $qrcode63[$indx];
                $id = $id63[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image64)) {
            foreach($image64 as $indx => $value) {
                $image = $image64[$indx];
                if (!empty($image)) {
                    $id = $id64[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode64!='') { 
            foreach($qrcode64 as $indx => $value) {
                $qrcode = $qrcode64[$indx];
                $id = $id64[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        if (!empty($image65)) {
            foreach($image65 as $indx => $value) {
                $image = $image65[$indx];
                if (!empty($image)) {
                    $id = $id65[$indx];
                    $images = $id . '-' . $unique . '.' . $image->getClientOriginalExtension();
                    $image->move($path, $images);
                    BannerTemplate::scopeUpdateTemplateImage($images,$id);
                }
            } 
        }
        if ($qrcode65!='') { 
            foreach($qrcode65 as $indx => $value) {
                $qrcode = $qrcode65[$indx];
                $id = $id65[$indx];
                BannerTemplate::scopeUpdateTemplateQrcode($qrcode,$id);    
            } 
        }

        return Redirect::back()->with('success','Banner has been updated.');

    }
}
?>