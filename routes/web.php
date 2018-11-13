<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::group(['middleware' => ['cors']], function () {

    Route::apiResource('mallnavs', 'Api\Malls\MallNavController');
    Route::apiResource('mallgoods', 'Api\Malls\MallGoodController');
    Route::apiResource('activitys','Api\Activities\ActivityController');

});
Route::apiResource('mallnavs', 'Api\Malls\MallNavController');
Route::apiResource('mallgoods', 'Api\Malls\MallGoodController');
Route::post('mallgoods/{mallgood}','Api\Malls\MallGoodController@show')->name('mallgoods');
Route::apiResource('mallswipers', 'Api\Malls\MallSwiperController');
Route::apiResource('mallgroups', 'Api\Malls\MallSwiperGroupController');
Route::apiResource('activitys','Api\Activities\ActivityController');

Route::post('activitys/{activity}','Api\Activities\ActivityController@show')->name('activitys');
