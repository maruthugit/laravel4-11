<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

App::error(function(PDOException $exception)
{
    Log::error("Error connecting to database: ".$exception->getMessage());

    return "Error connecting to database";
});

//App::after(function ($request, $response) {
//    $response->headers->set("Cache-Control","no-cache,no-store, must-revalidate");
//    $response->headers->set("Pragma", "no-cache"); //HTTP 1.0
//    $response->headers->set("Expires"," Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
//});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::json(array('error' => 'Your session has expired. Please log in again'), 403);
			// return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() !== Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

/*
|--------------------------------------------------------------------------
| Role ID
|--------------------------------------------------------------------------
| 1: Administrator
| 2: Admin Staff
| 3: Accounts
| 4: Designer
| 5: Logistic Admin
| 
*/

Route::filter('common', function()
{
	if(Auth::guest()) {
		return Redirect::guest('login');
	}

});

Route::filter('sysadmin', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 10, 1, 'OR');

	if($allow == false) {
        return View::make('home.denied', array('module' => 'System Administration'));   
	}
});

Route::filter('product', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 2, 1, 'OR');

	if($allow == false) {
        return View::make('home.denied', array('module' => 'Products'));   
	}
});

Route::filter('transaction', function()
{ 
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 4, 1, 'OR'); 

	if($allow == false) {
        return View::make('home.denied', array('module' => 'Transaction'));   
	}
}); 

Route::filter('seller', function()
{ 
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 9, 1, 'OR'); 

	if($allow == false) {
        return View::make('home.denied', array('module' => 'Seller'));   
	}
}); 

Route::filter('banner', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 5, 1, 'OR'); 

	if($allow == false) {
        return View::make('home.denied', array('module' => 'Banner'));    
	}
});

Route::filter('latestnews', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 6, 1, 'OR'); 

	if($allow == false) {
        return View::make('home.denied', array('module' => 'Latest News'));   
	}
});

Route::filter('hotitem', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 7, 1, 'OR'); 

	if($allow == false) {
        return View::make('home.denied', array('module' => 'Hot Items'));   
	}
});

Route::filter('comment', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 3, 1, 'OR'); 

	if($allow == false) {
        return View::make('home.denied', array('module' => 'Comments'));   
	}
});

Route::filter('customer', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 8, 1, 'OR');

	if($allow == false) {
        return View::make('home.denied', array('module' => 'Customers'));   
	}
});

Route::filter('shipping', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 1, 1, 'OR'); 

	if($allow == false) {
        return View::make('home.denied', array('module' => 'Shipping'));   
	}
});

Route::filter('pushnotice', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 11, 1, 'OR'); 

	if($allow == false) {
        return View::make('home.denied', array('module' => 'Push Notification'));   
	}
});

Route::filter('specialprice', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 13, 1, 'OR'); 

	if($allow == false) {
        return View::make('home.denied', array('module' => 'Special Pricing'));   
	}
});

Route::filter('logistic', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 14, 1, 'OR'); 

	if($allow == false) {
        return View::make('home.denied', array('module' => 'Logistic'));   
	}
});

Route::filter('general_report', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 17, 1, 'OR'); 

	if($allow == false) {
        return View::make('home.denied', array('module' => 'General Report'));   
	}
});

Route::filter('warehouse', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 24, 1, 'OR'); 

	if($allow == false) {
        return View::make('home.denied', array('module' => 'Warehouse'));   
	}
});

Route::filter('case', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 25, 1, 'OR');
	if($allow == false) {
        return View::make('home.denied', array('module' => 'Case'));   
	}
});

Route::filter('jocommy', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 26, 4, 'OR');
	if($allow == false) {
        return View::make('home.denied', array('module' => 'JocomMy Banner'));   
	}
});

Route::filter('purchaseorder', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 29, 1, 'OR');
	if($allow == false) {
        return View::make('home.denied', array('module' => 'Purchase Order'));   
	}
});

Route::filter('goodsreceivednote', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 30, 1, 'OR');
	if($allow == false) {
        return View::make('home.denied', array('module' => 'Goods Received Note'));   
	}
});

Route::filter('goodsdefect', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 31, 1, 'OR');
	if($allow == false) {
        return View::make('home.denied', array('module' => 'Goods Defect'));   
	}
});

Route::filter('flashsale', function()
{
	$allow		= Permission::CheckAccessLevel(Session::get('role_id'), 32, 1, 'OR');
	if($allow == false) {
        return View::make('home.denied', array('module' => 'Flash Sale'));   
	}
});

Route::filter('no-cache',function($route, $request, $response){

    $response->header("Cache-Control","no-cache,no-store, must-revalidate");
    $response->header("Pragma", "no-cache"); //HTTP 1.0
    $response->header("Expires"," Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

});
?>