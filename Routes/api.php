<?php

use Orion\Facades\Orion;


Route::group([
    'as' => 'api.media.',
    'prefix' => 'media',
    'middleware' => 'auth:api'
  ], function ($router) {
  

    Route::post('upload', 'Api\MediaController@store')->name("upload");
    
    Orion::resource('images', 'Api\MediaController');

    Orion::resource('media', 'Api\MediaController');
   


    Route::group([
      'prefix' => 'categories',
      'middleware' => 'auth.both:api'
    ], function ($router) {

                  
        Orion::resource('/', 'Api\CategoriesController',['only' => ['index', 'show', 'search']]);
        
        Route::post('{type}/search', 'Api\CategoriesController@children');
    
        Route::post('{type}/tree', 'Api\CategoriesController@tree')->name("categories.tree");
          

    });


});



Route::group([
  'as' => 'api.folders.',
  'middleware' => 'auth:api'
], function ($router) {

    Route::post('move_to_folder', 'Api\MediaController@moveToFolder')->name("move");;

    Route::get('folders', 'Api\FolderController@index')->name("index");

    Route::post('folders', 'Api\FolderController@store')->name("store");

});