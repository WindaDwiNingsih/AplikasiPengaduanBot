<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;

class SetupTelegramBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Telegram Bot';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));

        // Set commands
        $commands = [
            [
                'command' => 'start',
                'description' => 'Mulai bot pengaduan'
            ],
            [
                'command' => 'aduan',
                'description' => 'Buat pengaduan baru'
            ],
            [
                'command' => 'status',
                'description' => 'Cek status pengaduan'
            ]
        ];

        $telegram->setMyCommands(['commands' => $commands]);

        $this->info('Bot commands setup successfully!');
    }
}
