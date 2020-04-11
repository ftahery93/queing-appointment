<?php

// Home routes
Route::get('{code}/home', 'Vendor\HomeController@index');

// Dashboard routes
Route::get('{code}/dashboard', 'Vendor\DashboardController@index');

// incomeStatistics routes
Route::get('{code}/incomeStatistics', 'Vendor\IncomeStatisticController@index');
Route::post('{code}/incomeStatistics', 'Vendor\IncomeStatisticController@ajaxchart');

// User Profile
Route::get('{code}/user/profile', 'Vendor\UserProfileController@profile');
Route::put('{code}/user/profile', 'Vendor\UserProfileController@update');

Route::get('{code}/user/info', 'Vendor\AccountDetailController@index');
Route::put('{code}/user/info', 'Vendor\AccountDetailController@update');

// Change Password
Route::get('{code}/user/changepassword', 'Vendor\ChangePasswordController@index');
Route::put('{code}/user/changepassword', 'Vendor\ChangePasswordController@update');

// Permisson routes
Route::get('{code}/permissions', 'Vendor\PermissionController@index');
Route::get('{code}/permissions/create', 'Vendor\PermissionController@create');
Route::post('{code}/permissions', 'Vendor\PermissionController@store');
Route::get('{code}/permissions/{id}/edit', 'Vendor\PermissionController@edit');
Route::patch('{code}/permissions/{id}', 'Vendor\PermissionController@update');
Route::post('{code}/permissions/delete', 'Vendor\PermissionController@destroyMany');

// User routes
Route::get('{code}/users', 'Vendor\UserController@index');
Route::get('{code}/users/create', 'Vendor\UserController@create');
Route::post('{code}/users', 'Vendor\UserController@store');
Route::get('{code}/users/{id}/edit', 'Vendor\UserController@edit');
Route::patch('{code}/users/{id}', 'Vendor\UserController@update');
Route::post('{code}/users/delete', 'Vendor\UserController@destroyMany');

// vendorBranches routes
Route::get('{code}/vendorBranches', 'Vendor\VendorBranchController@index');
Route::get('{code}/vendorBranches/create', 'Vendor\VendorBranchController@create');
Route::post('{code}/vendorBranches', 'Vendor\VendorBranchController@store');
Route::get('{code}/vendorBranches/{id}/edit', 'Vendor\VendorBranchController@edit');
Route::patch('{code}/vendorBranches/{id}', 'Vendor\VendorBranchController@update');
Route::post('{code}/vendorBranches/delete', 'Vendor\VendorBranchController@destroyMany');
Route::get('{code}/vendorBranches/{branch_id}/uploadImages', 'Vendor\VendorBranchController@uploadImages');
Route::post('{code}/vendorBranches/{branch_id}/images', 'Vendor\VendorBranchController@images');
Route::post('{code}/vendorBranches/deleteImage/{id}', 'Vendor\VendorBranchController@deleteImage');


// Module1 Dashboard routes
Route::get('{code}/{slug}/dashboard', 'Vendor\Module1\DashboardController@index');

// Module1 Package routes
Route::get('{code}/{slug}/packages', 'Vendor\Module1\VendorPackageController@index');
Route::get('{code}/{slug}/packages/create', 'Vendor\Module1\VendorPackageController@create');
Route::post('{code}/{slug}/packages', 'Vendor\Module1\VendorPackageController@store');
Route::get('{code}/{slug}/packages/{id}/edit', 'Vendor\Module1\VendorPackageController@edit');
Route::patch('{code}/{slug}/packages/{id}', 'Vendor\Module1\VendorPackageController@update');
Route::post('{code}/{slug}/packages/delete', 'Vendor\Module1\VendorPackageController@destroyMany');

// Module1 Instructor Package routes
Route::get('{code}/{slug}/instructorPackages', 'Vendor\Module1\InstructorPackageController@index');
Route::get('{code}/{slug}/instructorPackages/create', 'Vendor\Module1\InstructorPackageController@create');
Route::post('{code}/{slug}/instructorPackages', 'Vendor\Module1\InstructorPackageController@store');
Route::get('{code}/{slug}/instructorPackages/{id}/edit', 'Vendor\Module1\InstructorPackageController@edit');
Route::patch('{code}/{slug}/instructorPackages/{id}', 'Vendor\Module1\InstructorPackageController@update');
Route::post('{code}/{slug}/instructorPackages/delete', 'Vendor\Module1\InstructorPackageController@destroyMany');

// Module1 Members routes
Route::get('{code}/{slug}/members', 'Vendor\Module1\MemberController@index');
Route::get('{code}/{slug}/members/create', 'Vendor\Module1\MemberController@create');
Route::post('{code}/{slug}/members', 'Vendor\Module1\MemberController@store');
Route::get('{code}/{slug}/members/{id}/edit', 'Vendor\Module1\MemberController@edit');
Route::patch('{code}/{slug}/members/{id}', 'Vendor\Module1\MemberController@update');
Route::post('{code}/{slug}/members/delete', 'Vendor\Module1\MemberController@destroyMany');
Route::get('{code}/{slug}/{id}/packageHistory', 'Vendor\Module1\MemberController@packageHistory');
Route::get('{code}/{slug}/{id}/packagePayment', 'Vendor\Module1\MemberController@packagePayment');
Route::post('{code}/{slug}/members/renewPackage', 'Vendor\Module1\MemberController@renewPackage');
Route::post('{code}/{slug}/members/getPackageDetail', 'Vendor\Module1\MemberController@getPackageDetail');
Route::get('{code}/{slug}/{id}/invoice', 'Vendor\Module1\MemberController@invoice');
Route::get('{code}/{slug}/{id}/sendInvoice', 'Vendor\Module1\MemberController@sendInvoice');
Route::post('{code}/{slug}/members/delete', 'Vendor\Module1\MemberController@trashMany');
Route::get('{code}/{slug}/members/trashedlist', 'Vendor\Module1\MemberController@trashedlist');
Route::post('{code}/{slug}/members/trashed/{id}/delete', 'Vendor\Module1\MemberController@destroy');
Route::post('{code}/{slug}/members/trashed/{id}/restore', 'Vendor\Module1\MemberController@restore');
Route::post('{code}/{slug}/members/instructorSubscription', 'Vendor\Module1\MemberController@instructorSubscription');
Route::get('{code}/{slug}/{id}/instructorInvoice', 'Vendor\Module1\MemberController@instructorInvoice');

// Module1 Instructor Subscriptions routes
Route::get('{code}/{slug}/instructorSubscriptions', 'Vendor\Module1\InstructorSubscriptionController@index');
Route::get('{code}/{slug}/instructorSubscriptions/{package_id}/subscribers', 'Vendor\Module1\InstructorSubscriptionController@subscribers');
Route::post('{code}/{slug}/instructorSubscriptions/subscribers/addAttendance', 'Vendor\Module1\InstructorSubscriptionController@addAttendance');
Route::get('{code}/{slug}/instructorSubscriptions/showAttendance/{subscribed_package_id}', 'Vendor\Module1\InstructorSubscriptionController@showAttendance');

// Module1  incomeStatistics routes
Route::get('{code}/{slug}/incomeStatistics', 'Vendor\Module1\IncomeStatisticController@index');
Route::post('{code}/{slug}/incomeStatistics', 'Vendor\Module1\IncomeStatisticController@ajaxchart');

// Module1 Reports routes
Route::get('{code}/{slug}/favourites', 'Vendor\Module1\ReportController@favourite');
Route::get('{code}/{slug}/payments', 'Vendor\Module1\ReportController@payment');
Route::get('{code}/{slug}/onlinePayments', 'Vendor\Module1\ReportController@onlinePayment');
Route::get('{code}/{slug}/subscriptionExpired', 'Vendor\Module1\ReportController@subscriptionExpired');
Route::get('{code}/{slug}/subscriptions', 'Vendor\Module1\ReportController@subscriptions');
Route::get('{code}/{slug}/m1/instructorSubscriptions', 'Vendor\Module1\ReportController@instructorSubscriptions');

// Module1 Reports Print routes
Route::get('{code}/{slug}/printFavourites', 'Vendor\Module1\ReportPrintController@favourite');
Route::get('{code}/{slug}/excelFavourites', 'Vendor\Module1\ReportExcelController@favourite');
Route::get('{code}/{slug}/printPayments', 'Vendor\Module1\ReportPrintController@payment');
Route::get('{code}/{slug}/excelPayments', 'Vendor\Module1\ReportExcelController@payment');
Route::get('{code}/{slug}/printonlinePayments', 'Vendor\Module1\ReportPrintController@onlinePayment');
Route::get('{code}/{slug}/excelonlinePayments', 'Vendor\Module1\ReportExcelController@onlinePayment');
Route::get('{code}/{slug}/printsubscriptionExpired', 'Vendor\Module1\ReportPrintController@subscriptionExpired');
Route::get('{code}/{slug}/excelsubscriptionExpired', 'Vendor\Module1\ReportExcelController@subscriptionExpired');
Route::get('{code}/{slug}/printsubscriptions', 'Vendor\Module1\ReportPrintController@subscriptions');
Route::get('{code}/{slug}/excelsubscriptions', 'Vendor\Module1\ReportExcelController@subscriptions');
Route::get('{code}/{slug}/printInstructorSubscriptions', 'Vendor\Module1\ReportPrintController@instructorSubscriptions');
Route::get('{code}/{slug}/excelInstructorSubscriptions', 'Vendor\Module1\ReportExcelController@instructorSubscriptions');


// Module2 Dashboard routes
Route::get('{code}/{slug}/m2/dashboard', 'Vendor\Module2\DashboardController@index');

// Module2  incomeStatistics routes
Route::get('{code}/{slug}/m2/incomeStatistics', 'Vendor\Module2\IncomeStatisticController@index');
Route::post('{code}/{slug}/m2/incomeStatistics', 'Vendor\Module2\IncomeStatisticController@ajaxchart');


// Module2 Class Package routes
Route::get('{code}/{slug}/classPackages', 'Vendor\Module2\ClassPackageController@index');
Route::get('{code}/{slug}/classPackages/create', 'Vendor\Module2\ClassPackageController@create');
Route::post('{code}/{slug}/classPackages', 'Vendor\Module2\ClassPackageController@store');
Route::get('{code}/{slug}/classPackages/{id}/edit', 'Vendor\Module2\ClassPackageController@edit');
Route::patch('{code}/{slug}/classPackages/{id}', 'Vendor\Module2\ClassPackageController@update');
Route::post('{code}/{slug}/classPackages/delete', 'Vendor\Module2\ClassPackageController@destroyMany');

// Module2 Classes routes
Route::get('{code}/{slug}/{class_master_id}/classes', 'Vendor\Module2\ClassController@index');
Route::get('{code}/{slug}/{class_master_id}/classes/create', 'Vendor\Module2\ClassController@create');
Route::post('{code}/{slug}/{class_master_id}/classes', 'Vendor\Module2\ClassController@store');
Route::get('{code}/{slug}/{class_master_id}/classes/{id}/edit', 'Vendor\Module2\ClassController@edit');
Route::patch('{code}/{slug}/{class_master_id}/classes/{id}', 'Vendor\Module2\ClassController@update');
Route::post('{code}/{slug}/{class_master_id}/classes/delete', 'Vendor\Module2\ClassController@destroyMany');
Route::get('{code}/{slug}/{id}/sendApproval', 'Vendor\Module2\ClassController@sendApproval');
Route::get('{code}/{slug}/{id}/manageSchedule', 'Vendor\Module2\ClassController@manageSchedule');
Route::post('{code}/{slug}/addSchedule', 'Vendor\Module2\ClassController@addSchedule');
Route::post('{code}/{slug}/deleteSchedule', 'Vendor\Module2\ClassController@deleteSchedule');
Route::get('{code}/{slug}/manageSchedule', 'Vendor\Module2\ClassController@manageSchedule');
Route::get('{code}/{slug}/schedules', 'Vendor\Module2\ClassController@schedules');
Route::get('{code}/{slug}/schedules/{id}', 'Vendor\Module2\ClassController@schedules');
Route::get('{code}/{slug}/classDetail/{id}', 'Vendor\Module2\ClassController@classDetail');
Route::post('{code}/{slug}/classes/changeRequest', 'Vendor\Module2\ClassController@changeRequest');
Route::get('{code}/{slug}/classSchedules/{id}', 'Vendor\Module2\ClassController@classSchedule');
Route::post('{code}/{slug}/classes/classSchedules/edit', 'Vendor\Module2\ClassController@editSchedule');
Route::get('{code}/{slug}/rejectedClasses', 'Vendor\Module2\ClassController@rejectedClasses');
Route::post('{code}/{slug}/rejectedClasses/edit', 'Vendor\Module2\ClassController@editRejectedClasses');
Route::get('{code}/{slug}/classBranch', 'Vendor\Module2\ClassController@classBranch');
Route::post('{code}/{slug}/addWeeklySchedule', 'Vendor\Module2\ClassController@addWeeklySchedule');
Route::post('{code}/{slug}/classes/classSchedules/delete', 'Vendor\Module2\ClassController@deleteMultiSchedule');

//ClassMaster
Route::get('{code}/{slug}/classMaster', 'Vendor\Module2\ClassMasterController@index');
Route::get('{code}/{slug}/classMaster/create', 'Vendor\Module2\ClassMasterController@create');
Route::post('{code}/{slug}/classMaster', 'Vendor\Module2\ClassMasterController@store');
Route::get('{code}/{slug}/classMaster/{id}/edit', 'Vendor\Module2\ClassMasterController@edit');
Route::patch('{code}/{slug}/classMaster/{id}', 'Vendor\Module2\ClassMasterController@update');
Route::post('{code}/{slug}/classMaster/delete', 'Vendor\Module2\ClassMasterController@destroyMany');

// Upload Schedule routes
Route::get('{code}/{slug}/uploadSchedule', 'Vendor\Module2\UploadScheduleController@index');
Route::get('{code}/{slug}/uploadSchedule/{id}', 'Vendor\Module2\UploadScheduleController@importdata');
Route::get('{code}/{slug}/uploadSchedule/excel/{id}', 'Vendor\Module2\UploadScheduleController@exportdata');
Route::get('{code}/{slug}/uploadSchedule/imported_list/{id}', 'Vendor\Module2\UploadScheduleController@importedlist');
Route::get('{code}/{slug}/uploadSchedule/excelindex/{id}', 'Vendor\Module2\UploadScheduleController@excelindex');
Route::post('{code}/{slug}/uploadSchedule', 'Vendor\Module2\UploadScheduleController@store');
Route::put('{code}/{slug}/uploadSchedule/updateFields', 'Vendor\Module2\UploadScheduleController@updateFields');

// Module2 Subscribers routes
Route::get('{code}/{slug}/subscribers', 'Vendor\Module2\SubscriberController@index');
Route::get('{code}/{slug}/subscribers/{id}/currentPackage', 'Vendor\Module2\SubscriberController@currentPackage');
Route::get('{code}/{slug}/subscribers/{id}/currentBooking', 'Vendor\Module2\SubscriberController@currentBooking');
Route::get('{code}/{slug}/subscribers/{id}/paymentDetails', 'Vendor\Module2\SubscriberController@paymentDetails');
Route::get('{code}/{slug}/subscribers/{id}/packageHistory', 'Vendor\Module2\SubscriberController@packageHistory');
Route::get('{code}/{slug}/subscribers/{id}/packagePayment', 'Vendor\Module2\SubscriberController@packagePayment');
Route::get('{code}/{slug}/subscribers/{id}/bookingHistory', 'Vendor\Module2\SubscriberController@bookingHistory');
Route::get('{code}/{slug}/archivedSubscribers', 'Vendor\Module2\SubscriberController@archivedSubscribers');

// Module2 Reports routes
Route::get('{code}/{slug}/report/bookings', 'Vendor\Module2\ReportController@bookings');
Route::get('{code}/{slug}/report/onlinePayments', 'Vendor\Module2\ReportController@onlinePayment');
Route::get('{code}/{slug}/report/subscriptionExpired', 'Vendor\Module2\ReportController@subscriptionExpired');
Route::get('{code}/{slug}/report/subscriptions', 'Vendor\Module2\ReportController@subscriptions');

// Module2 Reports Print routes
Route::get('{code}/{slug}/printBookings', 'Vendor\Module2\ReportPrintController@bookings');
Route::get('{code}/{slug}/excelBookings', 'Vendor\Module2\ReportExcelController@bookings');
Route::get('{code}/{slug}/reportPrint/onlinePayments', 'Vendor\Module2\ReportPrintController@onlinePayment');
Route::get('{code}/{slug}/excelModule2OnlinePayments', 'Vendor\Module2\ReportExcelController@onlinePayment');
Route::get('{code}/{slug}/reportPrint/subscriptionExpired', 'Vendor\Module2\ReportPrintController@subscriptionExpired');
Route::get('{code}/{slug}/excelModule2SubscriptionExpired', 'Vendor\Module2\ReportExcelController@subscriptionExpired');
Route::get('{code}/{slug}/reportPrint/subscriptions', 'Vendor\Module2\ReportPrintController@subscriptions');
Route::get('{code}/{slug}/excelModule2Subscriptions', 'Vendor\Module2\ReportExcelController@subscriptions');


// Module3 Classes routes
Route::get('{code}/{slug}/m3/schedules', 'Vendor\Module3\ClassController@schedules');
Route::get('{code}/{slug}/m3/schedules/{id}', 'Vendor\Module3\ClassController@schedules');
Route::get('{code}/{slug}/m3/classDetail/{id}', 'Vendor\Module3\ClassController@classDetail');

// Module3 Subscribers routes
Route::get('{code}/{slug}/m3/subscribers', 'Vendor\Module3\SubscriberController@index');
Route::get('{code}/{slug}/m3/subscribers/{id}/bookingHistory', 'Vendor\Module3\SubscriberController@bookingHistory');
Route::get('{code}/{slug}/m3/archivedSubscribers', 'Vendor\Module3\SubscriberController@archivedSubscribers');

// Module3 Reports routes
Route::get('{code}/{slug}/m3/report/bookings', 'Vendor\Module3\ReportController@bookings');

// Module3 Reports Print routes
Route::get('{code}/{slug}/m3/printBookings', 'Vendor\Module3\ReportPrintController@bookings');
Route::get('{code}/{slug}/m3/excelBookings', 'Vendor\Module3\ReportExcelController@bookings');

// incomeStatistics routes
Route::get('incomeStatistics', 'Vendor\IncomeStatisticController@index');
Route::post('incomeStatistics', 'Vendor\IncomeStatisticController@ajaxchart');

// Import Data routes
Route::get('{code}/importexportdata', 'Vendor\ImportExportDataController@index');
Route::get('{code}/importexportdata/{id}', 'Vendor\ImportExportDataController@importdata');
Route::get('{code}/importexportdata/excel/{id}', 'Vendor\ImportExportDataController@exportdata');
Route::get('{code}/importexportdata/imported_list/{id}', 'Vendor\ImportExportDataController@importedlist');
Route::get('{code}/importexportdata/excelindex/{id}', 'Vendor\ImportExportDataController@excelindex');
Route::post('{code}/importexportdata', 'Vendor\ImportExportDataController@store');
Route::put('{code}/importexportdata/updateFields', 'Vendor\ImportExportDataController@updateFields');

// LogActivity routes
Route::get('{code}/logActivity', 'Vendor\LogActivityController@index');

// Authentication Routes
Route::get('', 'Vendor\Auth\LoginController@index');
Route::get('{code}', 'Vendor\Auth\LoginController@loginindex');
Route::post('store', 'Vendor\Auth\LoginController@store');
Route::post('{code}/login', 'Vendor\Auth\LoginController@login');
Route::get('{code}/logout', 'Vendor\Auth\LoginController@logout');

// Password Reset Routes
Route::get('{code}/password/reset', 'Vendor\Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('{code}/password/email', 'Vendor\Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('{code}/password/reset/{token}', 'Vendor\Auth\ResetPasswordController@showResetForm');
Route::post('{code}/password/reset', 'Vendor\Auth\ResetPasswordController@reset');

// Module4 Dashboard routes
Route::get('{code}/{slug}/m4/dashboard', 'Vendor\Module4\DashboardController@index');

// Module4  incomeStatistics routes
Route::get('{code}/{slug}/m4/incomeStatistics', 'Vendor\Module4\IncomeStatisticController@index');
Route::post('{code}/{slug}/m4/incomeStatistics', 'Vendor\Module4\IncomeStatisticController@ajaxchart');

// Module4 Categories routes
Route::get('{code}/{slug}/categories', 'Vendor\Module4\CategoryController@index');
Route::get('{code}/{slug}/categories/create', 'Vendor\Module4\CategoryController@create');
Route::post('{code}/{slug}/categories', 'Vendor\Module4\CategoryController@store');
Route::get('{code}/{slug}/categories/{id}/edit', 'Vendor\Module4\CategoryController@edit');
Route::patch('{code}/{slug}/categories/{id}', 'Vendor\Module4\CategoryController@update');
Route::post('{code}/{slug}/categories/delete', 'Vendor\Module4\CategoryController@destroyMany');

// Module4 Products routes
Route::get('{code}/{slug}/products', 'Vendor\Module4\ProductController@index');
Route::get('{code}/{slug}/products/create', 'Vendor\Module4\ProductController@create');
Route::post('{code}/{slug}/products', 'Vendor\Module4\ProductController@store');
Route::get('{code}/{slug}/products/{id}/edit', 'Vendor\Module4\ProductController@edit');
Route::patch('{code}/{slug}/products/{id}', 'Vendor\Module4\ProductController@update');
Route::post('{code}/{slug}/products/delete', 'Vendor\Module4\ProductController@destroyMany');
Route::get('{code}/{slug}/products/getOptionValue/{id}', 'Vendor\Module4\ProductController@getOptionValue');
Route::get('{code}/{slug}/products/{product_id}/uploadImages', 'Vendor\Module4\ProductController@uploadImages');
Route::post('{code}/{slug}/products/{product_id}/images', 'Vendor\Module4\ProductController@images');
Route::post('{code}/{slug}/products/deleteImage/{id}', 'Vendor\Module4\ProductController@deleteImage');
Route::get('{code}/{slug}/products/{id}/{type}', 'Vendor\Module4\ProductController@destroyOptionValue');

// Module4 Products Options routes
Route::get('{code}/{slug}/options', 'Vendor\Module4\OptionController@index');
Route::get('{code}/{slug}/options/create', 'Vendor\Module4\OptionController@create');
Route::post('{code}/{slug}/options', 'Vendor\Module4\OptionController@store');
Route::get('{code}/{slug}/options/{id}/edit', 'Vendor\Module4\OptionController@edit');
Route::patch('{code}/{slug}/options/{id}', 'Vendor\Module4\OptionController@update');
Route::post('{code}/{slug}/options/delete', 'Vendor\Module4\OptionController@destroyMany');
Route::get('{code}/{slug}/optionvalue/{id}', 'Vendor\Module4\OptionController@destroyOptionValue');

// Module4 Orders routes
Route::get('{code}/{slug}/orders', 'Vendor\Module4\OrderController@index');
Route::get('{code}/{slug}/orders/{customer_id}', 'Vendor\Module4\OrderController@index');
Route::get('{code}/{slug}/order/{order_id}', 'Vendor\Module4\OrderController@order');
Route::get('{code}/{slug}/orders/{order_id}/orderInvoicePrint', 'Vendor\Module4\OrderController@orderInvoicePrint');

// Module4 Reports routes
Route::get('{code}/{slug}/report/productPurchased', 'Vendor\Module4\ReportController@productPurchased');
Route::get('{code}/{slug}/report/orderPayments', 'Vendor\Module4\ReportController@orderPayments');
Route::get('{code}/{slug}/report/coupons', 'Vendor\Module4\ReportController@coupons');
Route::get('{code}/{slug}/report/customerOrders', 'Vendor\Module4\ReportController@customerOrders');

// Module4 Reports Print routes
Route::get('{code}/{slug}/reportPrint/productPurchased', 'Vendor\Module4\ReportPrintController@productPurchased');
Route::get('{code}/{slug}/excelModule4ProductPurchased', 'Vendor\Module4\ReportExcelController@productPurchased');
Route::get('{code}/{slug}/reportPrint/orderPayments', 'Vendor\Module4\ReportPrintController@orderPayments');
Route::get('{code}/{slug}/excelModule4OrderPayments', 'Vendor\Module4\ReportExcelController@orderPayments');
Route::get('{code}/{slug}/reportPrint/coupons', 'Vendor\Module4\ReportPrintController@coupons');
Route::get('{code}/{slug}/excelModule4Coupons', 'Vendor\Module4\ReportExcelController@coupons');
Route::get('{code}/{slug}/reportPrint/customerOrders', 'Vendor\Module4\ReportPrintController@customerOrders');
Route::get('{code}/{slug}/excelModule4CustomerOrders', 'Vendor\Module4\ReportExcelController@customerOrders');

//Errors 
Route::get('{code}/errors/402', function () {
    return view('errors.402');
});
Route::get('{code}/errors/507', function () {
    return view('errors.507');
});
Route::get('errors/noAccess', function () {
    return view('errors.noAccess');
});