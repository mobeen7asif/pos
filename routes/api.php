<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', 'Api\ApiController@register');
Route::post('login', 'Api\ApiController@login');
Route::post('login-with-passcode', 'Api\ApiController@loginWithPassCode');
Route::post('forget-password', 'Api\ApiController@forgetPassword');

// sync data
Route::post('sync-data', 'Company\ProductsController@syncData');      
Route::post('get-sync-data', 'Company\ProductsController@getSyncData');      
Route::post('mark-attendence', 'Api\ApiController@loginMultiWithPin');

Route::post('company-login', 'Api\ApiController@companyLogin');

Route::get('store_admin_logout',"Api\ApiController@adminLogout");
Route::post('login-sync',"Api\ApiController@loginSync");
Route::group(['middleware' => 'auth:api'], function(){
    Route::post('store-bulk-orders', 'Company\OrderController@storeBulkOrders');
    Route::get('details', 'Api\ApiController@details');
    Route::get('profile', 'Api\ApiController@profileDetails');
    Route::post('update-profile', 'Api\ApiController@updateProfile');
    Route::post('change-password', 'Api\ApiController@changePassword');
    Route::get('logout',"Api\ApiController@logout");
    
    Route::post('get-attendance', 'Api\ApiController@getAttendance');
    Route::get('get-timeline', 'Api\ApiController@getTimeline');
    Route::post('login-with-pincode', 'Api\ApiController@loginWithPin');
    Route::get('ping-server', 'Api\ApiController@pingServer');
    
    Route::get('get-categories', 'Company\CategoriesController@getCategoriesApi');
    Route::post('search-products', 'Company\ProductsController@searchProducts');
    Route::post('search-products-by-title', 'Company\ProductsController@searchProductsByTitle');
    
    Route::post('transfer-stock', 'Company\StockController@transferStock');

    Route::post('create-customer', 'Company\CustomerController@storeApi');
    Route::post('update-customer/{id}', 'Company\CustomerController@updateApi');
    Route::post('search-customers', 'Company\CustomerController@searchCustomers');

    Route::post('create-order', 'Company\OrderController@store');
    Route::post('update-order', 'Company\OrderController@update');
    Route::post('return-order', 'Company\OrderController@salesReturn');
    Route::post('search-orders', 'Company\OrderController@searchOrders');
    Route::get('get-order/{order_id}', 'Company\OrderController@edit');
    Route::post('send-order-email', 'Company\OrderController@sendOrderEmailApi');    
    
    Route::get('get-tax-rates', 'Company\TaxRatesController@getTaxRatesApi');
    Route::get('get-shipping-options', 'Company\ShippingOptionController@getShippingOptionsApi');
     
    // Reports
    Route::post('get-report-details', 'Company\ReportController@getRetailsDashboard');
    Route::post('get-reports-graph', 'Company\ReportController@getReportsGraphApi');

    Route::get('get-waiters', 'Api\ApiController@getWaiters');
    Route::get('get-discounts', 'Api\ApiController@getDiscounts');
    Route::get('get-ads', 'Api\ApiController@getAds');

    Route::post('save-tables', 'Api\ApiController@saveFloorTables');

    Route::post('register-worker-device', 'Api\ApiController@registerWorkerDevice');
    Route::post('approve-customer-order', 'Api\ApiController@approveOrder');

    Route::post('/add_shifts/', 'Api\ApiController@addShifts');
    Route::post('/update_tip/', 'Api\ApiController@updateTip');

    Route::post('cancel_order/', 'Api\ApiController@cancelOrder');

    Route::get('customer-name-search', 'Api\ApiController@getCustomerName');

});



Route::post('customer-register', 'Api\ApiController@customerRegister');
Route::post('customer-login', 'Api\ApiController@customerLogin');

Route::group(['middleware' => ['checkSession']], function () {
    Route::post('customer-change-password', 'Api\ApiController@customerChangePassword');
    Route::post('customer-change-profile', 'Api\ApiController@updateCustomerProfile');
    Route::get('get-store-categories', 'Api\ApiController@getStoreCategories');
    Route::get('get-category-products', 'Api\ApiController@getCategoryProducts');
    Route::post('customer-create-order', 'Company\OrderController@store')->name('customer-order');
    Route::get('product-detail', 'Api\ApiController@productDetail');
    Route::get('product-detail-barcode', 'Api\ApiController@productDetailBarcode');
    Route::get('customer-orders', 'Api\ApiController@getOrders');
    Route::get('customer-order-detail', 'Api\ApiController@getOrderDetail');

    Route::post('save-card', 'Api\ApiController@saveCard');
    Route::get('get-cards', 'Api\ApiController@getCards');
    Route::post('delete-card', 'Api\ApiController@deleteCard');

    Route::post('book-table', 'Api\ApiController@bookTable');
    Route::post('register-customer-device', 'Api\ApiController@registerCustomerDevice');


    Route::post('complete-customer-order', 'Api\ApiController@updateOrder');

    Route::get('get-beacons', 'Api\ApiController@getBeacons');

    Route::get('search-store', 'Api\ApiController@searchStore');
});

//ERP apis
Route::group(['middleware' => ['checkErpToken']], function () {
    Route::get('create-company', 'Api\ApiController@createCompany');
});

Route::post('/customer-forgot-password', 'Api\ApiController@forgotPassword');
Route::post('/customer-update-password', 'Api\ApiController@updatePassword');
Route::get('/reset_password/{token}', function ($token) {
    $user = \App\Customer::where('token', $token)->first();
    if ($user) {
        $data['token'] = $token;
        return View::make('customer_password.change_password_view', $data);
    } else {
        return View::make('customer_password.token_expire_view');
    }
});

Route::post('/test/', 'Api\ApiController@test');
Route::get('/session/', 'Api\ApiController@test1');

Route::get('/save_product_orders/', 'Api\ApiController@saveProductOrder');
Route::get('/delete_data/', 'Api\ApiController@deleteData');
Route::post('/transaction/', 'Api\ApiController@test3');

Route::get('get_stores', 'Api\ApiController@getStores');



