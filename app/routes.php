---- routes.php ----
<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
Route::post('/generatedrivertimesheet', 'ApiController@generateDriverTimeSheet');
Route::post('/updatepricecost', 'ApiController@updatePriceCost');

Route::post('push/send', 'ApiController@push');
Route::post('push/queue/process', 'ApiController@processQueue');
Route::post('push/queue/delete', 'PushNotificationController@postDeleteQueue');
Route::post('push/device/register', 'ApiController@registerDevice');
Route::controller('push', 'PushNotificationController');

//Route::controller('/zone', 'ZoneController');
//Route::controller('/country', 'CountryController');
//Route::controller('/comment', 'CommentController');
//Route::controller('/user', 'UserController');
//Route::controller('/product/package', 'PackageController');
//Route::controller('/product/category', 'CategoryController');
//Route::controller('/product', 'ProductController');

// // -------------- Eugene
// Route::controller(Config::get('constants.ADMIN_FOLDER').'/transaction', 'TransactionController');
// Route::controller(Config::get('constants.ADMIN_FOLDER').'/coupon', 'CouponController');
// // --------------

// Facebook
Route::controller('facebook', 'FacebookController');

// Country Regions API
Route::post('api/country/regions', 'RegionController@getCoutryBasedRegionsApi');

// Logistics Truck Driver Values (API)
Route::post('api/truck-driver','ApiController@logisticsTruckDriverValues');

// Route Planner for driver
Route::get('/api/route-planner/driver', 'MapsController@routePlannerDriver');
Route::get('/api/route-planner/driver/route', 'MapsController@getRouteDriver');

// Flash Sale
Route::get('/api/jocommy/flashsale', 'ApiController@getAPIFlashsaleProduct');
Route::get('/api/jocommy/flashsale/productinfo', 'ApiController@getAPIFlashsaleProductInfo');

// jocom.my product info
Route::get('/api/jocommy/productinfo', 'ApiController@getAPIProductInfo');

// New Festival Campaigns
Route::resource('/campaigns/festival-campaigns', 'CampaignsController');
Route::get('/campaigns/festivals/get-data', ['uses'=>'CampaignsController@getCampaignData', 'as'=>'campaign.festival.data']);

//  API Requests - Festivals
Route::get('/api/campaigns/festival-campaigns', 'CampaignsController@fetchData');
Route::get('/api/campaigns/festival-campaigns/{id}', 'CampaignsController@fetchDetails');


// Keywords Setup Routes
Route::get('/sysadmin/address-keywords', 'KeywordsController@getAddressIndex');
Route::get('/sysadmin/address-keywords/get-data', 'KeywordsController@getAddressData');
Route::get('/sysadmin/address-keywords-create', 'KeywordsController@createKeyword');
Route::post('/sysadmin/address-keywords-store', 'KeywordsController@storeKeyword');
Route::get('/sysadmin/address-keywords-edit/{id}', ['uses' => 'KeywordsController@editKeyword', 'as' => 'keyword.edit']);
Route::put('/sysadmin/address-keywords-update/{id}', ['uses' => 'KeywordsController@updateKeyword', 'as' => 'keyword.update']);
Route::post('/sysadmin/address-keywords-delete', ['uses' => 'KeywordsController@deleteKeyword', 'as' => 'keyword.delete']);


// Warehouse - Fresh Inventory Purchase Management
Route::get('/warehouse/sorted/purchase-todos', ['uses' => 'InventoryManagerController@sortedIndex', 'as' => 'sorted.fresh.inventory']);
Route::get('/warehouse/sorted/transactions', ['uses' => 'InventoryManagerController@getSortedTransactions', 'as' => 'sorted.transactions']);
Route::get('/warehouse/purchase-requests-list/{id}', ['uses' => 'InventoryManagerController@purchaseList', 'as' => 'purchase.list']);
// Route::put('/warehouse/sorted/purchase-todos/{sort_id}/{product_id}', ['uses' => 'InventoryManagerController@updateListTask', 'as' => 'update.purchaseList']);
Route::post('/warehouse/sorted/purchase-todos-update', ['uses' => 'InventoryManagerController@updateListTask', 'as' => 'update.purchaseList']);
Route::get('/warehouse/fresh-inventory-history', ['uses' => 'InventoryManagerController@getFreshInventoryHistory', 'as' => 'fresh.inventory.history']);
Route::get('/warehouse/fresh-inventory-history-data', ['uses' => 'InventoryManagerController@historyOfPurchaseRequests', 'as' => 'fresh.inventory.history.data']);


$id = Session::get('role_id');

Route::get('/states', function() {
	$type 	= (Input::has('country_id')) ? "all" : "delivery";

    if ($type == "all") {
    	$country_id = Input::get('country_id');
	    $cust       = new Customer;
	    $states     = $cust->getStateList($country_id);
    } else {
    	$country_id = Input::get('delivercountry');
	    $delivery   = new Delivery;
	    $states     = $delivery->getStateList($country_id);
    }


    return Response::json($states);
});

Route::get('/cities', function() {
	$type 	= (Input::has('state_id')) ? "all" : "delivery";
	//echo "<br>Type: ".$type;

	if ($type == "all") {
		$state_id   = Input::get('state_id');
	    $cust       = new Customer;
	    $cities     = $cust->getCityList($state_id);
	} else {
		$state_id   = Input::get('state');
	    $delivery   = new Delivery;
	    $cities     = $delivery->getCityList($state_id);
	}

    return Response::json($cities);
});

Route::get('/places', function() {
    if (Input::has('country_id')) {
        $country_id = Input::get('country_id');
        $cust           = new Customer;
        $result         = $cust->getStateList($country_id);
    }

    if (Input::has('state_id')) {
        $state_id   = Input::get('state_id');
        $zone       = new Zone;
        $result     = $zone->GetCities($state_id);
    }

    if (Input::has('country_name')) {
        $country_name   = Input::get('country_name');
        $cust           = new Customer;
        $result         = $cust->getStateListName($country_name);
    }

    if (Input::has('state_name')) {
        $state_name = Input::get('state_name');
        $cust       = new Customer;
        $result     = $cust->getCityListName($state_name);
    }

    if (Input::has('city_name')) {
        $city_name  = Input::get('city_name');
        $cust       = new Customer;
        $result     = $cust->getLocalityListName($city_name);
    }

    // var_dump(json_encode($result));
    return Response::json($result);
});

// 14/04/2022 - User permission
Route::get('/refund/user-list', 'RefundController@userList');
Route::get('/refund/fetch-user', 'RefundController@ajaxFetchUser');
Route::get('/refund/userPermission', 'RefundController@userPermission');
Route::get('/refund/permission/edit/{id}', 'RefundController@editPermission');
Route::get('/refund/permission/delete/{id}', 'RefundController@deletePermission');
// 14/04/2022 - User permission


// API VERSION 2 CONTROLLER ROUTES(since: 01/08/22) //////////////////////////////

Route::post('/api/v2/generatedrivertimesheet', 'ApiV2Controller@generateDriverTimeSheet');
Route::post('/api/v2/updatepricecost', 'ApiV2Controller@updatePriceCost');

Route::post('/api/v2/push/send', 'ApiV2Controller@push');
Route::post('/api/v2/push/queue/process', 'ApiV2Controller@processQueue');
Route::post('/api/v2/push/device/register', 'ApiV2Controller@registerDevice');

// Logistics Truck Driver Values (API)
Route::post('api/v2/truck-driver','ApiV2Controller@logisticsTruckDriverValues');

// Flash Sale
Route::get('/api/v2/jocommy/flashsale', 'ApiV2Controller@getAPIFlashsaleProduct');
Route::get('/api/v2/jocommy/flashsale/productinfo', 'ApiV2Controller@getAPIFlashsaleProductInfo');

// jocom.my product info
Route::get('/api/v2/jocommy/productinfo', 'ApiV2Controller@getAPIProductInfo');

Route::controller('/api/v2', 'ApiV2Controller');

Route::post('/api/v2/productsku', 'ApiV2Controller@Productsku');
Route::post('/api/v2/cartitems', 'ApiV2Controller@Cartitems');

//API LOGISTIC CONTROLLER VERSION 2
Route::controller('/apilogistic/v2', 'ApiV2LogisticController');


//API USER CONTROLLER VERSION 2
Route::controller('v2/api/user', 'ApiV2UserController');

//API CHECKOUT CONTROLLER VERSION 2
Route::controller('/v2/checkout', 'CheckoutV2Controller');

//API CONVERSION CONTROLLER VERSION 2
Route::controller('/v2/api/conversion', 'ApiV2ConversionController');

//API LOCATION CONTROLLER VERSION 2
Route::controller('/v2/api/location', 'ApiV2LocationController');


//API POINT CONTROLLER VERSION 2
Route::controller('/v2/api/point', 'ApiV2PointController');

//APP CONTROLLER VERSION 2    NEED TO ADD IN SYSTEM GROUP ARRAY ROUTE

//FEEDBACK CONTROLLER VERSION 2
Route::controller('/v2/feedback' , 'FeedbackV2Controller');


//FEED CONTROLLER VERSION 2
Route::controller('/v2/feed', 'FeedV2Controller');


// Country Regions API ERSION 2
Route::post('/v2/api/country/regions', 'RegionV2Controller@getCoutryBasedRegionsApi');

// REGION CONTROLLER API VERSION 2
Route::get('/region/v2/', 'RegionV2Controller@index');
Route::get('/region/v2/list', 'RegionV2Controller@regionList');
Route::get('/region/v2/create', 'RegionV2Controller@createRegion');
Route::get('/region/v2/edit/{id}', 'RegionV2Controller@editRegion');
Route::post('/region/v2/states', 'RegionV2Controller@getStateByCountry');
Route::post('/region/v2/save', 'RegionV2Controller@saveRegion');
Route::get('/region/v2/reporttransaction', 'RegionV2Controller@transactionregion');
Route::post('/region/v2/country', 'RegionV2Controller@getRegionByCountry');
Route::post('/region/v2/available', 'RegionV2Controller@getAvailableRegion');

//Helpcenter Management
Route::controller('/helpcenter', 'HelpCenterController');

//END API VERSION 2 ROUTES


Route::group(array('before' => 'common'), function()
{
	Route::group(array('before' => 'shipping'), function()
	{
		Route::controller('/zone', 'ZoneController');
		Route::controller('/country', 'CountryController');
	});

	Route::group(array('before' => 'product'), function()
	{
		// -------------- KHAIRUL
		Route::controller('/product/package', 'PackageController');
        Route::get('product/category/charityproduct/add/{id}', 'CharityProductController@getAdd');
        Route::get('product/category/charityproduct/addoption/{id}', 'CharityProductController@getAddoption');
        Route::post('product/category/charityproduct/addoption/{id}', 'CharityProductController@postAddoption');
        Route::get('product/category/charityproduct/datatable', 'CharityProductController@getDatatable');
        Route::get('product/category/charityproduct/labeldatatable/{id}', 'CharityProductController@getProductLabelDatatable');
        Route::resource('product/category/charityproduct', 'CharityProductController', [
            'only' => ['show', 'update', 'destroy'],
        ]);
        Route::controller('/product/category', 'CategoryController');
		Route::controller('/product', 'ProductController');
		Route::controller('/inventory', 'InventoryController');
	});

	Route::group(array('before' => 'banner'), function()
	{
		// -------------- RoaYu
		Route::controller('/banner', 'BannerController');
	});

	Route::group(array('before' => 'latestnews'), function()
	{
		// -------------- RoaYu
		Route::controller('/latestnews', 'LatestnewsController');
	});

	Route::group(array('before' => 'hotitem'), function()
	{
		// -------------- RoaYu
		Route::controller('/hot_item', 'HotItemController');
	});
	Route::group(array('before' => 'branditem'), function()
	{
		// -------------- RoaYu
		Route::controller('/brands', 'BrandItemController');
	});

	Route::group(array('before' => 'transaction'), function()
	{
		// -------------- Eugene
		Route::controller('/transaction', 'TransactionController');
	    Route::controller('/coupon', 'CouponController');
	});

	Route::group(array('before' => 'seller'), function()
	{
		// -------------- RoaYu
		Route::controller('/seller', 'SellerController');
	});

	Route::group(array('before' => 'customer'), function()
	{
		// -------------- RoaYu
		Route::controller('/customer', 'CustomerController');
	});

	Route::group(array('before' => 'comment'), function()
	{
		// -------------- RoaYu
		Route::controller('/comment', 'CommentController');
	});

	Route::group(array('before' => 'specialprice'), function()
	{
		// -------------- RoaYu
		Route::controller('/special_price/customer', 'SpecialcustomerController');
		Route::controller('/special_price/group', 'SpecialgroupController');
		Route::controller('/special_price', 'SpecialpriceController');
	});

	Route::group(array('before' => 'logistic'), function()
	{
		Route::controller('/jlogistic', 'LogisticController');
		Route::get('/cronmissingtransaction', 'LogisticController@anyCronMissingTransaction');
		Route::get('/pendingorders', 'LogisticController@anyPendingOrders');
		Route::get('/download{attachment}', 'LogisticController@getDownload');
		Route::controller('/batch', 'BatchController');
		Route::controller('/driver', 'DriverController');
		
		// Drivers Maps Simulation implementation
		Route::get('/api/get-drivers', ['uses'=>'MapsController@getDrivers', 'as' => 'get-drivers']);
		Route::get('/driver-locations', ['uses'=>'MapsController@getDriverGpsView', 'as' => 'get-drivers-location']);
		Route::get('/driver-get-location-data', ['uses'=>'MapsController@getDriverLocationsData', 'as' => 'get-drivers-location-details']);
		Route::get('route-planner', 'MapsController@routePlanner');
		Route::get('route-planner/route/{driver_id}', 'MapsController@getRoute');
	});

	Route::group(array('before' => 'general_report'), function()
	{
		Route::controller('/report', 'ReportController');
		// Route::get('report/product', 'ReportController@product');
		// Route::controller('/batch', 'BatchController');
		// Route::controller('/driver', 'DriverController');
	});

	Route::group(array('before' => 'sysadmin'), function()
	{
		// -------------- RoaYu
		Route::controller('/sysadmin/user', 'UserController');
		Route::controller('/sysadmin/role', 'RoleController');
		Route::controller('/sysadmin/permission', 'PermissionController');
		Route::controller('/sysadmin/app', 'AppController');
		Route::controller('/sysadmin/appnewlogistic', 'AppnewLogisticController');
		Route::controller('/sysadmin/appnew', 'AppnewController');
		Route::controller('/sysadmin/reward', 'RewardController');
		
		Route::controller('/sysadmin/indvPermission', 'IndvPermissionController'); // Sub Module Permission
		
        //APP CONTROLLER VERSION 2    NEED TO ADD IN SYSTEM GROUP ARRAY ROUTE
        Route::controller('/sysadmin/v2/app', 'AppV2Controller');

		// -------------- Eugene
		Route::controller('/fees', 'FeesController');
	});

	Route::group(array('before' => 'refund'), function()
	{
		Route::controller('/refund', 'RefundController');
	});

	Route::group(array('before' => 'charity'), function()
	{
		Route::controller('/charity/user', 'CharityUserController');
	});
	
	Route::group(array('before' => 'case'), function()
    {
        // Task Module
        Route::get('/task', 'TaskController@index');
        Route::any('/task/create', 'TaskController@create');
        Route::any('/task/report', 'TaskController@report');
        Route::any('/task/details/{id}', 'TaskController@details');
        Route::post('/task/save', 'TaskController@saveTask');
        Route::post('/task/update', 'TaskController@updateTask');
        Route::get('/task/inbox/{type}', 'TaskController@tasks');
        Route::get('/task/category', 'TaskController@getCategory');
        Route::get('/task/admin', 'TaskController@getSysadmin');
        Route::post('/task/statusupdate', 'TaskController@updateTaskStatus');
        Route::post('/task/orderinfo', 'TaskController@getTransactionInfo');
        Route::post('/task/message/save', 'TaskController@saveMessage');
        Route::post('/task/message', 'TaskController@getMessage');
        Route::post('/task/reportprev', 'TaskController@reportprev');
        Route::post('/task/reportgen', 'TaskController@reportGenerate');
        Route::post('/task/updateLogiTrans', 'TaskController@updateLogiTrans');
        Route::post('/task/assignTo', 'TaskController@postAssignTo');
        
    });
    
    Route::group(array('before' => 'jocommy'), function()
	{
		Route::controller('/jocommy', 'JocomMyController');

	});
	
	Route::controller('home', 'HomeController');
	Route::controller('dashboard', 'DashboardController');
	Route::controller('platforms', 'PlatformController');
	
	// New Sales Dashboard Routes
	Route::get('/get-platforms-data', 'HomeController@getPlatformSales');
	Route::get('/get-platforms-percent', 'HomeController@getPlatformSalesPercent');
	Route::get('/get-platforms-compare-data', 'HomeController@getPlatformsCompareChartData');
	Route::get('/get-daily-transactions', 'HomeController@getDailyTransactions');
	Route::get('/get-monthly-transactions', 'HomeController@getMonthlyTransactions');
	Route::get('/top-products-last-week', 'HomeController@getTopProductsLastWeek');
	Route::get('/top-products-this-week', 'HomeController@getTopProductsThisWeek'); // Nadzri (28-09-2022)
	Route::get('/top-products-categories', 'HomeController@getTopProductsCategories');
	Route::get('/mobile-platforms', 'HomeController@getMobilePlatformValues');
	Route::get('/top-regions', 'HomeController@getTopRegions');
	Route::get('/top-undelivered-items', 'HomeController@getTopUndeliveredItems');
	Route::get('/top-undelivered-items-0-stock', 'HomeController@getTopUndeliveredZeroStockItems');
});

Route::get('points/customers/datatables', 'PointCustomerController@datatables');
Route::get('points/conversions/datatables', 'PointConversionController@datatables');
Route::get('points/customers/active-check', 'PointCustomerController@activeCheck');
Route::get('points/customers/refund-check', 'PointCustomerController@refundCheck');
Route::get('points/bcard/request-response', 'PointController@requestResponse');
Route::post('points/bcard/void', 'PointController@void');
Route::get('points/bcard/datatables', 'PointController@bcardDatatables');
Route::get('points/bcard', 'PointController@bcard');
Route::get('points/bcard/create', 'PointController@bcardcreate');
Route::post('points/bcard/store', 'PointController@bcardstore');
Route::get('points/datatables', 'PointController@datatables');
Route::resource('points/customers', 'PointCustomerController', [
    'only' => ['index', 'show', 'update'],
]);
Route::resource('points/conversions', 'PointConversionController', [
    'only' => ['index'],
]);
Route::resource('points', 'PointController', [
    'except' => ['edit'],
]);

Route::get('agents/datatables', 'AgentController@datatables');
Route::resource('agents', 'AgentController', [
    'except' => ['show'],
]);

Route::controller('/api/bcard', 'ApiBCardController');
Route::controller('/api/logi-dash', 'ApiLogisticDashboardController');
Route::controller('/api/conversion', 'ApiConversionController');
Route::controller('/api/point', 'ApiPointController');
Route::controller('/api/checkproduct', 'ApiCheckProductController');
Route::controller('/api/user', 'ApiUserController');
Route::controller('/api/location', 'ApiLocationController');
Route::controller('/api/inventory', 'ApiInventoryController');
Route::controller('/feed', 'FeedController');
Route::controller('/api', 'ApiController');
Route::controller('/api/analytics', 'AnalyticsController');
Route::controller('/attendance', 'ApiAttendanceController');
Route::get('/attendance/timeout', 'ApiAttendanceController@getTimeout');
Route::get('/attendance/generate', 'ApiAttendanceController@getGenerate');
Route::controller('/tracking', 'TrackingController');

Route::controller('/product_update', 'ProductUpdateController');

Route::controller('/pointcheckout', 'PointCheckoutController');

Route::post('/api/productsku', 'ApiController@Productsku');
Route::post('/api/cartitems', 'ApiController@Cartitems');

//YeeHao
Route::controller('/checkoutlite', 'CheckoutLiteController');

//Eugene
Route::controller('/checkout', 'CheckoutController');
Route::controller('/apilogistic', 'ApiLogisticController');
Route::controller('/tnc', 'TncController');
Route::controller('/gstreport', 'GstController');
Route::controller('/account', 'AccountController');
Route::controller('/automate', 'AutomateController');

Route::get('/process/report/{id?}', 'ReportController@process_report');
Route::get('/process/cancel/{id?}', 'ReportController@cancel_report');

// Google Analytics
Route::get('/analytics/google/{id?}', 'AnalyticsController@google_analytics');
Route::post('/analytics/mostrevieweditem', 'AnalyticsController@getTop10ViewedItems');

Route::controller('test', 'TestController');

Route::controller('/product_insert', 'ImportProductController');

Route::controller('/apicharity', 'ApiCharityController');

// temporary function to insert previous order of JIT
Route::controller('/jit_insert', 'InsertJITController');

// temporary function to move product category
Route::controller('/temp_category', 'TempCategoryController');


// 11Street route
//Route::controller('/eleven', 'ElevenStreetController');
Route::get('/eleven', 'ElevenStreetController@index')->before('third_party_platform');
//Route::get('/eleven/migrate/{migratefrom}', 'ElevenStreetController@migrateOrders');
Route::post('/eleven/migrate', 'ElevenStreetController@migrateOrders');
Route::get('/eleven/orders', 'ElevenStreetController@Orders');
Route::post('/eleven/orders/import', 'ElevenStreetController@importOrderByCSV2')->before('third_party_platform');
Route::post('/eleven/revert', 'ElevenStreetController@revertOrderStatus');
Route::get('/transaction/add/{order_id}', 'TransactionController@Add');
Route::get('/eleven/batch', 'ElevenStreetController@batchGenerate');
Route::get('/eleven/freshreporting', 'ElevenStreetController@createFreshreporting');
Route::post('/eleven/migratesingle', 'ElevenStreetController@migrateSingleOrder');
Route::get('/eleven/createpdf', 'ElevenStreetController@createpdf');
Route::get('/eleven/createpdfeinv', 'ElevenStreetController@createpdfeinv');
Route::get('/eleven/topspender', 'ElevenStreetController@topWinner');
Route::get('/eleven/updateemail', 'ElevenStreetController@updateEmail');
Route::get('/eleven/testcode', 'ElevenStreetController@testpartcode');

//qoo10
Route::controller('/qoo10' , 'Qoo10Controller');
Route::get('/qoo10/batch', 'Qoo10Controller@batchGenerate');
Route::get('/qoo10/batch2', 'Qoo10Controller@batchGenerate2');

//shopee
Route::controller('/shopee' , 'ShopeeController');
Route::get('/shopeemanual', 'ShopeeManualController@index');
Route::get('/shopeemanual/orders', 'ShopeeManualController@orders');
Route::post('/shopeemanual/upload', 'ShopeeManualController@upload');

//pgmall

Route::controller('/pgmall' , 'PGMallController');

//feedback
Route::controller('/feedback' , 'FeedbackController');
// Route::controller('/jocommy', 'JocomMyController');

//report template

Route::controller('/reporttemplate' , 'ReportTemplateController');
// CAMPAIGN
Route::post('/campaign/product', 'CampaignController@getCampaignProduct');
Route::post('/campaign/addproduct', 'CampaignController@addCampaignProduct');
Route::post('/campaign/removeproduct', 'CampaignController@removeCampaignProduct');
Route::get('/campaign/1', 'CampaignController@getAPICampaignProduct');
Route::post('/campaign/move', 'CampaignController@moveProductOrder');
Route::post('/campaign/productinfo', 'CampaignController@getAPICampaignProductInfo');
Route::post('/campaign/campaignupdate', 'CampaignController@getCampaignUpdate');
Route::post('/campaign/saveseq', 'CampaignController@saveseq');
Route::get('/campaign/voucher', 'CampaignController@getAPIVoucherProduct');
Route::post('/campaign/voucherproductinfo', 'CampaignController@getAPIVoucherProductInfo');

// LIVE STREAMING
Route::post('/livestream/product', 'CampaignLiveController@getCampaignProduct');
Route::post('/livestream/addproduct', 'CampaignLiveController@addCampaignProduct');
Route::post('/livestream/removeproduct', 'CampaignLiveController@removeCampaignProduct');
Route::get('/livestream/1', 'CampaignLiveController@getAPICampaignProduct');
Route::post('/livestream/move', 'CampaignLiveController@moveProductOrder');
Route::post('/livestream/productinfo', 'CampaignLiveController@getAPICampaignProductInfo');
Route::post('/livestream/campaignupdate', 'CampaignLiveController@getCampaignUpdate');
Route::post('/livestream/saveseq', 'CampaignLiveController@saveseq');

// BOOST DEALS
Route::post('/boostdeals/product', 'CampaignBoostController@getCampaignProduct');
Route::post('/boostdeals/addproduct', 'CampaignBoostController@addCampaignProduct');
Route::post('/boostdeals/removeproduct', 'CampaignBoostController@removeCampaignProduct');
Route::get('/boostdeals/1', 'CampaignBoostController@getAPICampaignProduct');
Route::post('/boostdeals/move', 'CampaignBoostController@moveProductOrder');
Route::post('/boostdeals/productinfo', 'CampaignBoostController@getAPICampaignProductInfo');
Route::post('/boostdeals/campaignupdate', 'CampaignBoostController@getCampaignUpdate');
Route::post('/boostdeals/saveseq', 'CampaignBoostController@saveseq');

// BOOST 11.11 DEALS
Route::post('/boost11deals/product', 'CampaignBoost11Controller@getCampaignProduct');
Route::post('/boost11deals/addproduct', 'CampaignBoost11Controller@addCampaignProduct');
Route::post('/boost11deals/removeproduct', 'CampaignBoost11Controller@removeCampaignProduct');
Route::get('/boost11deals/1', 'CampaignBoost11Controller@getAPICampaignProduct');
Route::post('/boost11deals/move', 'CampaignBoost11Controller@moveProductOrder');
Route::post('/boost11deals/productinfo', 'CampaignBoost11Controller@getAPICampaignProductInfo');
Route::post('/boost11deals/campaignupdate', 'CampaignBoost11Controller@getCampaignUpdate');
Route::post('/boost11deals/saveseq', 'CampaignBoost11Controller@saveseq');

// JOCOM 11.11 DEALS
Route::post('/jocom11deals/product', 'CampaignJocom11Controller@getCampaignProduct');
Route::post('/jocom11deals/addproduct', 'CampaignJocom11Controller@addCampaignProduct');
Route::post('/jocom11deals/removeproduct', 'CampaignJocom11Controller@removeCampaignProduct');
Route::get('/jocom11deals/1', 'CampaignJocom11Controller@getAPICampaignProduct');
Route::post('/jocom11deals/move', 'CampaignJocom11Controller@moveProductOrder');
Route::post('/jocom11deals/productinfo', 'CampaignJocom11Controller@getAPICampaignProductInfo');
Route::post('/jocom11deals/campaignupdate', 'CampaignJocom11Controller@getCampaignUpdate');
Route::post('/jocom11deals/saveseq', 'CampaignJocom11Controller@saveseq');

// JOCOM FEATURED PRODUCTS 
Route::post('/jocomfeatured/product', 'JocomFeaturedController@getCampaignProduct');
Route::post('/jocomfeatured/addproduct', 'JocomFeaturedController@addCampaignProduct');
Route::post('/jocomfeatured/removeproduct', 'JocomFeaturedController@removeCampaignProduct');
Route::get('/jocomfeatured/1', 'JocomFeaturedController@getAPICampaignProduct');
Route::post('/jocomfeatured/move', 'JocomFeaturedController@moveProductOrder');
Route::post('/jocomfeatured/productinfo', 'JocomFeaturedController@getAPICampaignProductInfo');
Route::post('/jocomfeatured/campaignupdate', 'JocomFeaturedController@getCampaignUpdate');
Route::post('/jocomfeatured/saveseq', 'JocomFeaturedController@saveseq');


// Officepantry
Route::post('/officepantry/product', 'OfficepantryController@getCampaignProduct');
Route::post('/officepantry/addproduct', 'OfficepantryController@addCampaignProduct');
Route::post('/officepantry/removeproduct', 'OfficepantryController@removeCampaignProduct');
Route::get('/officepantry/1', 'OfficepantryController@getAPICampaignProduct');
Route::post('/officepantry/move', 'OfficepantryController@moveProductOrder');
Route::post('/officepantry/productinfo', 'OfficepantryController@getAPICampaignProductInfo');

// Crossborder
Route::post('/crossborder/product', 'CrossborderController@getCampaignProduct');
Route::post('/crossborder/addproduct', 'CrossborderController@addCampaignProduct');
Route::post('/crossborder/removeproduct', 'CrossborderController@removeCampaignProduct');
Route::get('/crossborder/1', 'CrossborderController@getAPICampaignProduct');
Route::post('/crossborder/move', 'CrossborderController@moveProductOrder');
Route::post('/crossborder/productinfo', 'CrossborderController@getAPICampaignProductInfo');

// Boost Online store
Route::post('/booststore/product', 'BoostOnlinestoreController@getCampaignProduct');
Route::post('/booststore/productnew', 'BoostOnlinestoreController@getCampaignProductNew');
Route::post('/booststore/addproduct', 'BoostOnlinestoreController@addCampaignProduct');
Route::post('/booststore/removeproduct', 'BoostOnlinestoreController@removeCampaignProduct');
Route::get('/booststore/{campaign_id}', 'BoostOnlinestoreController@getAPICampaignProduct');
Route::post('/booststore/move', 'BoostOnlinestoreController@moveProductOrder');
Route::post('/booststore/productinfo', 'BoostOnlinestoreController@getAPICampaignProductInfo');

// eCommunity store
Route::post('/ecommunity/product', 'ECommunityController@getCampaignProduct');
Route::post('/ecommunity/addproduct', 'ECommunityController@addCampaignProduct');
Route::post('/ecommunity/removeproduct', 'ECommunityController@removeCampaignProduct');
Route::get('/ecommunity/{campaign_id}', 'ECommunityController@getAPICampaignProduct');
Route::post('/ecommunity/move', 'ECommunityController@moveProductOrder');
Route::post('/ecommunity/productinfo', 'ECommunityController@getAPICampaignProductInfo');
Route::post('/ecommunity/campaignupdate', 'ECommunityController@getCampaignUpdate');
Route::post('/ecommunity/saveseq', 'ECommunityController@saveseq');

// MyCashOnline store
Route::post('/mycashonline/product', 'MyCashController@getCampaignProduct');
Route::post('/mycashonline/productnew', 'MyCashController@getCampaignProductNew');
Route::post('/mycashonline/addproduct', 'MyCashController@addCampaignProduct');
Route::post('/mycashonline/removeproduct', 'MyCashController@removeCampaignProduct');
Route::get('/mycashonline/{campaign_id}', 'MyCashController@getAPICampaignProduct');
Route::post('/mycashonline/move', 'MyCashController@moveProductOrder');
Route::post('/mycashonline/productinfo', 'MyCashController@getAPICampaignProductInfo');

Route::controller('/flashsale' , 'FlashSaleController');
Route::controller('/jocomexccorner' , 'JocomExcCornerController');
Route::controller('/jcmcombodeals' , 'JocomComboDealsController');
Route::controller('/jcmdynamicsale' , 'DynamicSaleController');

Route::controller('/bannertemplate', 'BannerTemplateController');

Route::post('/services/create', 'LogisticServiceController@saveOrderDelivery');
Route::post('/services/createinternational', 'LogisticServiceController@saveOrderDeliveryInternational');
Route::post('/services/servicelist', 'LogisticServiceController@getListOrderService');
Route::post('/services/checkstatus', 'LogisticServiceController@checkDeliveryStatus');
Route::post('/services/search', 'LogisticServiceController@searchService');
Route::get('/services/download/{id}', 'LogisticServiceController@download');
Route::post('/services/shipper', 'LogisticServiceController@getShipperInformation');
Route::post('/services/updateshipper', 'LogisticServiceController@updateShipper');
Route::post('/services/manifestist', 'LogisticServiceController@getListManifestService');
Route::get('/services/downmanifest/{manifestNumber}', 'LogisticServiceController@getDownloadmanifestbyid');
Route::get('/services/stat', 'LogisticServiceController@getStatisticmanifest');

Route::post('/taqbin/create', 'TaQBinController@createWayBill');



// SHIPPER ASSIGN
Route::post('/taqbin/create', 'TaQBinController@createWayBill');
Route::post('/shipper/assign', 'TaQBinController@assignShiping');

Route::get('/courier', 'TaQBinController@courier');
Route::get('/courier/list', 'TaQBinController@courierList');
Route::get('/courier/slip/{order_id}', 'TaQBinController@taqbinslip');
Route::get('/courier/tracking', 'TaQBinController@trackOrder');


// REGION 
Route::get('/region', 'RegionController@index');
Route::get('/region/list', 'RegionController@regionList');
Route::get('/region/create', 'RegionController@createRegion');
Route::get('/region/edit/{id}', 'RegionController@editRegion');
Route::post('/region/states', 'RegionController@getStateByCountry');
Route::post('/region/save', 'RegionController@saveRegion');
Route::get('/region/reporttransaction', 'RegionController@transactionregion');
Route::post('/region/country', 'RegionController@getRegionByCountry');
Route::post('/region/available', 'RegionController@getAvailableRegion');

Route::post('/popbox/locker', 'PopboxController@getLockerLocation');


// LAZADA //
Route::get('/lazada', 'LazadaController@index');
Route::post('/lazada/migrate', 'LazadaController@migrateOrders');
Route::get('/lazada/orders', 'LazadaController@Orders');
Route::get('/lazada/batch', 'LazadaController@batchGenerate');
Route::get('/lazada/lazadastatus', 'LazadaController@LazadaStatus'); 
Route::post('/lazada/migratev2', 'LazadaController@migrateOrdersV2');
Route::post('/lazada/getauthtoken', 'LazadaController@getLazadaV2AuthToken');
Route::get('/lazada/authcallback/{appcode}', 'LazadaController@authcallback');
Route::get('/lazada/newgetrisk', 'LazadaController@newgetrisk'); 
Route::get('/lazada/sofdelivered/{logistic_id}', 'LazadaController@sofdelivered'); 
Route::get('/lazada/dbslazada', 'LazadaController@dbslazada');
Route::controller('lazadaauto', 'LazadaAutoController');

Route::get('/lazada/unmask', 'LazadaController@unmasktest');
Route::get('/lazada/compute', 'LazadaController@computerisk');

Route::get('/lazada/resettoken', 'LazadaController@expireLazadaToken');
Route::get('/lazada/lazadapriceupdate', 'LazadaController@lazadapriceupdate');
Route::get('/lazada/priceupdate', 'LazadaController@priceupdate');
Route::get('/lazada/lazadanewpriceupdate', 'LazadaController@lazadanewpriceupdate');
Route::get('/lazada/lazadadiscupdate', 'LazadaController@lazadadiscupdate');

// NOTIFICATION //
Route::get('/notification/list', 'NotificationController@getNotification');
Route::post('/notification/nextlist', 'NotificationController@getNextNotification');

// LeaderBoard
Route::get('/board', 'LeaderboardController@index');
Route::get('/boardlist', 'LeaderboardController@application');
Route::post('/board/save', 'LeaderboardController@submission');
Route::get('/board/list', 'LeaderboardController@getBoardlist');
Route::post('/board/approve', 'LeaderboardController@approve');

// MPAY PREPAID MASTERCARD

Route::get('/mpay/test', 'MpayPrepaidController@testmpay');
Route::get('/mpay', 'MpayPrepaidController@index');
Route::get('/mpay/list', 'MpayPrepaidController@cards'); //
Route::get('/mpay/country', 'MpayPrepaidController@getMpayCountry');
Route::get('/mpay/state', 'MpayPrepaidController@getMpayState');
Route::post('/mpay/create', 'MpayPrepaidController@createAccount');
Route::post('/mpay/balance', 'MpayPrepaidController@getBalance');
Route::post('/mpay/update/mail', 'MpayPrepaidController@updateMailStatus');
Route::post('/mpay/card/info', 'MpayPrepaidController@getCardInformation');
Route::post('/mpay/card/changepin', 'MpayPrepaidController@setChangePIN');
Route::post('/mpay/card/virtualcard', 'MpayPrepaidController@getVirtualCardNumber');
Route::post('/mpay/card/topup', 'MpayPrepaidController@getTopup');
Route::post('/mpay/card/enroll', 'MpayPrepaidController@enroll');
Route::post('/mpay/card/topupstatus', 'MpayPrepaidController@updateTopupStatus');
Route::post('/mpay/account/info', 'MpayPrepaidController@getAccountInformation');
Route::post('/mpay/card/list', 'MpayPrepaidController@getUserCards');
Route::post('/mpay/resubmit', 'MpayPrepaidController@resubmitDoc');
Route::get('/mpay/update', 'MpayPrepaidController@updateStatus');


// Exchange Rate

Route::get('/exchange/', 'CurrencyController@index');
Route::get('/exchange/list', 'CurrencyController@getList');
Route::get('/exchange/{id}', 'CurrencyController@getInfo');
Route::post('/exchange/update', 'CurrencyController@update');
Route::post('/exchange/log', 'CurrencyController@getLog');

//Processor dashboard
Route::controller('/processorDashboard', 'ProcessorDashboardController');

// LINE CLEAR

Route::get('/line/upload', 'LineClearController@createOrder');
Route::get('/line/tracker', 'LineClearController@trackOrder');

Route::get('/line/track', 'LineClearController@tracktest');

// BOOST
// Route::resource('boost', 'BoostController');
Route::get('/boost/test', 'BoostController@boostTest');
Route::post('/boost/validatepayment', 'BoostController@validatepayment');
Route::any('/boost/cancel/{onlineref}', 'BoostController@cancel');
Route::post('/boost/callback', 'BoostController@callback');
// Route::any('/boost/response', ['as' => 'post.path', 'uses' => 'BoostController@response']);
// Route::match(array('GET','POST'),'/boost/response/{onlineref}', 'BoostController@response');
Route::any('/boost/response/{onlineref}', 'BoostController@response');

//FAVEPAY
Route::get('/favepay/signature', 'FavepayController@callsignature');
Route::any('/favepay/paymentqrcode', 'FavepayController@paymentqrcode');
Route::any('/favepay/response', 'FavepayController@response');
Route::get('/favepay/callback', 'FavepayController@callback');

//PACEPAY
Route::get('/pacepay', 'PacepayController@index');
Route::get('/pacepay/auth', 'PacepayController@authentication');
Route::post('/pacepay/transaction', 'PacepayController@createTransaction');
Route::any('/pacepay/response', 'PacepayController@response');
Route::any('/pacepay/callbackurl', 'PacepayController@Webhookcallback');
Route::any('/pacepay/webhook_callback', 'PacepayController@webhook_callback');
Route::get('/pacepay/transaction/{transaction_id}', 'PacepayController@getTransaction');

// GRABPAY
Route::get('/grabpay','GrabPayController@Index');
Route::any('/grabpay/generate','GrabPayController@generate');
Route::any('/grabpay/codeverifier','GrabPayController@codeverifier');
Route::any('/grabpay/grabcomplete','GrabPayController@grabcomplete');
Route::any('/grabpay/sendurl','GrabPayController@sendurl');
Route::any('/grabpay/redirect', 'GrabPayController@redirect');
Route::any('/grabpay/webhook', 'GrabPayController@webhook');

// BLOG
Route::get('/blog', 'BlogController@index');
Route::get('/blog/migrate', 'BlogController@getMigrate');
Route::get('/blog/posts', 'BlogController@getPosts');
Route::get('/blog/create', 'BlogController@createArticle');
Route::get('/blog/edit/{id}', 'BlogController@editArticle');
Route::post('/blog/save', 'BlogController@saveArticle');
Route::post('/blog/update', 'BlogController@updateArticle');
Route::post('/blog/info', 'BlogController@getPostInfo');
Route::post('/blog/remove', 'BlogController@Removepost');

//NINJAVAN 

Route::get('/ninjavan', 'NinjavanController@index');
Route::get('/ninjavan/token', 'NinjavanController@getToken');
Route::post('/ninjavan/assign', 'NinjavanController@createOrder');
Route::get('/ninjavan/getwaybill/{id}', 'NinjavanController@generateWaybill');

//WAREHOUSE 
Route::controller('/warehouse', 'WarehouseController');
// Route::group(array('before' => 'warehouse'), function()
// 	{
// 	Route::controller('/warehouse', 'WarehouseController');
// });

// Visitor Management
Route::controller('/visitor', 'VisitorController');

//ONLINE CAMPAIGN 

Route::controller('/onlinecampaign', 'OnlineCampaignsController');

//stock trnsfer

      Route::get('stock/anywproducts', 'stockTransferController@anywproducts');
        Route::get('stock/forms{encrypted}{id}', 'stockTransferController@stockform');
        Route::get('stock/upload{id}', 'stockTransferController@anyUpload');
        Route::get('stock/edit{id}', 'stockTransferController@edit');
       Route::get('stock/stocklist{id}', 'stockTransferController@view');
 
      
        Route::get('stock/download{encrypted}', 'stockTransferController@anyDownload');

        Route::PUT('stock/update{$id}', 'stockTransferController@putupdate');
        Route::PUT('stock/file/{id}', 'stockTransferController@uploadfile');
   
		Route::get('stock/wareproducts', 'stockTransferController@anyWareproducts');
		Route::get('stock/stocks', 'stockTransferController@anystocks');
 
        Route::get('stock/files{file}', 'stockTransferController@getfiles');
    
    Route::resource('stock', 'stockTransferController',

	[
    'except' => ['show'],
]);


//pallet management

   Route::resource('pallet', 'PalletController', [
    'except' => ['show'],
]);

 Route::get('pallet/pallets', 'PalletController@anypallets');
   Route::get('pallet/edit{id}', 'PalletController@edit');
   Route::get('pallet/stocki', 'PalletController@stocki');
 
     Route::post('pallet/stockin', 'PalletController@stockin');
     Route::post('pallet/stockout', 'PalletController@stockout');
          Route::get('pallet/history{id}', 'PalletController@history');
             Route::get('supplier/tea{sid}', 'SupplierController@xyz');
        
        Route::get('pallet/update{$id}', 'PalletController@putupdate');
        
         Route::get('pallet/stocko','PalletController@getpallet');

Route::get('/pallet/country', 'PalletController@getsuppliers');


//supplier


Route::resource('supplier', 'SupplierController', [
    'except' => ['show'],
]);
 Route::get('supplier/suppliers', 'SupplierController@anysuppliers');
   Route::get('supplier/edit{id}', 'SupplierController@edit');

// AstroGO
Route::get('/astrogo', 'AstroGoController@index');
Route::get('/astrogo/orders', 'AstroGoController@orders');
Route::post('/astrogo/upload', 'AstroGoController@upload');

Route::get('/payment-terms', 'PaymentTermsController@index');
Route::get('/payment-terms/payments', 'PaymentTermsController@payments');
Route::get('/payment-terms/create', 'PaymentTermsController@create');
Route::post('/payment-terms/store', 'PaymentTermsController@store');
Route::get('/payment-terms/edit/{id}', 'PaymentTermsController@edit');
Route::post('/payment-terms/update/{id}', 'PaymentTermsController@update');
Route::get('/payment-terms/delete/{id}', 'PaymentTermsController@delete');

Route::get('/warehouse-location', 'WarehouseLocationController@index');
Route::get('/warehouse-location/locations', 'WarehouseLocationController@locations');
Route::get('/warehouse-location/create', 'WarehouseLocationController@create');
Route::post('/warehouse-location/store', 'WarehouseLocationController@store');
Route::get('/warehouse-location/edit/{id}', 'WarehouseLocationController@edit');
Route::post('/warehouse-location/update/{id}', 'WarehouseLocationController@update');
Route::get('/warehouse-location/delete/{id}', 'WarehouseLocationController@delete');


Route::get('/purchase-order', 'PurchaseOrderController@index');
Route::get('/purchase-order/orders', 'PurchaseOrderController@orders');
Route::get('/purchase-order/create', 'PurchaseOrderController@create');
Route::post('/purchase-order/store', 'PurchaseOrderController@store');
Route::get('/purchase-order/edit/{id}', 'PurchaseOrderController@edit');
Route::post('/purchase-order/update/{id}', 'PurchaseOrderController@update');
Route::get('/purchase-order/delete/{id}', 'PurchaseOrderController@delete');
Route::get('/purchase-order/files/{loc}', 'PurchaseOrderController@files');
Route::get('/purchase-order/download/{loc}', 'PurchaseOrderController@download');
Route::get('/purchase-order/seller-list', 'PurchaseOrderController@sellerList');
Route::get('/purchase-order/fetch-seller', 'PurchaseOrderController@ajaxFetchSeller');
Route::get('/purchase-order/warehouse-list', 'PurchaseOrderController@warehouseList');
Route::get('/purchase-order/fetch-warehouse', 'PurchaseOrderController@ajaxFetchWarehouse');
Route::get('/purchase-order/po-list', 'PurchaseOrderController@poList');
Route::get('/purchase-order/fetch-po', 'PurchaseOrderController@ajaxFetchPO');
Route::get('/purchase-order/fetch-po-products/{po_id}', 'PurchaseOrderController@ajaxFetchPoProducts');
Route::get('/purchase-order/pbx', 'PurchaseOrderController@pbxIndex');
Route::get('/purchase-order/pbx/list', 'PurchaseOrderController@pbxList');
Route::post('/purchase-order/pbx/generate-zip', 'PurchaseOrderController@generateZip');
Route::get('/purchase-order/pbx/download/{filename}', 'PurchaseOrderController@downloadPbx');
Route::post('/purchase-order/pbx/complete', 'PurchaseOrderController@completePbx');
Route::get('/purchase-order/pbx/products', 'PurchaseOrderController@products');
Route::get('/purchase-order/pbx/productajax', 'PurchaseOrderController@productAjax');
Route::get('/purchase-order/pbx/prices/{id}', 'PurchaseOrderController@prices');
Route::get('/purchase-order/pbx/priceajax/{id}', 'PurchaseOrderController@priceAjax');
Route::get('/purchase-order/pbx/editproducts', 'PurchaseOrderController@editproducts');
Route::get('/purchase-order/pbx/editproductajax', 'PurchaseOrderController@editproductAjax');
Route::get('/purchase-order/pbx/editprices/{id}', 'PurchaseOrderController@editprices');
Route::get('/purchase-order/pbx/editpriceajax/{id}', 'PurchaseOrderController@editpriceAjax');

//ADDED - 17/02/2022 (PO Dashboard)
Route::get('purchase-order/dashboard', 'PurchaseOrderController@anyDashboard');
Route::get('purchase-order/po-report', 'PurchaseOrderController@anyPoReport');
Route::get('purchase-order/po-data', 'PurchaseOrderController@anyPoData');
Route::get('purchase-order/po-dashboard-data', 'PurchaseOrderController@anyPoDashboardData');
Route::get('purchase-order/po-dashboard-child-data', 'PurchaseOrderController@anyPoDashboardChildData');
Route::get('purchase-order/download-excel/{type}', 'PurchaseOrderController@downloadExcel');
Route::get('purchase-order/download-pdf', 'PurchaseOrderController@exportPdf');
Route::get('purchase-order/download-po-dash-excel/{type}', 'PurchaseOrderController@downloadPoDashExcel');
Route::get('purchase-order/download-po-dash-pdf', 'PurchaseOrderController@exportPoDashPdf');

Route::post('/purchase-order/uploadsignpo', 'PurchaseOrderController@UploadSPO');
Route::get('/purchase-order/{filename}', ['as' => 'filename', 'uses' => 'PurchaseOrderController@signedpdf' ]);
Route::get('/purchase-order/signedpo/{filename}/{id}','PurchaseOrderController@deletesignedpdf');
Route::get('/purchase-order/spdf-download','PurchaseOrderController@pdfmerge');

Route::controller('/wavpay' , 'WavpayController');

Route::controller('grn', 'GrnController');
Route::controller('admingrn', 'GrnAdminController');
Route::controller('/imports', 'ImportProductsController');

Route::get('/einvoice', 'EInvoiceController@index');
Route::get('/einvoice/lists', 'EInvoiceController@lists');
Route::get('/einvoice/create/{id}', 'EInvoiceController@create');
Route::post('/einvoice/store', 'EInvoiceController@store');
Route::get('/einvoice/edit/{id}', 'EInvoiceController@edit');
Route::post('/einvoice/update/{id}', 'EInvoiceController@update');
Route::get('/einvoice/delete/{id}', 'EInvoiceController@delete');
Route::get('/einvoice/files/{loc}', 'EInvoiceController@files');
Route::get('/einvoice/download/{loc}', 'EInvoiceController@download');
Route::get('/einvoice/generate-pbx/{id}', 'EInvoiceController@generatePbx');
Route::get('/einvoice/pbx', 'EInvoiceController@pbxIndex');
Route::get('/einvoice/pbx/list', 'EInvoiceController@pbxList');
Route::get('/einvoice/pbx/download/{loc}', 'EInvoiceController@downloadPbx');
Route::post('/einvoice/pbx/complete', 'EInvoiceController@completePbx');

Route::get('/manager', 'ManagerController@index');
Route::get('/manager/managers', 'ManagerController@managers');
Route::get('/manager/create', 'ManagerController@create');
Route::post('/manager/store', 'ManagerController@store');
Route::get('/manager/edit/{id}', 'ManagerController@edit');
Route::post('/manager/update/{id}', 'ManagerController@update');
Route::get('/manager/delete/{id}', 'ManagerController@delete');

Route::controller('/ecombanner', 'EcomBannerController');

Route::controller('gdf', 'GoodsDefectFormController');
Route::controller('stock-transfer', 'StockTransferNewController');
Route::controller('stock-requisition', 'StockRequisitionController');
Route::controller('mailchimp-report', 'MailchimpReportController');
Route::controller('product-update', 'ProductUpdateV2Controller');

Route::controller('contestant', 'ContestantController');

Route::get('/temperature_log', 'TemperatureLogController@index');
Route::post('/temperature_log/store', 'TemperatureLogController@store');

Route::controller('/yhtest', 'YHTestController');

Route::controller('/', 'LoginController');









