<?php

namespace App\Http\Conversations; // PASTI HARUS app/Http/Conversations

use App\Models\Complaint;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Conversations\Conversation;

class ComplaintConversation extends Conversation
{
    protected $complaintData = [];

    // 1. Tanyakan Kategori
    public function askCategory()
    {
        $question = Question::create("Baik, pengaduan ini tentang apa?")
            ->addButtons([
                \BotMan\BotMan\Messages\Outgoing\Actions\Button::create('Infrastruktur')->value('infrastruktur'),
                \BotMan\BotMan\Messages\Outgoing\Actions\Button::create('Pelayanan Publik')->value('pelayanan'),
                \BotMan\BotMan\Messages\Outgoing\Actions\Button::create('Lain-lain')->value('lain'),
            ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->complaintData['category'] = $answer->getValue();
                $this->askTitle();
            } else {
                $this->say("Mohon pilih kategori dari tombol di bawah.");
                $this->askCategory();
            }
        });
    }

    // 2. Tanyakan Judul
    public function askTitle()
    {
        $this->ask('Apa judul singkat/garis besar dari pengaduan Anda?', function (Answer $answer) {
            $this->complaintData['title'] = $answer->getText();
            $this->askDetails();
        });
    }

    // 3. Tanyakan Detail
    public function askDetails()
    {
        $this->ask('Sekarang, jelaskan detail pengaduan selengkapnya.', function (Answer $answer) {
            $this->complaintData['details'] = $answer->getText();
            $this->saveComplaint();
        });
    }

    // 4. Simpan ke Database dan Konfirmasi
    public function saveComplaint()
    {
        $user_telegram_id = $this->bot->getUser()->getId();

        // Simpan data ke database
        $complaint = ComplaintConversation::create([
            'user_telegram_id' => $user_telegram_id,
            'title' => $this->complaintData['title'],
            'details' => $this->complaintData['details'],
            'category' => $this->complaintData['category'],
            'status' => 'Baru',
        ]);

        // Kirim konfirmasi ke pengguna
        $this->say("✅ **Pengaduan Terdaftar!**\n\nTerima kasih. Pengaduan Anda telah kami terima dengan ID: **#{$complaint->id}**.", ['parse_mode' => 'Markdown']);
    }

    // Mulai Percakapan
    public function run()
    {
        $this->askCategory();
    }
}
