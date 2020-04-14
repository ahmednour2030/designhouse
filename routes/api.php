<?php

// public routes
Route::get('me', 'User\MeController@getMe');

// Get designs
Route::get('designs', 'Designs\DesignController@index');
Route::get('designs/{id}', 'Designs\DesignController@findDesign');

// Get users
Route::get('users', 'user\UserController@index');

//
Route::get('teams/slug/{slug}', 'Teams\TeamsController@finBySlug');

// Route group for authentication users only.

Route::group(['middleware' => 'auth:api'], function () {

    Route::post('logout', 'Auth\LoginController@logout');
    Route::put('settings/profile', 'User\SettingsController@updateprofile');
    Route::put('settings/password', 'User\SettingsController@updatepassword');

    // Upload Design
    Route::post('designs', 'Designs\UploadController@upload');
    Route::put('design/{id}', 'Designs\DesignController@update');
    Route::delete('design/{id}', 'Designs\DesignController@destroy');

    // Likes and Unlikes
    Route::post('designs/{id}/like', 'Designs\DesignController@like');
    Route::get('designs/{id}/liked', 'Designs\DesignController@checkIfUserHasLiked');

    // Comments
    Route::post('designs/{id}/comments', 'Designs\CommentController@store');
    Route::put('comments/{id}', 'Designs\CommentController@update');
    Route::delete('comments/{id}', 'Designs\CommentController@destroy');

    // Teams
    Route::post('teams', 'Teams\TeamsController@store');
    Route::get('teams/{id}', 'Teams\TeamsController@findById');
    Route::get('teams', 'Teams\TeamsController@index');
    Route::get('users/teams', 'Teams\TeamsController@fetchUserTeams');
    Route::put('teams/{id}', 'Teams\TeamsController@update');
    Route::delete('teams/{id}', 'Teams\TeamsController@destroy');

});

// Route group for guests only.

Route::group(['middleware' => 'guest:api'], function () {

    Route::post('register', 'Auth\registerController@register');
    Route::post('verification/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('verification/resend', 'Auth\VerificationController@resend');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

});
