<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;

class AduanJalanCommand extends Command
{
    protected string $name = 'aduanjalan';
    protected array $aliases = ['subscribe'];
    protected string $description = 'Form untuk mengisi aduan jalan';

    public function handle()
    {
        $this->replyWithMessage([
            'text' => 'Silahkan isi form ',
        ]);
    }
}
