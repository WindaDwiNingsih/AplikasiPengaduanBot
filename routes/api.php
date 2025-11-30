<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BotTelegramController;
use Illuminate\Support\Facades\Request;


Route::middleware('api')->group(function(){
    Route::get('/setWebhook', [BotTelegramController::class, 'setWebhook']);
    Route::post('/aduancepat_bot/webhook', [BotTelegramController::class, 'webHook']);
   
});


