<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;

class BantuanCommand extends Command
{
    protected string $name = 'bantuan';
    protected array $aliases = ['bantu', 'panduan'];
    protected string $description = 'Menampilkan panduan penggunaan bot';

    public function handle()
    {
        $text = " **Panduan Penggunaan Bot Pengaduan**" . PHP_EOL . PHP_EOL .
            "**Perintah yang tersedia:**" . PHP_EOL .
            "• /start - Memulai bot dan melihat menu" . PHP_EOL .
            "• /aduan - Membuat pengaduan baru" . PHP_EOL .
            "• /statusku - Melihat status pengaduan" . PHP_EOL .
            "• /bantuan - Menampilkan panduan ini" . PHP_EOL . PHP_EOL .

            "**Cara membuat pengaduan:**" . PHP_EOL .
            "1. Ketik /aduan" . PHP_EOL .
            "2. Pilih kategori pengaduan" . PHP_EOL .
            "3. Kirim deskripsi pengaduan Anda" . PHP_EOL .
            "4. Pengaduan akan diproses oleh admin" . PHP_EOL . PHP_EOL .

            "**Cara mengecek status:**" . PHP_EOL .
            "Ketik /statusku untuk melihat semua pengaduan yang telah Anda buat." .
            "*Bantuan Lainnya:*\n" .
            "Hubungi admin: 08123456789";

        $this->replyWithMessage([
            'text' => $text,
            'parse_mode' => 'Markdown'
        ]);
    }
}
