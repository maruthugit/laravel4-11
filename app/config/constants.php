<?php
//file : app/config/constants.php
//
if ( ! file_exists("./media/pdf")) {
    mkdir("./media/pdf", 0755, true);
}

if ( ! file_exists("./media/pdf/po")) {
    mkdir("./media/pdf/po", 0755, true);
}

if ( ! file_exists("./media/pdf/po_parent")) {
    mkdir("./media/pdf/po_parent", 0755, true);
}

if ( ! file_exists("./media/pdf/supplier")) {
    mkdir("./media/pdf/supplier", 0755, true);
}

if ( ! file_exists("./media/pdf/invoice")) {
    mkdir("./media/pdf/invoice", 0755, true);
}

if ( ! file_exists("./media/pdf/stock")) {
    mkdir("./media/pdf/stock", 0755, true);
}

if ( ! file_exists("./media/pdf/invoice/archived")) {
    mkdir("./media/pdf/invoice/archived", 0755, true);
}
/*
if ( ! file_exists("./media/pdf/invoice/INVOICE2017")) {
    mkdir("./media/pdf/invoice/archived", 0755, true);
}
*/
if ( ! file_exists("./media/pdf/invoice/einv")) {
    mkdir("./media/pdf/invoice/einv", 0755, true);
}

if ( ! file_exists("./media/pdf/invoice_parent")) {
    mkdir("./media/pdf/invoice_parent", 0755, true);
}

if ( ! file_exists("./media/pdf/do")) {
    mkdir("./media/pdf/do", 0755, true);
}

if(!file_exists("./media/pdf/cn")) {
    mkdir("./media/pdf/cn", 0755, true);
}

//logistic module
if ( ! file_exists("./logistic/images/signature")) {
    mkdir("./logistic/images/signature", 0755, true);
}

if ( ! file_exists("./logistic/media/pdf/do")) {
    mkdir("./logistic/media/pdf/do", 0755, true);
}

if ( ! file_exists("./gst/csv")) {
    mkdir("./gst/csv", 0755, true);
}

if ( ! file_exists("./dynamic/images")) {
    mkdir("./dynamic/images", 0755, true);
}

if ( ! file_exists(".media/csv/import")) {
    mkdir(".media/csv/import", 0755, true);
}

// temporary function to insert previous order of JIT
if ( ! file_exists(".media/csv/jit")) {
    mkdir(".media/csv/jit", 0755, true);
}

if ( ! file_exists("./media/csv/report")) {
    mkdir("./media/csv/report", 0755, true);
}

/*RoaYu*/
/* BANNER Module */
if ( ! file_exists("./images/banner")) {
    mkdir("./images/banner", 0755, true);
}

if ( ! file_exists("./images/banner/en")) {
    mkdir("./images/banner/en", 0755, true);
}

if ( ! file_exists("./images/banner/cn")) {
    mkdir("./images/banner/cn", 0755, true);
}

if ( ! file_exists("./images/banner/my")) {
    mkdir("./images/banner/my", 0755, true);
}

if ( ! file_exists("./images/banner/thumbs")) {
    mkdir("./images/banner/thumbs", 0755, true);
}

if ( ! file_exists("./images/banner/thumbs/en")) {
    mkdir("./images/banner/thumbs/en", 0755, true);
}

if ( ! file_exists("./images/banner/thumbs/cn")) {
    mkdir("./images/banner/thumbs/cn", 0755, true);
}

if ( ! file_exists("./images/banner/thumbs/my")) {
    mkdir("./images/banner/thumbs/my", 0755, true);
}

if ( ! file_exists("./images/banner_tab")) {
    mkdir("./images/banner_tab", 0755, true);
}

if ( ! file_exists("./images/banner_tab/en")) {
    mkdir("./images/banner_tab/en", 0755, true);
}

if ( ! file_exists("./images/banner_tab/cn")) {
    mkdir("./images/banner_tab/cn", 0755, true);
}

if ( ! file_exists("./images/banner_tab/my")) {
    mkdir("./images/banner_tab/my", 0755, true);
}

if ( ! file_exists("./images/banner_tab/thumbs")) {
    mkdir("./images/banner_tab/thumbs", 0755, true);
}

if ( ! file_exists("./images/banner_tab/thumbs/en")) {
    mkdir("./images/banner_tab/thumbs/en", 0755, true);
}

if ( ! file_exists("./images/banner_tab/thumbs/cn")) {
    mkdir("./images/banner_tab/thumbs/cn", 0755, true);
}

if ( ! file_exists("./images/banner_tab/thumbs/my")) {
    mkdir("./images/banner_tab/thumbs/my", 0755, true);
}

/* SELLER Module */
if ( ! file_exists("./images/seller")) {
    mkdir("./images/seller", 0755, true);
}

/* LATEST NEWS Module */
if ( ! file_exists("./images/latest_news")) {
    mkdir("./images/latest_news", 0755, true);
}

if ( ! file_exists("./images/latest_news/en")) {
    mkdir("./images/latest_news/en", 0755, true);
}

if ( ! file_exists("./images/latest_news/cn")) {
    mkdir("./images/latest_news/cn", 0755, true);
}

if ( ! file_exists("./images/latest_news/my")) {
    mkdir("./images/latest_news/my", 0755, true);
}

if ( ! file_exists("./images/latest_news/thumbs")) {
    mkdir("./images/latest_news/thumbs", 0755, true);
}

if ( ! file_exists("./images/latest_news/thumbs/en")) {
    mkdir("./images/latest_news/thumbs/en", 0755, true);
}

if ( ! file_exists("./images/latest_news/thumbs/cn")) {
    mkdir("./images/latest_news/thumbs/cn", 0755, true);
}

if ( ! file_exists("./images/latest_news/thumbs/my")) {
    mkdir("./images/latest_news/thumbs/my", 0755, true);
}

if ( ! file_exists("./images/latest_news_tab")) {
    mkdir("./images/latest_news_tab", 0755, true);
}

if ( ! file_exists("./images/latest_news_tab/en")) {
    mkdir("./images/latest_news_tab/en", 0755, true);
}

if ( ! file_exists("./images/latest_news_tab/cn")) {
    mkdir("./images/latest_news_tab/cn", 0755, true);
}

if ( ! file_exists("./images/latest_news_tab/my")) {
    mkdir("./images/latest_news_tab/my", 0755, true);
}

if ( ! file_exists("./images/latest_news_tab/thumbs")) {
    mkdir("./images/latest_news_tab/thumbs", 0755, true);
}

if ( ! file_exists("./images/latest_news_tab/thumbs/en")) {
    mkdir("./images/latest_news_tab/thumbs/en", 0755, true);
}

if ( ! file_exists("./images/latest_news_tab/thumbs/cn")) {
    mkdir("./images/latest_news_tab/thumbs/cn", 0755, true);
}

if ( ! file_exists("./images/latest_news_tab/thumbs/my")) {
    mkdir("./images/latest_news_tab/thumbs/my", 0755, true);
}

/* HOT ITEMS Module */
if ( ! file_exists("./images/hot_items")) {
    mkdir("./images/hot_items", 0755, true);
}

if ( ! file_exists("./images/hot_items/en")) {
    mkdir("./images/hot_items/en", 0755, true);
}

if ( ! file_exists("./images/hot_items/cn")) {
    mkdir("./images/hot_items/cn", 0755, true);
}

if ( ! file_exists("./images/hot_items/my")) {
    mkdir("./images/hot_items/my", 0755, true);
}

if ( ! file_exists("./images/hot_items/thumbs")) {
    mkdir("./images/hot_items/thumbs", 0755, true);
}

if ( ! file_exists("./images/hot_items/thumbs/en")) {
    mkdir("./images/hot_items/thumbs/en", 0755, true);
}

if ( ! file_exists("./images/hot_items/thumbs/cn")) {
    mkdir("./images/hot_items/thumbs/cn", 0755, true);
}

if ( ! file_exists("./images/hot_items/thumbs/my")) {
    mkdir("./images/hot_items/thumbs/my", 0755, true);
}

if ( ! file_exists("./images/hot_items_tab")) {
    mkdir("./images/hot_items_tab", 0755, true);
}

if ( ! file_exists("./images/hot_items_tab/en")) {
    mkdir("./images/hot_items_tab/en", 0755, true);
}

if ( ! file_exists("./images/hot_items_tab/cn")) {
    mkdir("./images/hot_items_tab/cn", 0755, true);
}

if ( ! file_exists("./images/hot_items_tab/my")) {
    mkdir("./images/hot_items_tab/my", 0755, true);
}

if ( ! file_exists("./images/hot_items_tab/thumbs")) {
    mkdir("./images/hot_items_tab/thumbs", 0755, true);
}

if ( ! file_exists("./images/hot_items_tab/thumbs/en")) {
    mkdir("./images/hot_items_tab/thumbs/en", 0755, true);
}

if ( ! file_exists("./images/hot_items_tab/thumbs/cn")) {
    mkdir("./images/hot_items_tab/thumbs/cn", 0755, true);
}

if ( ! file_exists("./images/hot_items_tab/thumbs/my")) {
    mkdir("./images/hot_items_tab/thumbs/my", 0755, true);
}

/* Brands Module */
if ( ! file_exists("./images/brand_items")) {
    mkdir("./images/brand_items", 0755, true);
}

if ( ! file_exists("./images/brand_items/en")) {
    mkdir("./images/brand_items/en", 0755, true);
}

if ( ! file_exists("./images/brand_items/cn")) {
    mkdir("./images/brand_items/cn", 0755, true);
}

if ( ! file_exists("./images/brand_items/my")) {
    mkdir("./images/brand_items/my", 0755, true);
}

if ( ! file_exists("./images/brand_items/thumbs")) {
    mkdir("./images/brand_items/thumbs", 0755, true);
}

if ( ! file_exists("./images/brand_items/thumbs/en")) {
    mkdir("./images/brand_items/thumbs/en", 0755, true);
}

if ( ! file_exists("./images/brand_items/thumbs/cn")) {
    mkdir("./images/brand_items/thumbs/cn", 0755, true);
}

if ( ! file_exists("./images/brand_items/thumbs/my")) {
    mkdir("./images/brand_items/thumbs/my", 0755, true);
}

if ( ! file_exists("./images/brand_items_tab")) {
    mkdir("./images/brand_items_tab", 0755, true);
}

if ( ! file_exists("./images/brand_items_tab/en")) {
    mkdir("./images/brand_items_tab/en", 0755, true);
}

if ( ! file_exists("./images/brand_items_tab/cn")) {
    mkdir("./images/brand_items_tab/cn", 0755, true);
}

if ( ! file_exists("./images/brand_items_tab/my")) {
    mkdir("./images/brand_items_tab/my", 0755, true);
}

if ( ! file_exists("./images/brand_items_tab/thumbs")) {
    mkdir("./images/brand_items_tab/thumbs", 0755, true);
}

if ( ! file_exists("./images/brand_items_tab/thumbs/en")) {
    mkdir("./images/brand_items_tab/thumbs/en", 0755, true);
}

if ( ! file_exists("./images/brand_items_tab/thumbs/cn")) {
    mkdir("./images/brand_items_tab/thumbs/cn", 0755, true);
}

if ( ! file_exists("./images/brand_items_tab/thumbs/my")) {
    mkdir("./images/brand_items_tab/thumbs/my", 0755, true);
}

if ( ! file_exists("./archive/banner")) {
    mkdir("./archive/banner", 0755, true);
}

if ( ! file_exists("./archive/banner/thumbs")) {
    mkdir("./archive/banner/thumbs", 0755, true);
}

if ( ! file_exists("./archive/seller")) {
    mkdir("./archive/seller", 0755, true);
}

if ( ! file_exists("./archive/latest_news")) {
    mkdir("./archive/latest_news", 0755, true);
}

if ( ! file_exists("./archive/latest_news/thumbs")) {
    mkdir("./archive/latest_news/thumbs", 0755, true);
}

if ( ! file_exists("./media/csv")) {
    mkdir("./media/csv", 0755, true);
}

if ( ! file_exists("./media/csv/upload")) {
    mkdir("./media/csv/upload", 0755, true);
}

if ( ! file_exists("./media/csv/log")) {
    mkdir("./media/csv/log", 0755, true);
}

if ( ! file_exists("./images/charity")) {
    mkdir("./images/charity", 0755, true);
}

if ( ! file_exists("./media/xml")) {
    mkdir("./media/xml", 0755, true);
}

if ( ! file_exists(".media/csv/inventory")) {
    mkdir(".media/csv/inventory", 0755, true);
}

if ( ! file_exists("./images/driver")) {
    mkdir("./images/driver", 0755, true);
}

if ( ! file_exists("./media/csv/pendingdelivery")) {
    mkdir("./media/csv/pendingdelivery", 0755, true);
}

if ( ! file_exists("./media/csv/withoutimg")) {
    mkdir("./media/csv/withoutimg", 0755, true);
}

if ( ! file_exists("./media/zip/po")) {
    mkdir("./media/zip/po", 0755, true);
}

if ( ! file_exists("./media/zip/grn")) {
    mkdir("./media/zip/grn", 0755, true);
}

if ( ! file_exists("./media/txt/einv")) {
    mkdir("./media/txt/einv", 0755, true);
}

if ( ! file_exists("./media/pdf/grn")) {
    mkdir("./media/pdf/grn", 0755, true);
}

if ( ! file_exists("./media/pdf/einv")) {
    mkdir("./media/pdf/einv", 0755, true);
}

if ( ! file_exists("./media/pdf/drivertimesheet")) {
    mkdir("./media/pdf/drivertimesheet", 0755, true);
}

if ( ! file_exists("./media/pdf/gdf")) {
    mkdir("./media/pdf/gdf", 0755, true);
}

if ( ! file_exists("./media/pdf/stocktransfer")) {
    mkdir("./media/pdf/stocktransfer", 0755, true);
}

if ( ! file_exists("./media/csv/stockin")) {
    mkdir("./media/csv/stockin", 0755, true);
}

if ( ! file_exists("./images/taskmsg")) {
    mkdir("./images/taskmsg", 0755, true);
}


if ( ! file_exists("./media/refund_remark")) {
    mkdir("./media/refund_remark", 0755, true);
}

if ( ! file_exists("./media/pdf/purchase")) {
    mkdir("./media/pdf/purchase", 0755, true);
}


return array(
    'CMS_VERSION'                    => '2.0.1',
    'ADMIN_FOLDER'                   => 'admin',
    'SYSTEM_ADMIN'                   => 'sysadmin',
    'CHECKOUT_FOLDER'                => 'checkout',
    'PO_PDF_FILE_PATH'               => 'media/pdf/po',
    'PO_PARENT_PDF_FILE_PATH'        => 'media/pdf/po_parent',
    'SUPPLIER_INVOICE_PDF_FILE_PATH' => 'media/pdf/supplier',
    'INVOICE_PDF_FILE_PATH'          => 'media/pdf/invoice',
    'STOCK_INVOICE_PDF_FILE_PATH'    => 'media/pdf/stock',
    'SORTER_DO_PDF_FILE_PATH'        => 'media/pdf/sorted',
    'INVOICE_PDF_FILE_PATH_2017'     => 'media/pdf/invoice/INVOICE2017',
    'EINVOICE_PDF_FILE_PATH_2016'    => 'media/pdf/invoice/einv',
    'INVOICE_PDF_FILE_PATH_2016'     => 'media/pdf/invoice/archived',
    'INVOICE_PARENT_PDF_FILE_PATH'   => 'media/pdf/invoice_parent',
    'DO_PDF_FILE_PATH'               => 'media/pdf/do',
    'STOCK_FILE_WRITE_OFF'           => 'media/pdf/writeoff',
    'PURCHASE_PDF_FILE_PATH'         => 'media/pdf/purchase',
    'ENVIRONMENT'                    => 'live',
    'TEST_MAIL'                      => 'maruthu@tmgrocer.com',
    'CURRENCY'                       => Fees::get_currency(),
    'INVOICE_PREFIX'                 => 'TMG-25/',

    'LOGISTIC_DO_PATH'               => 'logistic/media/pdf/do',
    'LOGISTIC_SIG_PATH'              => 'logistic/images/signature',
    'LOGISTIC_DELIVERY_IMG_PATH'     => 'logistic/images/delivery_img',
    'LOGISTIC_APP_INSTALLER'         => 'logistic/installer',
    
    'FEEDBACK_IMG'                   => 'images/feedback',
    'TASK_MSG_IMG'                   => 'images/taskmsg',

    'GST_REPORT_PATH'                => 'gst/csv',

    'REPORT_PATH'                    => 'media/csv/report',

    'BANNER_FILE_PATH'               => 'images/banner/',
    'BANNER_THUMB_FILE_PATH'         => 'images/banner/thumbs/',
    'BANNER_TAB_FILE_PATH'           => 'images/banner_tab/',
    'BANNER_TAB_THUMB_FILE_PATH'     => 'images/banner_tab/thumbs/',

    'PRODUCT_IMAGE_THUMB_FILE_PATH'  => 'images/data/thumbs/',

    'SELLER_FILE_PATH'               => 'images/seller/',
    
    'PUSH_NOTIFICATION_IMAGE_PATH'   => 'images/push/',
    
    'ATTACHMENT_PDF'                 => 'media/pdf/refund/',
    'ATTACHMENT_IMAGE'               => 'media/images/refund/',
    
    'ATTACHMENT_GEARUP_RECEIPT'      => 'media/images/gearup_receipt/',

    'CSV_FILE_PATH'                  => 'media/csv/',
    'CSV_UPLOAD_PATH'                => 'media/csv/upload/',
    'CSV_IMPORT_PATH'                => 'media/csv/import/',
    'PRODUCT_IMPORT_CSV_IMPORT_PATH' => 'media/csv/import/cost_price/',
    'CSV_JIT_PATH'                   => 'media/csv/jit/', // temporary function to insert previous order of JIT
    'CSV_LOG_PATH'                   => 'media/csv/log/',
    'CSV_PENDING_DELIVERY_PATH'      => 'media/csv/pendingdelivery',
    'CSV_WITHOUT_PHOTO_PATH'         => 'media/csv/withoutimg',
    
    'CN_PDF_FILE_PATH'               => 'media/pdf/cn/',

    'LATESTNEWS_FILE_PATH'           => 'images/latest_news/',
    'LATESTNEWS_THUMB_FILE_PATH'     => 'images/latest_news/thumbs/',
    'LATESTNEWS_TAB_FILE_PATH'       => 'images/latest_news_tab/',
    'LATESTNEWS_TAB_THUMB_FILE_PATH' => 'images/latest_news_tab/thumbs/',

    'HOTITEM_FILE_PATH'              => 'images/hot_items/',
    'HOTITEM_THUMB_FILE_PATH'        => 'images/hot_items/thumbs/',
    'HOTITEM_TAB_FILE_PATH'          => 'images/hot_items_tab/',
    'HOTITEM_TAB_THUMB_FILE_PATH'    => 'images/hot_items_tab/thumbs/',
    
    'BRANDITEM_FILE_PATH'              => 'images/brand_items/',
    'BRANDITEM_THUMB_FILE_PATH'        => 'images/brand_items/thumbs/',
    'BRANDITEM_TAB_FILE_PATH'          => 'images/brand_items_tab/',
    'BRANDITEM_TAB_THUMB_FILE_PATH'    => 'images/brand_items_tab/thumbs/',

    'ARC_BANNER_FILE_PATH'           => 'archive/banner/',
    'ARC_BANNER_THUMB_FILE_PATH'     => 'archive/banner/thumbs/',
    'ARC_SELLER_FILE_PATH'           => 'archive/seller/',
    'ARC_LATESTNEWS_FILE_PATH'       => 'archive/latest_news/',
    'ARC_LATESTNEWS_THUMB_FILE_PATH' => 'archive/latest_news/thumbs/',
    
    'REGION_IMG_THUMB'               => 'images/region/',

    'GOOGLE_CLIENT_SECRET'           => base_path().'/app/config/google/client_secrets.p12',
    'GOOGLE_SERVICE_ACCOUNT_EMAIL'   => 'jocom-475@elaborate-night-133723.iam.gserviceaccount.com',

    'POINTS'                         => ['4053' => '1'], // Product ID => Point Type ID

    'HIDE_PRICE'                     => array(), // Hide pricing     'bumbudesaklcc', 'bumbudesaklia2'

    'CHARITY_FILE_PATH'               => 'images/charity/',
    
    'XML_FILE_PATH'                  => 'media/xml/', 
    
    'HTML_CONTENT_BLOG_PATH'         => 'media/html/blog/', 
    'IMAGE_CONTENT_BLOG_PATH'        => 'images/blog/',
    
    'DRIVER_PROFILE_FILE_PATH'       => 'images/driver/',
    
    'DRIVER_PROFILE_FILE_URL'        => 'https://api.jocom.com.my/',
    
    'CMS_USER_FILE_PATH'             => 'images/userprofile/',

    'NEW_BANNER_FILE_PATH'           => 'images/manage_banners/',
    'NEW_JOCOMMY_BANNER_PATH'        => 'images/jocommy/',
    'BANNER_POPUP'                   => 'images/popup_banners/', // NEW BANNER TYPE
    
    'MPAY_IMAGE_PATH'                => 'images/mpay/',

    'NEW_INVOICE_START_DATE'         => '2017-04-01 00:00:00', // Without GST
    'NEW_INVOICE_V2_START_DATE'      => '2017-10-01 00:00:00', // Included GST
    'NEW_INVOICE_SST_START_DATE'     => '2018-09-01 00:00:00', // New SST Tax
    'NEW_INVOICE_SST_DISCOUNT_START_DATE'     => '2019-03-01 00:00:00', // Invoice Format With Discount Value
    
    'STOCKOUT_START_DATE'            => '2017-11-03 00:00:00',
    
    'ELEVENSTREET_PST_START_DATE'    => '2018-10-01 00:00:00',
    
    'ELEVENSTREET_PST_END_DATE'      => '2018-12-31 23:59:59',
    
    'CSV_FILE_INVENTORY_PATH'        => 'media/csv/inventory/',
    
    'JOCOMMY_BANNER_PATH'            => 'images/jocommy_banner/',
    
    'COMMENT_IMG_PATH'               => 'images/comment/',
    
    // 11Street API KEY 
    'ElevenStreetAPIKEY_ACCT_1'      => "cde0717a0f6f5495c1e5cd00eec71523",
    'ElevenStreetAPIKEY_ACCT_2'      => "0bb8b77ea9f70301b89799008392328c",
    'ElevenStreetAPIKEY_ACCT_FN'     => "89a7a16775a6b741bf069ad91b98ea21",
    'ElevenStreetAPIKEY_ACCT_COCACOLA'     => "8397cba597fe11db485e8e785e09af7c",
    'ElevenStreetAPIKEY_ACCT_SPRITZER'     => "5f9a72d40ed090a3dc8c792c41a8c592",
    'ElevenStreetAPIKEY_ACCT_CACTUS'     => "1bb6dbbd61df6497c969a2f6e580d8c2",
    'ElevenStreetAPIKEY_ACCT_FNCREAMERIES'     => "f107f7e4b84bafb95855870647543b3d",
    'ElevenStreetAPIKEY_ACCT_STARBUCK'     => "c8e69b35ff8e23849eeed6459f98ab26",
    'ElevenStreetAPIKEY_ACCT_POKKA'     => "0def6296bc942307efcaa82423e560e6",
    'ElevenStreetAPIKEY_ACCT_YEOS'     => "03c0a23e8bc558c19fd014117d07edf0",
    'ElevenStreetAPIKEY_ACCT_ORIENTAL'     => "f0d36d78f7921dccfb8ed56d0031cc87",
    'ElevenStreetAPIKEY_ACCT_KAWANFOOD' => "fa3a3d430faccccd48703e21cf2cdc0c",
    'ElevenStreetAPIKEY_ACCT_NIKUDO' => "391b388c0b504c71de2ffed42a652638",
    'ElevenStreetAPIKEY_ACCT_ETIKA' => "c36f2fde0f407a6d6e14b8fe6b6c096b",
    'ElevenStreetManagerEmail'       => "asif@jocom.my",
    'ElevenStreetManagerEmailCC'     => "quenny@jocom.my",
    'AstroGoShopManagerEmail'       => "asif@jocom.my",
    'AstroGoShopManagerEmailCC'       => "asif@jocom.my",
    'PrestomallManagerEmail'       => "etika@jocom.my",
    'PGMallManagerEmail'       => "asif@jocom.my",
    
    
    'GOOGLE_MAP_API_KEY'             => "AIzaSyDcTN4TPOfZRUmCF_7S_4w3sFlxaGEr3f4", // "AIzaSyBPcKtMmHfljZFsJSHy4wuzp5vO7NNwGVo", 

    //mcheckout molpay      
    'MERCHANT_ID_LIVE' => 'tmgrocer',       
    'MERCHANT_ID_TEST' => 'webtng_Dev',     
            
    'MOLPAY_VERIFYKEY_LIVE' => '66d0b72e8824aa4d9f1bd02235f971b5',      
    'MOLPAY_VERIFYKEY_TEST' => 'c181b6dde003c0a99217ba98a4af861d',
    
    //MCheckout revPay
    'MERCHANT_REVPAY_ID_LIVE' => 'MER00000093106',       
    'MERCHANT_REVPAY_ID_DEV' => 'MER00000004016',     
    
    'MERCHANT_REVPAY_KEY_LIVE' => 'VtZhpEkJXl',  
    'MERCHANT_REVPAY_KEY_LIVE_01' => 'Dpu1GYyHws',  
    'MERCHANT_REVPAY_KEY_LIVE_02' => '6NJfNZEI0g',  
    'MERCHANT_REVPAY_KEY_DEV' => 'KzIb2wb3HX',  
    'MERCHANT_REVPAY_KEY_DEV_01' => 'cIEpnF4o9F',  
    'MERCHANT_REVPAY_KEY_DEV_02' => 'datujUakUv',  
    
    //Qoo10 //
    'Qoo10_apiKey'          => "BhiQeRsAhG0h3v7a0UR6HbAk1U_g_1_YUVSll0gjLoEnKAM_g_3_",
    'Qoo10_URL'             => 'https://www.qoo10.my',
    'Qoo10_SINGAPORE_URL'   => 'https://api.qoo10.sg',
    'Qoo10ManagerEmail'     => "asif@jocom.my",
    'Qoo10ManagerEmailCC'   => "quenny@jocom.my",
    'Qoo10_user_id'         => "jocom",
    'Qoo10_pwd'             => "jocom@ops99", //"jocom@99",
    
    'Qoo10_fn_apiKey'       => "BhiQeRsAhG1jS_g_2_mP6_g_1_iNCChyqLzfUzdhpEJhsT6QiR4_g_3_",
    'Qoo10_fn_user_id'      => "fnonline",
    'Qoo10_fn_pwd'          => "jocom@ops99",
    
    //PGMall(14/03/2022)
    
    'PGMallJocomAuth'       => "0HblIS38GHPz3Y64eZG0Lxx8yfK/K/QS5Vpz3FhTYRsWWbtUiDyuPeZMSB07391tdaaB+k0JK42wz+wnWfkeorWBTAbi7Ec0ovxGkn8QgQIdsl/MdAOTIoywb3rJdQB0|Ql+0tccfKLDKi0e5u4dXIMCIFp434Jh3OZFEoBqyVXQ=",    

    
    //Shopee //
    'ShopeeManagerEmail'    => "asif@jocom.my",
    'ShopeeManagerEmailCC'  => "quenny@jocom.my",
    'ShopeeJocomShopid'     => "3558759",
    'ShopeeJocomPartnerID'  => "25179",
    'ShopeeJocomSecret'     => "dbcd95b3936fa1d040b146aa9f6854363a1c56f2837644ddc8875f8630e927d9",
    'ShopeeCocaShopid'      => "28891865",
    'ShopeeCocaPartnerID'   => "25180",
    'ShopeeCocaSecret'      => "a672d85757819a1a7257ecaa33e88b8d8bf07a1d60cdec1b612d02c34605cf8d",
    'ShopeeYeoShopid'       => "23423707",
    'ShopeeYeoPartnerID'    => "25181",
    'ShopeeYeoSecret'       => "108a24265afaf7b3561309ec9fd198124d40884deff000bbd1a278d3d3f16bbe",
    //F&N
    'ShopeeFNShopid'       =>  "40067702",
    'ShopeeFNPartnerID'    =>  "25653",
    'ShopeeFNSecret'       =>  "4764bd963a43beb4089260386c12e49e10540b6da7c03fd6ffa6a8eec352030f",
    //F&N
    //Pokka
    'ShopeePokkaShopid'     =>  "107829560",
    'ShopeePokkaPartnerID'  =>  "843643",
    'ShopeePokkaSecret'     =>  "a10ed8a2acc0850d82ae895c63f62b0f991774870aa8fb26d0df0194256a1412",
    //Kawan
    'ShopeeKawanShopid'     =>  "139008795",
    'ShopeeKawanPartnerID'  =>  "843654",
    'ShopeeKawanSecret'     =>  "a4a3c07ea5bd41ec7d78b8cf98f041924bd254dd0c95d9b353bec22e701d8112",
    //Nikudo
    'ShopeeNikudoShopid'     =>  "160303083",
    'ShopeeNikudoPartnerID'  =>  "843652",
    'ShopeeNikudoSecret'     =>  "216d1967b787c2e734b3e77283e1cab52e88d3347432dc9015a04644185203fc",
    //Oriental
    'ShopeeOrientalShopid'     =>  "107879244",
    'ShopeeOrientalPartnerID'  =>  "843655",
    'ShopeeOrientalSecret'     =>  "bc7d840f071352ccffbbe04f88690fe95a6ab0b2cea9598996ec050f60d5ea56",
    //Starbucks
    'ShopeeStarbucksShopid'     =>  "67984312",
    'ShopeeStarbucksPartnerID'  =>  "843653",
    'ShopeeStarbucksSecret'     =>  "18dc3836dac81404629aeb777c586474f2068ae2c8b741fc00b2dec8d3a3631d",
    //Etika
    'ShopeeEtikaShopid'     =>  "130887610",
    'ShopeeEtikaPartnerID'  =>  "845128",
    'ShopeeEtikaSecret'     =>  "537308686b7fb862a1f9a4c74b286ba29170aea859d5708ea557388bd273df54",
    
    //Ebfrozen
    'ShopeeEbfrozenShopid'     =>  "531110268",
    'ShopeeEbfrozenPartnerID'  =>  "2002883",
    'ShopeeEbfrozenSecret'     =>  "aa8e66d1ab3065189d3a4263dd72fc4cbaff207efe0c8f32807e4eb3ea836afa",
    
    //Ebfrozen
    'ShopeeEverbestShopid'     =>  "531046275",
    'ShopeeEverbestPartnerID'  =>  "2002884",
    'ShopeeEverbestSecret'     =>  "94dea2fa4423124a7a250cc75550ebc80b86ad08c00614f6f4a294e1ce51e47f",
    
    
    'ShopeeUrlGet'          => "https://partner.shopeemobile.com/api/v2/orders/get|",
    'ShopeeUrlDetail'       => "https://partner.shopeemobile.com/api/v2/orders/detail|",

    // TA Q BIN //
    'TA_Q_BIN_API_KEY' => 'aNJ?};~j9HN?f^2S',
    'TA_Q_BIN_API_URL_TEST' => 'http://www.taqbintms.com/ediapi/taqbinweightshipmentinfo-test.php',
    'TA_Q_BIN_API_URL_LIVE' => 'http://www.taqbintms.com/ediapi/taqbinweightshipmentinfo.php',
    'TA_Q_BIN_SUCCESS_CODE' => 'Data submitted successfully!',
    'TA_Q_BIN_TRACKING_URL' => 'http://statusedi.9625taqbin.com/gli_status/GSXST010X10Action_doStart.action',
    'TA_Q_BIN_TRACKING_USERID' => 'JOCOM401',
    'TA_Q_BIN_TRACKING_PASSWORD' => 'J8EHS8X468aH',


    // POP BOX //
    'POPBOX_PRODUCTION_ENV' => 'https://partnerapi.popbox.asia/',
    'POPBOX_DEVELOPMENT_ENV' => 'http://api-dev.popbox.asia/',
    'POPBOX_PRODUCTION_API_KEY' => '2adc529a94a58a5d164aa9c079149d3f26bf1f15',
    'POPBOX_DEVELOPMENT_API_KEY' => '76ae16139e8b3f0dc4b3d6409f3b2b3967b450ce',


    // LAZADA // 
    'LAZADA_API_USER_ID_DEVELOPMENT' => 'maruthu@jocom.my',
    //'LAZADA_API_USER_ID_PRODUCTION' => 'azwan.anuar@jocom.my',
    'LAZADA_API_USER_ID_PRODUCTION' => 'asif@jocom.my',
    'LAZADA_API_KEY_DEVELOPMENT' => '6iyquVo5iylJWbDGovLW-wUYR_22ymGe_2fbxcET9FfJ0q2p5VQodQny',
    //'LAZADA_API_KEY_PRODUCTION' => 'AEQKwnGkXuJiy9igAI4WgEKsh1YcAgfeX9OfrLDvm8VRrELatfqvkFzh',
    'LAZADA_API_KEY_PRODUCTION' => 'CnZGchtpi4kOG1L0wDVTEFCPVUEDoQQ9kmlJPQCPAY-GIyWvl7UQrmYO',
    
    'LAZADA_JOCOM_API_USER_ID' => 'joshua.sew@jocom.my',
    'LAZADA_JOCOM_API_KEY' => '86e1FXfylg6o_AsyaA6hwYZdMl_lBeeJd3gpWBg8lxNYNATFGVhpdfIn',
    
    'LAZADA_V2_AUTH_TOKEN_URL' => 'https://auth.lazada.com/rest',
    'LAZADA_V2_URL_PATH' => 'https://api.lazada.com.my/rest',
    
    
    'LAZADA_JOCOM_V2_APP_SECRET' => '1TNHEhOqpOACKuGn3LSgkrpKxtsw9fWV',
    'LAZADA_JOCOM_V2_APP_KEY' => '102479',
    
    'LAZADA_FNN_V2_APP_SECRET' => 'YDpR8UvqghAgd0oUJGW3g8SQqkm8mk07',
    'LAZADA_FNN_V2_APP_KEY' => '102485',
    
    'LAZADA_ETIKA_V2_APP_SECRET'  => '3cE4SnbyYxbIrn5l6zgbQUAsXAaOIKxV',
    'LAZADA_ETIKA_V2_APP_KEY' => '119183',
    
    'LAZADA_YEOS_V2_APP_SECRET'  => 'KxyXF9Op6XidjQjsIL4gUhvxdbTLif3i',
    'LAZADA_YEOS_V2_APP_KEY' => '119189',
    
    'LAZADA_STARBUCKS_V2_APP_SECRET'  => 'sRLa3HuaByWZkwFPZUBDZwzZsWJssB6T',
    'LAZADA_STARBUCKS_V2_APP_KEY' => '105651',
    
    'LAZADA_POKKA_V2_APP_SECRET'  => 'z6uBr6ersEZlbehHeetbTj005wc6sKhC',
    'LAZADA_POKKA_V2_APP_KEY' => '122505',
    
    'LAZADA_EBFROZEN_V2_APP_SECRET'  => 'nw5140h1GNGsggjb0udxWeKNYP7gbST9',
    'LAZADA_EBFROZEN_V2_APP_KEY' => '106062',
    
    'LAZADA_EVERBEST_V2_APP_SECRET'  => 'kdTbCZzK6IgaYdvVSMuskSB7zZ6VgHc7',
    'LAZADA_EVERBEST_V2_APP_KEY' => '106064',
    
    'LAZADA_JCMEXPRESS_V2_APP_SECRET'  => 'kWMJsnQWmzVoe5SzPz6RECQ0lXgbTktM',
    'LAZADA_JCMEXPRESS_V2_APP_KEY' => '106066',
        
    'LAZADA_API_ENV_DEVELOPMENT' => 'https://api.sellercenter.lazada.com.my',
    'LAZADA_API_ENV_PRODUCTION' => 'https://api.sellercenter.lazada.com.my',
    'LAZADA_API_ENV_PUSH_STATUS' => 'http://52.76.181.220/api/jocom.php',
    'LAZADA_PREFIX_TRACKING_NUMBER' => 'JLZD',
    
    'ACCOUNT_ADMIN' => array("agnes","joshua","rebecca","choong","maruthu","sclim","quenny","winnie","asif","tal","barry","queenie.wh","wenyi","huiyee"),
    'REPORT_GMV_GROUP' => array("agnes","joshua","choong","gladys","winnie","maruthu","sclim"),
    
    'REGION_SPECIAL_CODE' => array("0"),


    
    // MPAY PREPAID MASTERCARD
    'MPAY_PPEMASTERCARD_PARTNERID_DEV' => '100000000004560',
    'MPAY_PPEMASTERCARD_PARTNERKEY_DEV' => 'HGJKLMSOIONDUQWE123JDU456dIDSFHFI8J89223LKDJF',
    'MPAY_PPEMASTERCARD_PARTNER_DEV_WEBSERVICE' => 'https://uat.mpay.my/mpay/tpwalletapi/',
    
    'MPAY_PPEMASTERCARD_PARTNERID_PRO' => '100000000006780',
    'MPAY_PPEMASTERCARD_PARTNERKEY_PRO' => '6T5DT9KSYYRKD7T7UEMIC1DSL565V6TIYKG0EBOT8U4HR',
    'MPAY_PPEMASTERCARD_PARTNER_PRO_WEBSERVICE' => 'https://www.mpay.my/mpay/tpwalletapi/',
    
    // LINE CLEAR 
    'LINE_CLEAR_WEBSERVICE_DEV' => 'http://13.67.50.144:81/LineClear_Test/services/cust_ws_ver2.asmx',
    'LINE_CLEAR_CLIENTID_DEV' => 'Lineclear2017',
    'LINE_CLEAR_USERNAME_DEV' => 'Lineclear',
    'LINE_CLEAR_PASSWORD_DEV' => 'Lineclear@2017',
    'LINE_CLEAR_AGENTID_DEV' => 'AG001001',
    
    // BOOST API KEY
    'BOOST_API_KEY_PRO' => '0M2MPJ5G5QGK7UJH19UGTFKYOA',
    'BOOST_SECRET_KEY_PRO' => '95518739-596b-421e-8acb-e90c8720ba55',
    'BOOST_ENV_PRO' => 'https://wallet.boost-my.com',
    'BOOST_ENV_PRO_AUTHENTICATE' => 'https://wallet.boost-my.com',
    'BOOST_ENV_PRO_REDIRECT_URL' => 'https://api.jocom.com.my/boost/response/',
    'BOOST_ENV_PRO_CANCEL_URL' => 'https://api.jocom.com.my/boost/cancel/',
    'BOOST_API_KEY_DEV' => 'online-JOCOM MSHOPPING SDN BHD',
    //'BOOST_SECRET_KEY_DEV' => '3d9e6b22-c78e-470d-af25-1a3709275bab',
    'BOOST_SECRET_KEY_DEV' => 'bb4a6361-a03b-47e1-9c11-70a7cb3deac5',
    'BOOST_ENV_DEV' => 'https://stage-wallet.boostorium.com',
    'BOOST_ENV_DEV_AUTHENTICATE' => 'https://stage-wallet.boostorium.com',
    'BOOST_ENV_DEV_REDIRECT_URL' => 'http://uat.all.jocom.com.my/boost/response/',
    'BOOST_ENV_DEV_CANCEL_URL' => 'http://uat.all.jocom.com.my/boost/cancel/',
    // 'BOOST_MERCHANT_ID' => 'MCM0009793',
    'BOOST_MERCHANT_ID' => 'MCM0034395',
    
    'BOOST_MONDAY_CAMPAIGN_START_DATE' => '2019-04-22 00:00:00',
    'BOOST_MONDAY_CAMPAIGN_END_DATE' => '2019-06-30 23:59:59',
    
    'BOOST_BIGPOINT_CAMPAIGN_START_DATE' => '2019-05-01 00:00:00',
    'BOOST_BIGPOINT_CAMPAIGN_END_DATE' => '2019-05-19 23:59:59',
    
    //'LINE_CLEAR_WEBSERVICE_PRO' => 'http://13.67.50.144/lineclear_live/services/cust_ws_ver2.asmx',
    // http://lineclear.southeastasia.cloudapp.azure.com/LineClear/services/cust_ws_ver2.asmx
    'LINE_CLEAR_WEBSERVICE_PRO' => 'http://lineclear.southeastasia.cloudapp.azure.com/LineClear/services/cust_ws_ver2.asmx?op=PushOrderData_New', // http://lineclear.southeastasia.cloudapp.azure.com/LineClear/services/cust_ws_ver2.asmx?op=PushOrderData_New
    'LINE_CLEAR_CLIENTID_PRO' => 'Lineclear2017',
    'LINE_CLEAR_USERNAME_PRO' => 'Lineclear',
    'LINE_CLEAR_PASSWORD_PRO' => 'Lineclear@2017',
    'LINE_CLEAR_AGENTID_PRO' => 'B3100199',
    
    // PracBix
    'GRN_PDF_FILE_PATH' => 'media/pdf/grn',
    'EINV_PDF_FILE_PATH' => 'media/pdf/einv',

    'PBX_PO' => 'media/zip/po',
    'PBX_GRN' => 'media/zip/grn',
    'PBX_EINV' => 'media/txt/einv',
    
    'ECOM_BANNER_FILE_PATH' => 'images/banner/ecom',
    
    'DRIVER_TIME_SHEET' => 'media/pdf/drivertimesheet',
    
    //GRABPAY 
    'GRABPAY_ENV_PRO_URL'            => 'https://partner-api.grab.com',
    'GRABPAY_ENV_PRO_PARTNER_ID'     => '8ebf940e-92c1-493c-a517-4693099e5657', 
    'GRABPAY_ENV_PRO_PARTNER_SECRET' => 'bUfHoS8kNPUxSbC_',
    'GRABPAY_ENV_PRO_CLIENT_ID'      => '7e7d07f655b64fcca6257ffb8d5f3faa',
    'GRABPAY_ENV_PRO_CLIENT_SECRET'  => '_Rjy97nQtq8obtfv',
    'GRABPAY_ENV_PRO_MID'            => '9a80a2b8-eed3-4e70-a3f3-5355a8fe308f',
    'GRABPAY_ENV_PRO_REDIRECT'       => 'https://api.jocom.com.my/grabpay/redirect', 
    'GRABPAY_ENV_PRO_WEBHOOK'        => 'https://api.jocom.com.my/grabpay/webhook', 
    
    //FAVEPAY

    'FAVEPAY_ENV_PRO' => 'https://omni.myfave.com',
    'FAVEPAY_ENV_DEV' => 'https://omni.app.fave.ninja',
    'FAVEPAY_ENV_DEV_COUNTRY'  => 'MY',
    'FAVEPAY_ENV_DEV_PRIVATE_API_KEY'  => '2vmnx1wzjru082nn',
    'FAVEPAY_ENV_DEV_PREFIX'  => 'BD3',
    'FAVEPAY_ENV_DEV_OUTLET_ID'  => '21767',
    'FAVEPAY_ENV_DEV_APP_ID'  => 'm7v3wpd8wm',
    'FAVEPAY_ENV_DEV_REDIRECT_URL'  => 'https://uat.all.jocom.com.my/favepay/response',
    'FAVEPAY_ENV_DEV_CALLBACK_URL'  => 'https://uat.all.jocom.com.my/favepay/callback',

    'FAVEPAY_ENV_PRO_COUNTRY'  => 'MY',
    'FAVEPAY_ENV_PRO_PRIVATE_API_KEY'  => 'lz1kzndpec0khyvn',
    'FAVEPAY_ENV_PRO_PREFIX'  => 'MY9',
    'FAVEPAY_ENV_PRO_OUTLET_ID'  => '48897',
    'FAVEPAY_ENV_PRO_APP_ID'  => 'h0x76ewf10',
    'FAVEPAY_ENV_PRO_REDIRECT_URL'  => 'https://api.jocom.com.my/favepay/response',
    'FAVEPAY_ENV_PRO_CALLBACK_URL'  => 'https://api.jocom.com.my/favepay/callback',
    
    //PACEPAY
    'PACEPAY_ENV_PRO' => 'https://api.pacenow.co',
    'PACEPAY_ENV_DEV' => 'https://api-playground.pacenow.co',
    'PACEPAY_ENV_DEV_COUNTRY'  => 'MY',
    'PACEPAY_ENV_DEV_CLIENT_ID'  => 'pacetestmy',
    'PACEPAY_ENV_DEV_CLIENT_SECRET'  => 'sbx-pacetestmy-20d99621-2e9b-48',
    'PACEPAY_ENV_DEV_MERCHANT_ID'  => 'MYM00065',
    'PACEPAY_ENV_DEV_SUCCESS_URL'  => 'https://uat.all.jocom.com.my/pacepay/response',
    'PACEPAY_ENV_DEV_FAILED_URL'  => 'https://uat.all.jocom.com.my/pacepay/response',

    'PACEPAY_ENV_PRO_COUNTRY'  => 'MY',
    'PACEPAY_ENV_PRO_CLIENT_ID'  => '4ud4vdgd6dqdzjywbbexnm',
    'PACEPAY_ENV_PRO_CLIENT_SECRET'  => '23a206a2-e038-4080-92cd-1d4fd0a6f285',
    'PACEPAY_ENV_PRO_MERCHANT_ID'  => 'MYM00065',
    'PACEPAY_ENV_PRO_SUCCESS_URL'  => 'https://api.jocom.com.my/pacepay/response',
    'PACEPAY_ENV_PRO_FAILED_URL'  => 'https://api.jocom.com.my/pacepay/response',
    
    // WEBSITE API URL BASE
    'JOCOM_WEBAPI_BASE_DEV' => 'https://jocom.my/home/',
    'JOCOM_WEBAPI_BASE_PRO' => 'https://jocom.my/',
    
    // WAVPAY
	'WAVPAY_DEV' => [
		'url' => 'https://mapi-dev.wavpay.net',
		'name' => 'JOCOM MSHOPPING SDN BHD',
		'id' => 220927001,
		'key' => 'L0VqSCHFSNF2JBrlhZHrqoXSZfMKrPMY',
		'brikas' => [
			'url' => 'https://api.selangkah.my/dxapi_stage',
			'email' => 'dev@majucircle.com',
			'pass' => 'abcd1234',
		],
	],
	'WAVPAY_PRO' => [
		'url' => 'https://mapi.wavpay.net',
		'name' => 'JOCOM MSHOPPING SDN BHD',
		'id' => 221122001,
		'key' => 'SeuN28U2JUD9uZAuiv1zFTMsu3AhInRK',
		'brikas' => [
			'url' => 'https://api.selangkah.my/dxapi_stage',
			'email' => 'dev@majucircle.com',
			'pass' => 'abcd1234',
		],
	],
    
    //NINJAVAN 
    'NINJAVAN_ENV_PRO' => 'https://api.ninjavan.co',
    'NINJAVAN_ENV_PRO_CLIENT_ID' => '5c3cf0ec407844d28ec81365d1ea8534',
    'NINJAVAN_ENV_PRO_CLIENT_SECRET' => '92bb78bad465451195611c362309033c',

    'NINJAVAN_ENV_DEV' => 'https://api-sandbox.ninjavan.co',
    'NINJAVAN_ENV_DEV_CLIENT_ID' => 'a107e998f35042589a4d0b32f88cc3ec',
    'NINJAVAN_ENV_DEV_CLIENT_SECRET' => 'b780124e631c4a73aea0578c5d0f9973',
    
    'GDF_PDF_FILE_PATH' => 'media/pdf/gdf',
    'STOCK_TRANSFER_PDF_FILE_PATH' => 'media/pdf/stocktransfer',
    
    'MAILCHIMP_API_KEY' => 'f68b8923278d2c78949d500bf2b717de-us4',       
    'MAILCHIMP_REPORT_ENDPOINT' => 'https://us4.api.mailchimp.com/3.0/reports/', 
    
    'CSV_WAREHOUSE_STOCKIN' => 'media/csv/stockin',
);