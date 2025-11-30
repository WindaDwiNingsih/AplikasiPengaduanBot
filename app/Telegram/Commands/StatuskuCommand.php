<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use App\Models\Complaint;

class StatuskuCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected  String $name = 'statusku';

    /**
     * @var string Command Description
     */
    protected String $description = 'Cek status pengaduan yang pernah diajukan';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $chatId = $this->getUpdate()->getMessage()->getChat()->getId();

        // Cari pengaduan user
        $complaints = Complaint::where('telegram_user_id', $chatId)
            ->orderBy('created_at', 'desc')
            ->limit(5) // Batasi 5 pengaduan terbaru
            ->get();

        if ($complaints->count() == 0) {
            $text = "*Status Pengaduan*\n\n"
                . "Anda belum pernah membuat pengaduan.\n\n"
                . "Ketik /aduan untuk membuat pengaduan pertama Anda.";
        } else {
            $text = " *STATUS PENGAJUAN ANDA*\n\n";

            foreach ($complaints as $index => $complaint) {
                $statusText = $this->getStatusText($complaint->status);
                $text .= "──────────────────────\n";
                $text .= "*Pengaduan " . ($index + 1) . "*\n";
                $text .= "NO Tiket " . $complaint->id ."\n";
                $text .= "Kategori: " . $complaint->category . "\n";
                $text .= "Tanggal: " . $complaint->created_at->format('d/m/Y H:i') . "\n";
                $text .= "Status: " . $statusText . "\n";

                if ($complaint->admin_notes) {
                    $text .= "Catatan: " . $complaint->admin_notes . "\n";
                }

                $text .= "\n";
            }

            $text .= "Total: " . $complaints->count() . " pengaduan";
        }

        $this->replyWithMessage([
            'text' => $text,
            'parse_mode' => 'Markdown'
        ]);
    }
    /**
     * Get status text in Indonesian
     */
    private function getStatusText($status)
    {
        switch ($status) {
            case 'pending':
                return 'Menunggu';
            case 'process':
                return 'Diproses';
            case 'resolved':
                return 'Selesai';
            case 'rejected':
                return 'Ditolak';
            default:
                return $status;
        }
    }
}
