<?php

// User routes
Route::resource('users', 'Trainer\UserController');
Route::post('users/delete', 'Trainer\UserController@destroyMany');

// User Profile
Route::get('user/profile', 'Trainer\UserProfileController@profile');
Route::put('user/profile', 'Trainer\UserProfileController@update');

// Change Password
Route::get('user/changepassword', 'Trainer\ChangePasswordController@index');
Route::put('user/changepassword', 'Trainer\ChangePasswordController@update');

// Package routes
Route::get('branches/{id}/view', 'Trainer\TrainerPackageController@view');
Route::resource('branches', 'Trainer\TrainerPackageController');
Route::post('branches/delete', 'Trainer\TrainerPackageController@destroyMany');

// Services routes
Route::get('services/{id}/view', 'Trainer\ServiceController@view');
Route::resource('services', 'Trainer\ServiceController');
Route::post('services/delete', 'Trainer\ServiceController@destroyMany');

// Appointment routes
Route::get('appointments/{id}/view', 'Trainer\AppointmentController@view');
Route::resource('appointments', 'Trainer\AppointmentController');
Route::post('appointments/delete', 'Trainer\AppointmentController@destroyMany');

// Queue routes
Route::get('queues/{service_id}/view', 'Trainer\QueueController@index');
Route::resource('queues', 'Trainer\QueueController');
Route::post('queues/delete', 'Trainer\QueueController@destroyMany');



// Authentication Routes
Route::get('', 'Trainer\Auth\LoginController@index');
Route::post('login', 'Trainer\Auth\LoginController@login');
Route::get('logout', 'Trainer\Auth\LoginController@logout');

// Password Reset Routes
Route::get('password/reset', 'Trainer\Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('password/email', 'Trainer\Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Trainer\Auth\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Trainer\Auth\ResetPasswordController@reset');



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