<?php

// Dashboard routes
Route::get('dashboard', 'Admin\DashboardController@index');

// Activity routes
Route::resource('activities', 'Admin\ActivityController');
Route::post('masters/activities/delete', 'Admin\ActivityController@destroyMany');

// Module routes
Route::resource('modules', 'Admin\ModuleController');
Route::post('masters/modules/delete', 'Admin\ModuleController@destroyMany');

// Area routes
Route::resource('areas', 'Admin\AreaController');
Route::post('masters/areas/delete', 'Admin\AreaController@destroyMany');

// Amenities routes
Route::resource('amenities', 'Admin\AmenityController');
Route::post('masters/amenities/delete', 'Admin\AmenityController@destroyMany');

// PaymentMode routes
Route::resource('paymentModes', 'Admin\PaymentModeController');
Route::post('masters/paymentModes/delete', 'Admin\PaymentModeController@destroyMany');

// User routes
Route::resource('users', 'Admin\UserController');
Route::post('users/delete', 'Admin\UserController@destroyMany');

// User Profile
Route::get('user/profile', 'Admin\UserProfileController@profile');
Route::put('user/profile', 'Admin\UserProfileController@update');

// Change Password
Route::get('user/changepassword', 'Admin\ChangePasswordController@index');
Route::put('user/changepassword', 'Admin\ChangePasswordController@update');

// Permisson routes
Route::resource('permissions', 'Admin\PermissionController');
Route::post('permissions/delete', 'Admin\PermissionController@destroyMany');

// Package routes
Route::resource('packages', 'Admin\PackageController');
Route::post('packages/delete', 'Admin\PackageController@destroyMany');

// RegisteredUser routes
Route::post('registeredUsers/delete', 'Admin\RegisteredUserController@trashMany');
Route::get('registeredUsers/trashedlist', 'Admin\RegisteredUserController@trashedlist');
Route::post('registeredUsers/trashed/{id}/delete', 'Admin\RegisteredUserController@destroy');
Route::post('registeredUsers/trashed/{id}/restore', 'Admin\RegisteredUserController@restore');
Route::get('registeredUsers/{id}/packageHistory', 'Admin\RegisteredUserController@packageHistory');
Route::get('registeredUsers/{id}/packagePayment', 'Admin\RegisteredUserController@packagePayment');
Route::get('registeredUsers/{id}/ownerDetail', 'Admin\RegisteredUserController@ownerDetail');
Route::resource('registeredUsers', 'Admin\RegisteredUserController');

// CMSPages routes
Route::resource('cmsPages', 'Admin\CmsPageController');
Route::post('cmsPages/delete', 'Admin\CmsPageController@destroyMany');

// sponsoredAdvertisements routes
Route::resource('sponsoredAds', 'Admin\SponsoredAdController');
Route::post('sponsoredAds/delete', 'Admin\SponsoredAdController@destroyMany');

// Faq routes
Route::resource('faq', 'Admin\FaqController');
Route::post('faq/delete', 'Admin\FaqController@destroyMany');

// Contactus routes
Route::get('contactus', 'Admin\ContactusController@index');

// Notifications routes
Route::resource('notifications', 'Admin\NotificationController');
Route::post('notifications/delete', 'Admin\NotificationController@destroyMany');

// Import Data routes
Route::get('importexportdata', 'Admin\ImportExportDataController@index');
Route::get('importexportdata/{id}', 'Admin\ImportExportDataController@importdata');
Route::get('importexportdata/excel/{id}', 'Admin\ImportExportDataController@exportdata');
Route::get('importexportdata/imported_list/{id}', 'Admin\ImportExportDataController@importedlist');
Route::post('importexportdata', 'Admin\ImportExportDataController@store');

// Authentication Routes
Route::get('', 'Admin\Auth\LoginController@index');
Route::post('login', 'Admin\Auth\LoginController@login');
Route::get('logout', 'Admin\Auth\LoginController@logout');

// Password Reset Routes
Route::get('password/reset', 'Admin\Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('password/email', 'Admin\Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Admin\Auth\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Admin\Auth\ResetPasswordController@reset');

// Backup routes
Route::get('backup', 'Admin\BackupController@index');
Route::get('backup/create', 'Admin\BackupController@create');
Route::get('backup/download/{file_name}', 'Admin\BackupController@download');
Route::post('backup/{file_name}/delete', 'Admin\BackupController@delete');

// LogActivity routes
Route::get('logActivity', 'Admin\LogActivityController@index');
Route::get('vendorLogActivity', 'Admin\LogActivityController@vendorLog');
Route::get('trainerLogActivity', 'Admin\LogActivityController@trainerLog');

// languageManagement routes
Route::resource('languageManagement', 'Admin\LanguageManagementController');
Route::post('languageManagement/delete', 'Admin\LanguageManagementController@destroyMany');

// incomeStatistics routes
Route::get('incomeStatistics', 'Admin\IncomeStatisticController@index');
Route::post('incomeStatistics', 'Admin\IncomeStatisticController@ajaxchart');

// classPackages routes
Route::get('classPackages', 'Admin\ClassPackageController@index');

// classes routes
Route::get('module2ClassSchedules', 'Admin\ClassController@module2ClassSchedules');
Route::get('module2ClassSchedules/{class_id}/{vendor_id}', 'Admin\ClassController@module2ClassSchedules');
Route::get('module3ClassSchedules', 'Admin\ClassController@module3ClassSchedules');
Route::get('module3ClassSchedules/{class_id}/{vendor_id}', 'Admin\ClassController@module3ClassSchedules');
Route::get('classDetail/{id}/{module_id}', 'Admin\ClassController@classDetail');

// archivedClasses routes
Route::get('archivedClasses', 'Admin\ArchivedClassController@index');

// Transaction routes
Route::post('transactions/delete', 'Admin\TransactionController@destroyMany');
Route::get('transactions/{id}/transactionEmail', 'Admin\TransactionController@transactionEmail');
Route::resource('transactions', 'Admin\TransactionController');

// Vendor routes
Route::post('vendors/delete', 'Admin\VendorController@trashMany');
Route::get('vendors/trashedlist', 'Admin\VendorController@trashedlist');
Route::post('vendors/trashed/{id}/delete', 'Admin\VendorController@destroy');
Route::post('vendors/trashed/{id}/restore', 'Admin\VendorController@restore');
Route::resource('vendors', 'Admin\VendorController');

// vendorBranches routes
Route::get('{vendor_id}/vendorBranches', 'Admin\VendorBranchController@branchList');
Route::get('vendorBranches/{vendor_id}/create', 'Admin\VendorBranchController@create');
Route::post('vendorBranches/{vendor_id}/store', 'Admin\VendorBranchController@store');
Route::post('vendorBranches/{vendor_id}/delete', 'Admin\VendorBranchController@destroyMany');
Route::resource('vendorBranches', 'Admin\VendorBranchController');
Route::get('vendorBranches/{branch_id}/uploadImages', 'Admin\VendorBranchController@uploadImages');
Route::post('vendorBranches/{branch_id}/images', 'Admin\VendorBranchController@images');
Route::post('vendorBranches/deleteImage/{id}', 'Admin\VendorBranchController@deleteImage');

// vendorPackages routes
Route::get('vendorPackages', 'Admin\VendorPackageController@index');

// instructorPackages routes
Route::get('instructorPackages', 'Admin\InstructorPackageController@index');

// Vendor Notifications routes
Route::resource('vendorNotifications', 'Admin\VendorNotificationController');
Route::post('vendorNotifications/delete', 'Admin\VendorNotificationController@destroyMany');

// Vendor Transaction routes
Route::get('{vendor_id}/vendortransactions', 'Admin\VendorTransactionController@index');
Route::get('vendortransactions/{vendor_id}/create', 'Admin\VendorTransactionController@create');
Route::post('vendortransactions/{vendor_id}/store', 'Admin\VendorTransactionController@store');
Route::post('vendortransactions/delete', 'Admin\VendorTransactionController@destroyMany');
Route::get('vendortransactions/{id}/transactionEmail', 'Admin\VendorTransactionController@transactionEmail');
Route::resource('vendortransactions', 'Admin\VendorTransactionController');

// Vendor Members routes
Route::get('{vendor_id}/members', 'Admin\MemberController@index');
Route::get('members/{vendor_id}/{id}/packageHistory', 'Admin\MemberController@packageHistory');
Route::get('members/{id}/packagePayment', 'Admin\MemberController@packagePayment');
Route::get('members/{id}/packagePayment', 'Admin\MemberController@packagePayment');

//Vendors Reports routes
Route::get('vendorFavourites', 'Admin\VendorReportController@favourite');
Route::get('vendorPayments', 'Admin\VendorReportController@payment');
Route::get('vendorOnlinePayments', 'Admin\VendorReportController@onlinePayment');
Route::get('vendorSubscriptionExpired', 'Admin\VendorReportController@subscriptionExpired');
Route::get('vendorSubscriptions', 'Admin\VendorReportController@subscriptions');
Route::get('classSubscriptions', 'Admin\VendorReportController@classSubscriptions');
Route::get('fitflowMembershipSubscriptions', 'Admin\VendorReportController@fitflowMembershipSubscriptions');
Route::get('classSubscriptionExpired', 'Admin\VendorReportController@classSubscriptionExpired');
Route::get('fitflowMembershipSubscriptionExpired', 'Admin\VendorReportController@fitflowMembershipSubscriptionExpired');
Route::get('classOnlinePayments', 'Admin\VendorReportController@classOnlinePayment');
Route::get('fitflowMembershipOnlinePayments', 'Admin\VendorReportController@fitflowMembershipOnlinePayment');
Route::get('classBookings', 'Admin\VendorReportController@classBookings');
Route::get('fitflowMembershipBookings', 'Admin\VendorReportController@fitflowMembershipBookings');
Route::get('m1/instructorSubscriptions', 'Admin\VendorReportController@instructorSubscriptions');

// Vendors Reports Print routes
Route::get('printVendorFavourites', 'Admin\VendorReportPrintController@favourite');
Route::get('excelVendorFavourites', 'Admin\VendorReportExcelController@favourite');
Route::get('printVendorPayments', 'Admin\VendorReportPrintController@payment');
Route::get('excelVendorPayments', 'Admin\VendorReportExcelController@payment');
Route::get('printVendorOnlinePayments', 'Admin\VendorReportPrintController@onlinePayment');
Route::get('excelVendorOnlinePayments', 'Admin\VendorReportExcelController@onlinePayment');
Route::get('printVendorSubscriptionExpired', 'Admin\VendorReportPrintController@subscriptionExpired');
Route::get('excelVendorSubscriptionExpired', 'Admin\VendorReportExcelController@subscriptionExpired');
Route::get('printVendorSubscriptions', 'Admin\VendorReportPrintController@subscriptions');
Route::get('excelVendorSubscriptions', 'Admin\VendorReportExcelController@subscriptions');
Route::get('printClassSubscriptions', 'Admin\VendorReportPrintController@classSubscriptions');
Route::get('excelClassSubscriptions', 'Admin\VendorReportExcelController@classSubscriptions');
Route::get('printClassSubscriptionExpired', 'Admin\VendorReportPrintController@classSubscriptionExpired');
Route::get('excelClassSubscriptionExpired', 'Admin\VendorReportExcelController@classSubscriptionExpired');
Route::get('printClassOnlinePayments', 'Admin\VendorReportPrintController@classOnlinePayment');
Route::get('excelClassOnlinePayments', 'Admin\VendorReportExcelController@classOnlinePayment');
Route::get('printClassBookings', 'Admin\VendorReportPrintController@classBookings');
Route::get('excelClassBookings', 'Admin\VendorReportExcelController@classBookings');
Route::get('printfitflowMembershipSubscriptions', 'Admin\VendorReportPrintController@fitflowMembershipSubscriptions');
Route::get('excelfitflowMembershipSubscriptions', 'Admin\VendorReportExcelController@fitflowMembershipSubscriptions');
Route::get('printfitflowMembershipSubscriptionExpired', 'Admin\VendorReportPrintController@fitflowMembershipSubscriptionExpired');
Route::get('excelfitflowMembershipSubscriptionExpired', 'Admin\VendorReportExcelController@fitflowMembershipSubscriptionExpired');
Route::get('printfitflowMembershipOnlinePayments', 'Admin\VendorReportPrintController@fitflowMembershipOnlinePayment');
Route::get('excelfitflowMembershipOnlinePayments', 'Admin\VendorReportExcelController@fitflowMembershipOnlinePayment');
Route::get('printfitflowMembershipBookings', 'Admin\VendorReportPrintController@fitflowMembershipBookings');
Route::get('excelfitflowMembershipBookings', 'Admin\VendorReportExcelController@fitflowMembershipBookings');
Route::get('printInstructorSubscriptions', 'Admin\VendorReportPrintController@instructorSubscriptions');
Route::get('excelInstructorSubscriptions', 'Admin\VendorReportExcelController@instructorSubscriptions');

// Personel Trainer routes
Route::post('trainers/delete', 'Admin\TrainerController@trashMany');
Route::get('trainers/trashedlist', 'Admin\TrainerController@trashedlist');
Route::post('trainers/trashed/{id}/delete', 'Admin\TrainerController@destroy');
Route::post('trainers/trashed/{id}/restore', 'Admin\TrainerController@restore');
Route::get('trainers/{id}/sendCredential', 'Admin\TrainerController@sendCredential');
Route::get('trainers/{id}/packages', 'Admin\TrainerController@packages');
Route::resource('trainers', 'Admin\TrainerController');

// Personel Trainer Subscribers
Route::get('{trainer_id}/subscribers', 'Admin\SubscriberController@index');
Route::get('subscribers/{id}/attendanceHistory', 'Admin\SubscriberController@attendanceHistory');
Route::get('subscribers/{id}/currentPackage', 'Admin\SubscriberController@currentPackage');
Route::get('subscribers/{id}/paymentDetails', 'Admin\SubscriberController@paymentDetails');
Route::post('subscribers/create', 'Admin\SubscriberController@create');
Route::get('{trainer_id}/archivedSubscribers', 'Admin\SubscriberController@archivedSubscribers');
Route::get('{id}/archivedAttendanceHistory', 'Admin\SubscriberController@archivedAttendanceHistory');
Route::get('{id}/packageHistory', 'Admin\SubscriberController@packageHistory');
Route::get('{id}/packagePayment', 'Admin\SubscriberController@packagePayment');

// Trainer Transaction routes
Route::get('{trainer_id}/trainertransactions', 'Admin\TrainerTransactionController@index');
Route::get('trainertransactions/{trainer_id}/create', 'Admin\TrainerTransactionController@create');
Route::post('trainertransactions/{trainer_id}/store', 'Admin\TrainerTransactionController@store');
Route::post('trainertransactions/delete', 'Admin\TrainerTransactionController@destroyMany');
Route::get('trainertransactions/{id}/transactionEmail', 'Admin\TrainerTransactionController@transactionEmail');
Route::resource('trainertransactions', 'Admin\TrainerTransactionController');


// Trainer Notifications routes
Route::resource('trainerNotifications', 'Admin\TrainerNotificationController');
Route::post('trainerNotifications/delete', 'Admin\TrainerNotificationController@destroyMany');

// trainerPackage routes
Route::get('trainerPackages', 'Admin\TrainerPackageController@index');

// Trainers Reports routes
Route::get('trainerPayments', 'Admin\TrainerReportController@payment');
Route::get('trainerSubscriptionExpired', 'Admin\TrainerReportController@subscriptionExpired');
Route::get('trainerSubscriptions', 'Admin\TrainerReportController@subscriptions');

// Trainers Reports Print routes
Route::get('printTrainerPayments', 'Admin\TrainerReportPrintController@payment');
Route::get('excelTrainerPayments', 'Admin\TrainerReportExcelController@payment');
Route::get('printTrainerSubscriptionExpired', 'Admin\TrainerReportPrintController@subscriptionExpired');
Route::get('excelTrainerSubscriptionExpired', 'Admin\TrainerReportExcelController@subscriptionExpired');
Route::get('printTrainerSubscriptions', 'Admin\TrainerReportPrintController@subscriptions');
Route::get('excelTrainerSubscriptions', 'Admin\TrainerReportExcelController@subscriptions');

// Pending Classes Approval
Route::get('pendingVendorClasses', 'Admin\PendingClassController@index');
Route::get('pendingClasses/{vendor_id}', 'Admin\PendingClassController@pendingClasses');
Route::post('pendingClasses/editClasses', 'Admin\PendingClassController@editClasses');
Route::get('pendingClasses/previousDetail/{id}', 'Admin\PendingClassController@previousDetail');
Route::get('pendingCommission/{vendor_id}', 'Admin\PendingClassController@pendingCommission');
Route::post('pendingClasses/addCommission', 'Admin\PendingClassController@addCommission');

// Contract Expired routes
Route::get('expiredContracts', 'Admin\ExpiredContractController@index');

// Coupon code routes
Route::get('coupons/{coupon_id}/couponHistory', 'Admin\CouponController@couponHistory');
Route::post('coupons/delete', 'Admin\CouponController@destroyMany');
Route::resource('coupons', 'Admin\CouponController');

// Orders code routes
Route::get('orders', 'Admin\OrderController@index');
Route::get('orders/{customer_id}', 'Admin\OrderController@index');
Route::get('order/{order_id}', 'Admin\OrderController@order');
Route::get('orders/{order_id}/orderInvoicePrint', 'Admin\OrderController@orderInvoicePrint');
Route::post('orders/{order_id}/orderHistory', 'Admin\OrderController@orderHistory');

// Module4 Reports routes
Route::get('orderReport/productPurchased', 'Admin\OrderReportController@productPurchased');
Route::get('orderReport/orderPayments', 'Admin\OrderReportController@orderPayments');
Route::get('orderReport/coupons', 'Admin\OrderReportController@coupons');
Route::get('orderReport/customerOrders', 'Admin\OrderReportController@customerOrders');

// Module4 Reports Print routes
Route::get('orderReportPrint/productPurchased', 'Admin\OrderReportPrintController@productPurchased');
Route::get('excelModule4ProductPurchased', 'Admin\OrderReportExcelController@productPurchased');
Route::get('orderReportPrint/orderPayments', 'Admin\OrderReportPrintController@orderPayments');
Route::get('excelModule4OrderPayments', 'Admin\OrderReportExcelController@orderPayments');
Route::get('orderReportPrint/coupons', 'Admin\OrderReportPrintController@coupons');
Route::get('excelModule4Coupons', 'Admin\OrderReportExcelController@coupons');
Route::get('orderReportPrint/customerOrders', 'Admin\OrderReportPrintController@customerOrders');
Route::get('excelModule4CustomerOrders', 'Admin\OrderReportExcelController@customerOrders');

//Cache Config , Route , View, Optimize 
Route::get('configCache', 'Admin\CacheController@configCache');
Route::get('routeCache', 'Admin\CacheController@routeCache');
Route::get('viewCache', 'Admin\CacheController@viewCache');
Route::get('optimize', 'Admin\CacheController@optimize');

//Cache Clear Config , Route , View, Optimize 
Route::get('configCacheClear', 'Admin\CacheController@configCacheClear');
Route::get('routeCacheClear', 'Admin\CacheController@routeCacheClear');
Route::get('viewCacheClear', 'Admin\CacheController@viewCacheClear');
Route::get('cacheClear', 'Admin\CacheController@cacheClear');



//CronJob routes
Route::get('cronJob', 'Admin\CronJobController@index');

Route::get('/updateapp', function() {
    exec('composer dump-autoload');
    echo 'composer dump-autoload complete';
});

//Errors 
Route::get('errors/401', function () {
    return view('errors.401');
});
Route::get('errors/505', function () {
    return view('errors.505');
});
