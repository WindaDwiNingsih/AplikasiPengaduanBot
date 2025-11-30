<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use App\Services\CategoryService;

class AduanCommand extends Command
{
    protected string $name = 'aduan';
    protected string $description = 'Mulai membuat pengaduan baru';

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function handle()
    {
        $update = $this->getUpdate();
        $chatId = $update->getMessage()->getChat()->getId();

        // Ambil kategori utama dari service
        $mainCategories = $this->categoryService->getMainCategories();

        $keyboard = [];

        // Buat inline keyboard untuk kategori utama
        foreach ($mainCategories as $category) {
            $keyboard[] = [
                [
                    'text' => $category,
                    'callback_data' => $category // ⭐ LANGSUNG gunakan nama kategori
                ]
            ];
        }

        $text = "📋 *PILIH KATEGORI PENGADUAN*\n\n"
            . "Silakan pilih kategori yang sesuai dengan pengaduan Anda:";

        $this->replyWithMessage([
            'text' => $text,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard
            ])
        ]);
    }
}
