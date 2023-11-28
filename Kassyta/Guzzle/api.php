
Route::prefix('v2')->gtoup(function(){
    Route::post('/http/guzzle',[EjemploApiController::class,'apiHttp']);
    Route::get('/adafruit',[EjemploApiController::class,'adafruit']);

});