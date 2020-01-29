<?php

use App\User;
use App\Categories;
use App\Company;

Route::get('/dashboard', function () {
	
    $user = User::count();
 
    $users[] = Auth::user();
    $users[] = Auth::guard()->user();
    $users[] = Auth::guard('admin')->user();
    
    $data['total_comapny'] = Company::count();
    
    
    return view('admin.dashboard', $data);
})->name('dashboard');


//Route::get('/dashboard','DashboardController@index');
Route::resource('holidays', 'Admin\HolidaysController');
Route::get('get-holidays', 'Admin\HolidaysController@getHolidays');
Route::resource('timeline', 'Admin\TimelineController');
Route::get('timeline/create/{user_id}', 'Admin\TimelineController@create');
Route::get('user-timeline/{user_id}', 'Admin\UsersController@timeline');
Route::get('user-attendance/{user_id}', 'Admin\UsersController@attendance');
Route::get('get-attendance/{user_id}', 'Admin\UsersController@getAttendance');
Route::get('attendance/create/{user_id}', 'Admin\AttendanceStatusController@create');
Route::post('attendance/', 'Admin\AttendanceStatusController@store');

Route::get('settings', 'Admin\SettingsController@index');
Route::post('settings/update', 'Admin\SettingsController@update');

Route::get('profile', 'Admin\ProfileController@index');
Route::post('profile/update', 'Admin\ProfileController@update');
Route::get('change-password', 'Admin\ProfileController@changePasswordView');
Route::post('change-password', 'Admin\ProfileController@changePassword');


//create company
 Route::resource('companies', 'Admin\CompanyController');
 Route::get('get-companies', 'Admin\CompanyController@getCompanies');
 Route::get('companies/company-login/{company_id}', 'Admin\CompanyController@companyLogin');
 Route::get('company/create', 'CompanyAuth\RegisterController@showRegistrationForm');
 Route::post('company/create', 'CompanyAuth\RegisterController@store');
 Route::get('email-templates/{id}', 'Admin\EmailTemplateController@index');
 Route::post('update-email-template/{id}', 'Admin\EmailTemplateController@update');

Route::resource('stores', 'Admin\StoreController');
Route::get('get-stores', 'Admin\StoreController@getStores');