<?php

namespace App\Http\Controllers;

use App\Services\ComplaintBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Laravel\Facades\Telegram as FacadesTelegram;

class BotTelegramController extends Controller
{
    protected $botService;

    public function __construct(ComplaintBotService $botService)
    {
        $this->botService = $botService;
    }

    public function webhook(Request $request)
    {
        try {
            // Dapatkan data dari Telegram
            $updates = Telegram::getWebhookUpdate();

            // SERAHKAN SEMUA HANDLING KE SERVICE
            $this->botService->handleWebhook($updates);

            return response()->json(['status' => 'success']);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error'], 500);
        }

    }
    
    public function setWebhook(){
        $response=Telegram::setWebhook(['url'=>env('TELEGRAM_WEBHOOK_URL')]);
        return $response;
    }

}