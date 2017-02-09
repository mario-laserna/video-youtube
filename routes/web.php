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

Route::get('/', 'VideoController@index')->name('video.index');
Route::post('/video', 'VideoController@store')->name('video.store');
Route::get('/video/transform', 'VideoController@transform')->name('video.transform');

Route::get('youtube', 'YoutubeController@youtube')->name('youtube');
Route::get('video/youtube', 'YoutubeController@show')->name('video.youtube');
