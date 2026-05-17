<?php

Route::group([
  'prefix' => "media",
  'as' => 'media.',
], function ($router) {

  Route::get('show/{filename}', 'UserMediaController@show')->name("public.show");
 
  Route::get('download/{filename}', 'UserMediaController@download')->name("public.download");

 // Route::get('download_all', 'UserMediaController@downloadAll')->name("download-all");

});


Route::group([
    "middleware" => ["web","auth","role:admin|editor|analyzer|guest"],
], function ($router) {


  Route::resource('media', 'MediaController');


});