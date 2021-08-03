<?php


Route::get('/', function () {
    return view('welcome');
});


Route::post('/upload','Sales\salesController@uploadLargeFiles');

Route::get('/upload','Sales\salesController@index');
Route::get('/delete','Sales\salesController@delete');

// Route::post('/upload', [salesController::class, 'uploadLargeFiles'])->name('upload');



Route::get('/store','Sales\salesController@store');