<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () { return view('welcome'); });

Route::get('static/article/{id}', 'ArticleController@staticShow');
Route::get('dynamic/article/{id}', 'ArticleController@dynamicShow');
Route::get('generate/static_test_urls', 'ArticleController@generateStaticUrls');
Route::get('generate/dynamic_test_urls', 'ArticleController@generateDynamicUrls');
Route::get('generate/static_files', 'ArticleController@generateStaticFiles');

Route::group(['prefix' => 'install'], function() {
    // index
    Route::get('/', 'InstallController@index');
    // step one
    Route::get('step_one', 'InstallController@stepOne');
    // step one handler
    Route::post('step_one', 'InstallController@stepOneHandler');
});

Route::group(['prefix' => 'home'], function() {
    // index
    Route::get('/', 'Home\IndexController@index');

    // template management
    Route::resource('template', 'Home\TemplateController');
    Route::get('template/get_template/{template}', 'Home\TemplateController@getTemplate');
    Route::delete('template/delete_template_section/{section}', 'Home\TemplateController@deleteTemplateSection');

    // category management
    Route::resource('category', 'Home\CategoryController');
    Route::get('category/get_category_template_list/{category}', 'Home\CategoryController@getCategoryTemplateList');
    Route::get('category/get_available_parent_categories/{department}', 'Home\CategoryController@getAvailableParentCategories');

    // article management
    Route::resource('article', 'Home\ArticleController');

    // department management
    Route::resource('department', 'Home\DepartmentController');

    // user management
    Route::resource('user', 'Home\UserController');
    Route::get('user/get_available_roles/{template}', 'Home\UserController@getAvailableRoles');
    Route::get('user/get_category_managers/{department}', 'Home\UserController@getCategoryManagers');
});

// 暂时还未涉足
// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');
