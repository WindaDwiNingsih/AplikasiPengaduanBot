<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class StartCommand extends Command
{
    protected string $name = 'start';
    //protected array $aliases = ['subscribe'];
    protected string $description = 'Untuk memulai percakapan dengan bot';

    public function handle()
    {
        $chatId = $this->getUpdate()->getMessage()->getChat()->getId();

        $firstName = $this->getUpdate()->getMessage()->getChat()->getFirstName();

        // $keyboard = [
        //     'keyboard' => [
        //         [
        //             ['text' => ' Buat Pengaduan'],
        //             ['text' => ' Status Pengaduan']
        //         ],
        //         [
        //             ['text' => ' Bantuan'],
        //             ['text' => ' Mulai Ulang']
        //         ]
        //     ],
        //     'resize_keyboard' => true,
        //     'one_time_keyboard' => false
        // ];

        $text = "👋 Halo *{$firstName}*! Selamat datang di *Bot Pengaduan*.\n\n"
            . "Saya siap membantu Anda menyampaikan pengaduan dengan mudah dan cepat.\n\n"
            . "📋 *Menu Utama:*\n"
            . "• 📋 Buat Pengaduan - Ajukan pengaduan baru\n"
            . "• 📊 Status Pengaduan - Cek status pengaduan\n"
            . "• ℹ️ Bantuan - Panduan penggunaan\n"
            . "Silakan pilih menu di bawah atau ketik perintah:\n"
            . "/aduan - Buat pengaduan baru\n"
            . "/statusku - Cek status pengaduan\n"
            . "/bantuan - Tampilkan bantuan";

        $this->replyWithMessage([
            'text' => $text,
            'parse_mode' => 'Markdown',
            // 'reply_markup' => json_encode($keyboard)
        ]);
        
    }
}
