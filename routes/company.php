<?php

use App\User;

Route::get('/dashboard', function () {
	
    $user = User::count();
 
    $users[] = Auth::user();
    $users[] = Auth::guard()->user();
    $users[] = Auth::guard('admin')->user();

    return view('company.dashboard', compact('user'));
})->name('dashboard');

Route::get('/home', function(){ return redirect('company/dashboard'); });

Route::get('profile', 'Company\ProfileController@index');
Route::post('profile/update', 'Company\ProfileController@update');
Route::get('change-password', 'Company\ProfileController@changePasswordView');
Route::post('change-password', 'Company\ProfileController@changePassword');

Route::resource('users', 'Company\UsersController');
Route::get('get-users','Company\UsersController@getUsers');
Route::get('users/get-logs/{user_id}','Company\UsersController@getUserLogs');
Route::get('get-user-logs/{user_id}','Company\UsersController@getUserAjaxLogs');


Route::resource('stores', 'Company\StoreController');

Route::get('create-store', 'Company\StoreController@create');
Route::get('get-stores', 'Company\StoreController@getStores');


Route::resource('categories', 'Company\CategoriesController');   
Route::get('get-categories', 'Company\CategoriesController@getCategories');
Route::get('get-store-categories/{store_id}','Company\CategoriesController@getStoreCategories');
Route::resource('suppliers', 'Company\SuppliersController');
Route::get('get-suppliers', 'Company\SuppliersController@getSuppliers');
Route::resource('currencies', 'Company\CurrencyController');
Route::get('get-currencies', 'Company\CurrencyController@getCurrencies');
Route::resource('tax-rates', 'Company\TaxRatesController');
Route::get('get-tax-rates', 'Company\TaxRatesController@getTaxRates');
Route::resource('shipping-options', 'Company\ShippingOptionController');
Route::get('get-shipping-options', 'Company\ShippingOptionController@getShippingOptions');

//discounts
Route::get('discounts', 'Company\DiscountsController@showDiscounts');
Route::get('discounts/create/{store_id}', 'Company\DiscountsController@createDiscountView');
Route::post('discounts/create', 'Company\DiscountsController@createDiscount');
Route::get('get-discounts', 'Company\DiscountsController@getDiscounts');
Route::get('discounts/{id}/{store_id}/edit', 'Company\DiscountsController@editView');
Route::post('discounts/{id}', 'Company\DiscountsController@updateDiscount');
Route::delete('discounts/{id}', 'Company\DiscountsController@deleteDiscount');
Route::get('get_store_categories', 'Company\DiscountsController@getStoreCategories');

Route::get('discounts/bogo', 'Company\DiscountsController@addBogoView');
Route::post('discounts/add/bogo', 'Company\DiscountsController@addBogoDiscount');
Route::post('discounts/update/bogo', 'Company\DiscountsController@updateBogoDiscount');
Route::get('discounts_bogo/{id}/{store_id}/edit', 'Company\DiscountsController@editBogoView');
Route::get('get-products-ajax', 'Company\DiscountsController@getProductsAjax');
Route::get('get-categories-ajax', 'Company\DiscountsController@getCategoriesAjax');


Route::resource('products', 'Company\ProductsController');
Route::patch('products/update-store/{product_id}', 'Company\ProductsController@updateStore');
Route::patch('products/update-combo-products/{product_id}', 'Company\ProductsController@updateComboProducts');
Route::get('get-products', 'Company\ProductsController@getProducts');
Route::delete('products/remove-variant/{variant_id}','Company\ProductsController@removeVariant');
Route::delete('products/remove-combo/{combo_id}','Company\ProductsController@removeCombo');
Route::post('products/store-image','Company\ProductsController@storeImage');
Route::get('products/delete-image/{id}','Company\ProductsController@deleteImage');
Route::post('products/set-default-image','Company\ProductsController@setDefaultImage');
Route::get('products/get-store-categories/{store_id}','Company\ProductsController@getStoreCategories');
Route::get('get-all-store-categories/{product_id?}','Company\ProductsController@getAllStoreCategories');
Route::post('products/create-product-attribute','Company\ProductsController@createProductAttribute');
Route::get('products/get-product-attributes/{product_id}','Company\ProductsController@getProductAttributes');
Route::delete('products/remove-product-attribute/{id}','Company\ProductsController@removeProductAttribute');
Route::post('products/create-product-variant','Company\ProductsController@createProductVariant');
Route::get('products/get-product-variants/{product_id}','Company\ProductsController@getProductVariants');
Route::post('products/set-product-as-default','Company\ProductsController@setProductAsDefault');
Route::get('products/get-product-modifiers/{product_id}','Company\ProductsController@getProductModifiers');
Route::post('products/set-product-modifier','Company\ProductsController@setProductModifier');
Route::get('products/edit/{product_id}','Company\ProductsController@editVariant');
Route::patch('products/update-variant-product/{product_id}','Company\ProductsController@updateVariantProduct');
Route::get('get-combo-products','Company\ProductsController@getComboProducts');
Route::get('get-product-stocks','Company\ProductsController@productStocks');
Route::get('product-stocks/{product_id}','Company\ProductsController@productStocks');
Route::get('get-product-stocks/{product_id}','Company\ProductsController@getProductStocks');
Route::get('product-sale-history/{product_id}','Company\ProductsController@productSaleHistory');
Route::get('get-product-history/{product_id}','Company\ProductsController@getProductSaleHistory');


//save min max modifier
Route::post('products/set-product-modifier','Company\ProductsController@setProductModifier');

Route::resource('manage-stocks', 'Company\StockController');
Route::get('get-stocks', 'Company\StockController@getStocks');
Route::get('get-store-products/{store_id}', 'Company\StockController@getStoreProducts');
Route::post('products/save-modifier-numbers', 'Company\ProductsController@saveModifierNumber');

Route::resource('variants', 'Company\VariantController');
Route::get('get-variants', 'Company\VariantController@getVariants');
Route::resource('modifiers', 'Company\ModifierController');
Route::get('get-modifiers', 'Company\ModifierController@getModifiers');
Route::get('modifiers/remove-option/{option_id}', 'Company\ModifierController@removeModifierOption');
Route::resource('customers', 'Company\CustomerController');
Route::get('get-customers', 'Company\CustomerController@getCustomers');
Route::get('customer_detail/{id}', 'Company\CustomerController@getCustomerDetail');
Route::resource('customer-groups', 'Company\CustomerGroupController');
Route::get('get-customer-groups', 'Company\CustomerGroupController@getCustomerGroups');
Route::get('favorite-products/{customer_id}', 'Company\CustomerController@favoriteProducts');
Route::get('get-favorite-products/{customer_id}', 'Company\CustomerController@getFavoriteProducts');

Route::get('sales', 'Company\OrderController@index');
Route::get('get-sales/{id}/{type}', 'Company\OrderController@getOrders');
Route::get('get-sale/{order_id}', 'Company\OrderController@edit');
Route::get('invoice/{id}', 'Company\OrderController@orderInvoice');

//Reporsts
Route::get('reports/retail-report', 'Company\ReportController@index');
Route::post('reports/retail-dashboard', 'Company\ReportController@getRetailsDashboard');
Route::get('reports/stores-stock/{store_id?}', 'Company\ReportController@getStoreStocksChart');
Route::get('reports/sales-report', 'Company\ReportController@salesReport');
Route::get('reports/get-sales-report', 'Company\ReportController@getSalesReport');
Route::get('reports/products-report', 'Company\ReportController@productsReport');
Route::get('reports/get-products-report', 'Company\ReportController@getProductsReport');
Route::get('reports/customers-report', 'Company\ReportController@customersReport');
Route::get('reports/get-customers-report', 'Company\ReportController@getCustomersReport');
Route::get('reports/staff-report', 'Company\ReportController@staffReport');
Route::get('reports/get-staff-report', 'Company\ReportController@getStaffReport');
Route::get('reports/history/{user_id}', 'Company\ReportController@getStaffHistory');
Route::get('reports/get-staff-history/{user_id}','Company\ReportController@getStaffAjaxHistory');
Route::get('reports/shift-report', 'Company\ReportController@shiftReport');
Route::get('reports/get-shift-report', 'Company\ReportController@manage-stocks');
Route::get('reports/shift-log/{shift_id}', 'Company\ReportController@getShiftLogs');
Route::get('reports/get-shift-log/{shift_id}', 'Company\ReportController@getShiftAjaxLogs');
Route::post('reports/get-sale-graph', 'Company\ReportController@getSalesGraph');
Route::post('reports/get-products-graph', 'Company\ReportController@getProductsGraph');
Route::post('reports/get-customers-graph', 'Company\ReportController@getCustomersGraph');
Route::post('reports/get-staff-graph', 'Company\ReportController@getStaffGraph');


Route::resource('roles', 'Company\RoleController');
Route::get('get-roles', 'Company\RoleController@getRoles');
Route::get('roles/permissions/{role_id}', 'Company\RoleController@getRolePermissions');
Route::put('roles/permissions/{role_id}', 'Company\RoleController@updateRolePermission');
Route::resource('permissions', 'Company\PermissionController');
Route::get('get-permissions', 'Company\PermissionController@getPermissions');


Route::get('email-template', 'Company\EmailTemplateController@index');
Route::get('get-email-templates', 'Company\EmailTemplateController@getEmailTemplates');
Route::get('email_templates/{id}/edit', 'Company\EmailTemplateController@editEmailTemplate');
Route::post('update-email-template', 'Company\EmailTemplateController@update');

Route::get('settings', 'Company\SettingsController@index');
Route::post('settings/update', 'Company\SettingsController@update');

Route::get('duty/settings', 'Company\SettingsController@dutyView');
Route::post('duty/settings/update', 'Company\SettingsController@dutyUpdate');

//new work , meal types
Route::get('meal_types', 'Company\MealController@index');
Route::get('meals/create', 'Company\MealController@create');
Route::post('meals/create', 'Company\MealController@store');
Route::get('get-meals/{id}', 'Company\MealController@getMeals');
Route::delete('meals/{id}', 'Company\MealController@destroy');
Route::get('meals/{id}/edit', 'Company\MealController@edit');
Route::post('meals/{id}', 'Company\MealController@update');

//csv routes
Route::get('import/customers', 'Company\CSVController@addCsvView');
Route::post('upload/csv', 'Company\CSVController@uploadCsvFile');
Route::post('insert-customers-file', 'Company\CSVController@insertCustomersData');
Route::get('import/products', 'Company\CSVController@addProductsCsvView');
Route::post('upload/products_csv', 'Company\CSVController@uploadProductsCsvFile');
Route::post('insert-products-file', 'Company\CSVController@insertProductsData');
//floor plan routes
Route::get('floors', 'Company\FloorController@index');
Route::get('get-floors/{id}', 'Company\FloorController@getFloors');
Route::get('floors/{id}/edit', 'Company\FloorController@edit');
Route::post('floors_update/{id}', 'Company\FloorController@updateFloor');
Route::delete('floors/{id}', 'Company\FloorController@destroy');
Route::get('floors/create', 'Company\FloorController@create');
Route::post('/floors/create_floor', 'Company\FloorController@store');

Route::get('tables/{id}/edit', 'Company\FloorController@editTableView');
Route::get('tables/{id}/free_table', 'Company\FloorController@freeTable');
Route::post('tables/update_table', 'Company\FloorController@updateTable');

Route::get('tables/{id}', 'Company\FloorController@getTablesView');
Route::get('get-tables/{id}', 'Company\FloorController@getTables');

Route::get('floors/waiter_assign/{id}', 'Company\FloorController@waiterAssignView');
Route::post('tables/update_waiter', 'Company\FloorController@updateWaiter');
Route::get('tables/{id}/qr', 'Company\FloorController@getQr');

//advertisement routes
Route::get('ads', 'Company\AdsController@index');
Route::get('get-ads/{id}', 'Company\AdsController@getAds');
Route::get('ads/{id}/edit', 'Company\AdsController@edit');
Route::post('ad_update/{id}', 'Company\AdsController@updateAd');
Route::delete('ads/{id}', 'Company\AdsController@destroy');
Route::get('ads/create', 'Company\AdsController@create');
Route::post('/ads/create_ad', 'Company\AdsController@store');

Route::get('/store/{store_id}/beacon', 'Company\StoreController@beaconView');
Route::post('/store/beacon/{store_id}', 'Company\StoreController@addBeacon');

Route::get('get-category-products-ajax', 'Company\CategoriesController@getProducts');

Route::get('/delete_image/{table}/{id}/{column}', 'Company\StoreController@deleteImage');