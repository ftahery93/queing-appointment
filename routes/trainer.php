<?php

// Dashboard routes
Route::get('dashboard', 'Trainer\DashboardController@index');

// User Profile
Route::get('user/profile', 'Trainer\UserProfileController@profile');
Route::put('user/profile', 'Trainer\UserProfileController@update');

// Change Password
Route::get('user/changepassword', 'Trainer\ChangePasswordController@index');
Route::put('user/changepassword', 'Trainer\ChangePasswordController@update');

// Package routes
Route::resource('packages', 'Trainer\TrainerPackageController');
Route::post('packages/delete', 'Trainer\TrainerPackageController@destroyMany');

// Authentication Routes
Route::get('', 'Trainer\Auth\LoginController@index');
Route::post('login', 'Trainer\Auth\LoginController@login');
Route::get('logout', 'Trainer\Auth\LoginController@logout');

// Password Reset Routes
Route::get('password/reset', 'Trainer\Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('password/email', 'Trainer\Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Trainer\Auth\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Trainer\Auth\ResetPasswordController@reset');

// incomeStatistics routes
Route::get('incomeStatistics', 'Trainer\IncomeStatisticController@index');
Route::post('incomeStatistics', 'Trainer\IncomeStatisticController@ajaxchart');

// subscribers routes
Route::get('subscribers', 'Trainer\SubscriberController@index');
Route::get('subscribers/{id}/attendanceHistory', 'Trainer\SubscriberController@attendanceHistory');
Route::get('subscribers/{id}/currentPackage', 'Trainer\SubscriberController@currentPackage');
Route::get('subscribers/{id}/paymentDetails', 'Trainer\SubscriberController@paymentDetails');
Route::post('subscribers/create', 'Trainer\SubscriberController@create');
Route::get('archivedSubscribers', 'Trainer\SubscriberController@archivedSubscribers');
Route::get('{id}/archivedAttendanceHistory', 'Trainer\SubscriberController@archivedAttendanceHistory');
Route::get('{id}/packageHistory', 'Trainer\SubscriberController@packageHistory');
Route::get('{id}/packagePayment', 'Trainer\SubscriberController@packagePayment');

// Module1 Reports routes
Route::get('payments', 'Trainer\ReportController@payment');
Route::get('onlinePayments', 'Trainer\ReportController@onlinePayment');
Route::get('subscriptionExpired', 'Trainer\ReportController@subscriptionExpired');
Route::get('subscriptions', 'Trainer\ReportController@subscriptions');
Route::get('attendance', 'Trainer\ReportController@attendance');

// Module1 Reports Print routes
Route::get('printPayments', 'Trainer\ReportPrintController@payment');
Route::get('excelPayments', 'Trainer\ReportExcelController@payment');
Route::get('printSubscriptionExpired', 'Trainer\ReportPrintController@subscriptionExpired');
Route::get('excelSubscriptionExpired', 'Trainer\ReportExcelController@subscriptionExpired');
Route::get('printSubscriptions', 'Trainer\ReportPrintController@subscriptions');
Route::get('excelSubscriptions', 'Trainer\ReportExcelController@subscriptions');
Route::get('printAttendance', 'Trainer\ReportPrintController@attendance');
Route::get('excelAttendance', 'Trainer\ReportExcelController@attendance');


//Errors 
Route::get('errors/401', function () {
    return view('errors.401');
});
Route::get('errors/505', function () {
    return view('errors.505');
});

Route::get('errors/noAccessTrainer', function () {
    return view('errors.noAccessTrainer');
});