<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Models\Complaint;
use App\Models\AgencyCategory;
use Illuminate\Support\Facades\Cache;
use App\Services\CategoryService;

class ComplaintBotService
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    const STATE_CACHE_PREFIX = 'bot.state.';
    const STATE_CACHE_TTL = 3600;

    protected function getUserState($chatId)
    {
        return Cache::get(self::STATE_CACHE_PREFIX . $chatId, []);
    }

    protected function setUserState($chatId, $stateData)
    {
        Cache::put(self::STATE_CACHE_PREFIX . $chatId, $stateData, 3600);
    }

    protected function clearUserState($chatId)
    {
        Cache::forget(self::STATE_CACHE_PREFIX . $chatId);
    }

    public function handleWebhook($updates)
    {
        try {
            Log::info('Webhook received:', ['update_id' => $updates->getUpdateId()]);

            // 1. Biarkan Telegram handle commands terlebih dahulu
            if ($updates->getMessage() && str_starts_with($updates->getMessage()->getText(), '/')) {
                Telegram::commandsHandler(true);
                return true;
            }

            // 2. HANDLE CALLBACK QUERY (tombol sub kategori inline)
            if ($updates->has('callback_query')) {
                return $this->handleCallbackQuery($updates->getCallbackQuery());
            }

            // 3. HANDLE PHOTO UPLOADS
            if ($updates->getMessage() && $updates->getMessage()->has('photo')) {
                return $this->handlePhotoUpload($updates->getMessage());
            }

            // 4. HANDLE LOCATION
            if ($updates->getMessage() && $updates->getMessage()->has('location')) {
                return $this->handleLocation($updates->getMessage());
            }

            // 5. HANDLE REGULAR MESSAGES
            if ($updates->getMessage()) {
                return $this->handleMessage($updates->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('Error handling webhook: ' . $e->getMessage());
        }
    }

    protected function handleMessage($message)
    {
        $chatId = $message->getChat()->getId();
        $text = $message->getText() ?? '';
        $userState = $this->getUserState($chatId);
        $telegramUser = $message->getFrom();

        Log::info("Processing message:", [
            'chat_id' => $chatId,
            'text' => $text,
            'user_state' => $userState
        ]);

        // 1. PRIORITY TERTINGGI: Handle berdasarkan STATE USER
        if (
            isset($userState['main_category']) &&
            !isset($userState['title']) &&
            !in_array($text, ['Selesai', 'Upload Foto'])
        ) {
            Log::info("Handling as title");
            return $this->handleComplaintTitle($chatId, $text, $telegramUser);
        }

        if (
            isset($userState['title']) &&
            !isset($userState['description']) &&
            !in_array($text, ['Selesai', 'Upload Foto'])
        ) {
            Log::info("Handling as description");
            return $this->handleComplaintDescription($chatId, $text);
        }

        if (
            isset($userState['description']) &&
            $userState['step'] == 'lokasi' &&
            !isset($userState['location'])
        ) {
            Log::info("Handling as location text");
            return $this->handleLocationText($chatId, $text);
        }

        // 2. Handle tombol action
        if ($text === 'Upload Foto') {
            return $this->requestPhotoUpload($chatId);
        }

        if ($text === 'Selesai') {
            return $this->saveComplaint($chatId);
        }

        if ($text === 'Kirim Lokasi Sekarang') {
            return Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "Silakan kirim lokasi Anda dengan tombol GPS di keyboard"
            ]);
        }

        // 3. Handle kategori utama selection  
        $mainCategories = $this->categoryService->getMainCategories();
        if (in_array($text, $mainCategories)) {
            return $this->handleMainCategorySelection($chatId, $text, $telegramUser);
        }

        // 4. Handle tombol menu utama
        if ($text === 'Buat Pengaduan') {
            return $this->showCategoryOptions($chatId);
        }

        if ($text === 'Status Pengaduan') {
            $statusCommand = new \App\Telegram\Commands\StatuskuCommand();
            return $statusCommand->handle();
        }

        if ($text === 'Bantuan') {
            $bantuanCommand = new \App\Telegram\Commands\BantuanCommand();
            return $bantuanCommand->handle();
        }

        if ($text === 'Mulai Ulang') {
            $startCommand = new \App\Telegram\Commands\StartCommand();
            return $startCommand->handle();
        }

        // 5. Default response - JIKA TIDAK ADA STATE AKTIF
        if (empty($userState)) {
            return $this->sendDefaultMessage($chatId);
        }

        // 6. Jika ada state aktif tapi input tidak dikenali
        return Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "Input tidak dikenali. Silakan ikuti instruksi yang diberikan."
        ]);
    }

    /**
     * Handle pemilihan kategori utama - VERSI SIMPLE
     */
    protected function handleMainCategorySelection($chatId, $mainCategory, $telegramUser)
    {
        // ⭐ SIMPLE: Cek apakah ada sub kategori untuk main category ini
        if ($this->categoryService->hasSubCategories($mainCategory)) {
            return $this->showSubCategoryOptions($chatId, $mainCategory, $telegramUser);
        }

        // ⭐ SIMPLE: Langsung lanjut tanpa sub kategori
        return $this->proceedWithCategory($chatId, $mainCategory, null, null, null, $telegramUser);
    }

    /**
     * Tampilkan pilihan sub kategori - VERSI SIMPLE
     */
    protected function showSubCategoryOptions($chatId, $mainCategory, $telegramUser)
    {
        // ⭐ SIMPLE: Ambil sub categories berdasarkan main_category
        $subCategories = $this->categoryService->getSubCategories($mainCategory);

        $keyboard = [];

        foreach ($subCategories->chunk(2) as $chunk) {
            $row = [];
            foreach ($chunk as $subCategory) {
                $row[] = [
                    'text' => $subCategory->name,
                    'callback_data' => 'subcat_' . $subCategory->id
                ];
            }
            $keyboard[] = $row;
        }

        // Tambahkan tombol "Lainnya"
        $keyboard[] = [[
            'text' => 'Lainnya',
            'callback_data' => 'subcat_other'
        ]];

        $newState = [
            'pending_main_category' => $mainCategory,
            'step' => 'selecting_subcategory',
            'created_at' => now()->toISOString(),
            'session_id' => uniqid()
        ];

        if ($telegramUser) {
            $newState['telegram_user_id'] = $telegramUser->getId();
            $newState['first_name'] = $telegramUser->getFirstName() ?? null;
            $newState['last_name'] = $telegramUser->getLastName() ?? null;
            $newState['username'] = $telegramUser->getUsername() ?? null;
        }

        $this->setUserState($chatId, $newState);

        return Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "*Kategori: " . $mainCategory . "*\n\n"
                . "Silakan pilih *sub kategori* yang sesuai:",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard
            ])
        ]);
    }

    /**
     * Handle pemilihan sub kategori - VERSI SIMPLE
     */
    protected function handleSubCategorySelection($chatId, $callbackData, $telegramUser)
    {
        $userState = $this->getUserState($chatId);
        $mainCategory = $userState['pending_main_category'] ?? null;

        if (!$mainCategory) {
            return Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "Sesi telah kadaluarsa. Silakan mulai ulang dengan /aduan"
            ]);
        }

        $subCategoryName = null;
        $subCategoryId = null;
        $agencyId = null;

        if ($callbackData === 'subcat_other') {
            $subCategoryName = 'Lainnya';
            $agencyId = null;
        } else {
            // ⭐ SIMPLE: Ambil sub category langsung dari database
            $subCategoryId = str_replace('subcat_', '', $callbackData);
            $subCategory = AgencyCategory::with('agency')->find($subCategoryId);

            if ($subCategory) {
                $subCategoryName = $subCategory->name;
                $subCategoryId = $subCategory->id;
                $agencyId = $subCategory->agency_id;
            }
        }

        return $this->proceedWithCategory($chatId, $mainCategory, $subCategoryName, $subCategoryId, $agencyId, $telegramUser);
    }

    /**
     * Lanjutkan flow setelah kategori dipilih - VERSI SIMPLE
     */
    protected function proceedWithCategory($chatId, $mainCategory, $subCategoryName, $subCategoryId, $agencyId, $telegramUser)
    {
        $newState = [
            'main_category' => $mainCategory,
            'sub_category' => $subCategoryName,
            'agency_sub_category_id' => $subCategoryId,
            'agency_id' => $agencyId,
            'step' => 'title',
            'created_at' => now()->toISOString(),
            'session_id' => uniqid()
        ];

        if ($telegramUser) {
            $newState['telegram_user_id'] = $telegramUser->getId();
            $newState['first_name'] = $telegramUser->getFirstName() ?? null;
            $newState['last_name'] = $telegramUser->getLastName() ?? null;
            $newState['username'] = $telegramUser->getUsername() ?? null;
        }

        $this->setUserState($chatId, $newState);

        $text = "*Kategori: " . $mainCategory . "*";
        if ($subCategoryName) {
            $text .= "\n*Sub Kategori: " . $subCategoryName . "*";
        }
        $text .= "\n\nSekarang silakan tuliskan *judul singkat* untuk pengaduan Anda:\n\n"
            . "Contoh:\n"
            . "\"Jalan Rusak di Depan Pasar\"\n"
            . "\"Sampah Menumpuk di Gang 5\"\n"
            . "\"Lampu Jalan Mati di Perumahan\"";

        return Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown'
        ]);
    }

    // ⭐ METHOD-METHOD BERIKUTNYA TETAP SAMA (tidak perlu perubahan)
    // handleComplaintTitle, handleComplaintDescription, handleLocation, 
    // handleLocationText, handlePhotoUpload, requestPhotoUpload, 
    // showCategoryOptions, saveComplaint, formatSuccessMessage, 
    // sendDefaultMessage, handleCallbackQuery

    protected function handleComplaintTitle($chatId, $title, $telegramUser)
    {
        $userState = $this->getUserState($chatId);
        $userState['title'] = $title;
        $userState['step'] = 'description';
        $this->setUserState($chatId, $userState);

        $text = "*Judul: " . $title . "*\n"
            . "*Kategori: " . ($userState['main_category'] ?? 'Tidak ada kategori') . "*";

        if (isset($userState['sub_category'])) {
            $text .= "\n*Sub Kategori: " . $userState['sub_category'] . "*";
        }

        $text .= "\n\nSekarang silakan tuliskan *deskripsi lengkap* pengaduan Anda:\n\n"
            . "Contoh:\n"
            . "\"Jalan rusak parah di depan pasar, mengganggu lalu lintas dan membahayakan pengendara. Lubangnya cukup dalam dan sudah beberapa minggu tidak diperbaiki.\"";

        return Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown'
        ]);
    }

    protected function handleComplaintDescription($chatId, $description)
    {
        $userState = $this->getUserState($chatId);
        $userState['description'] = $description;
        $userState['step'] = 'lokasi';
        $this->setUserState($chatId, $userState);

        $keyboard = [
            'keyboard' => [
                [
                    ['text' => 'Lanjut Upload Foto'],
                    ['text' => 'Selesai']
                ]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        return Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "*Deskripsi berhasil disimpan!*\n\n"
                . "Sekarang silakan kirim *lokasi* kejadian:\n\n"
                . "• Klik 'Kirim Lokasi Sekarang' untuk share GPS\n"
                . "• Atau ketik alamat lengkap secara manual",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    protected function handleLocation($message)
    {
        $chatId = $message->getChat()->getId();
        $location = $message->getLocation();
        $userState = $this->getUserState($chatId);

        if (!isset($userState['step']) || $userState['step'] !== 'lokasi') {
            return $this->sendDefaultMessage($chatId);
        }

        $userState['location'] = [
            'latitude' => $location->getLatitude(),
            'longitude' => $location->getLongitude(),
            'type' => 'gps',
            'address' => 'Lokasi GPS: ' . $location->getLatitude() . ', ' . $location->getLongitude()
        ];
        $userState['step'] = 'photo';
        $this->setUserState($chatId, $userState);

        return $this->requestPhotoUpload($chatId);
    }

    protected function handleLocationText($chatId, $text)
    {
        $userState = $this->getUserState($chatId);
        $userState['location'] = [
            'address' => $text,
            'type' => 'text'
        ];
        $userState['step'] = 'photo';
        $this->setUserState($chatId, $userState);
        return $this->requestPhotoUpload($chatId);
    }

    protected function handlePhotoUpload($message)
    {
        $chatId = $message->getChat()->getId();
        $userState = $this->getUserState($chatId);

        if (!isset($userState['step']) || !in_array($userState['step'], ['photo', 'complete'])) {
            return $this->sendDefaultMessage($chatId);
        }

        $photos = $message->getPhoto();
        $array_photos = $photos->toArray();

        Log::info($array_photos[count($array_photos) - 1]['file_id']);

        $fileId = $array_photos[count($array_photos) - 1]['file_id'];

        if (empty($array_photos)) {
            return Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "Gagal merekam photo. Pastikan anda mengirimkan gambar (bukan dokumen atau format lain). Jika file yang dikirim sebagai file, pastikan itu berformat JPG/PNG",
                'parse_mode' => 'Markdown'
            ]);
        }

        if (!isset($userState['photos'])) {
            $userState['photos'] = [];
        }

        $userState['photos'][] = [
            'file_id' => $fileId,
        ];
        $userState['step'] = 'complete';
        $this->setUserState($chatId, $userState);

        $keyboard = [
            'keyboard' => [
                [
                    ['text' => 'Upload Foto Lagi'],
                    ['text' => 'Selesai']
                ]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        $photoCount = count($userState['photos']);

        return Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "*Foto berhasil diupload!* (" . $photoCount . " foto)\n\n"
                . "Anda bisa:\n"
                . "• Upload foto tambahan\n"
                . "• Selesaikan pengaduan",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    protected function requestPhotoUpload($chatId)
    {
        $keyboard = [
            'keyboard' => [
                [
                    ['text' => 'Upload Foto'],
                    ['text' => 'Selesai']
                ]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        return Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "*Upload Foto* (Opsional)\n\n"
                . "Silakan upload foto pendukung:\n"
                . "• Klik clip (📎) di input chat\n"
                . "• Pilih 'Photo' atau 'Gallery'\n"
                . "• Pilih foto yang ingin diupload\n\n"
                . "Atau klik 'Selesai Tanpa Foto' untuk lanjut",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    protected function showCategoryOptions($chatId)
    {
        $aduanCommand = new \App\Telegram\Commands\AduanCommand();
        return $aduanCommand->handle();
    }

    protected function saveComplaint($chatId)
    {
        $data = $this->getUserState($chatId);

        if (!isset($data['main_category']) || !isset($data['description'])) {
            $this->clearUserState($chatId);
            return Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "Tidak ada data pengaduan yang bisa disimpan.\n\nKetik /aduan untuk memulai."
            ]);
        }

        try {
            $userId = $data['telegram_user_id'] ?? $chatId;
            $firstName = $data['first_name'] ?? 'Anonim';
            $username = $data['username'] ?? null;
            $fullName = trim($firstName . ' ' . ($data['last_name'] ?? ''));

            // ⭐ SIMPLE: Ambil agency_id dari sub category atau fallback
            $agencyId = $data['agency_id'] ?? null;

            if (!$agencyId) {
                $fallbackMapping = [
                    'Infrastruktur' => 2,
                    'Lingkungan' => 3,
                    'Pendidikan' => 5,
                    'Kesehatan' => 1,
                    'Transportasi' => 4,
                    'Lainnya' => null
                ];
                $agencyId = $fallbackMapping[$data['main_category']] ?? null;
            }

            $locationInfo = "Belum Diisi";
            $locationData = null;

            if (isset($data['location'])) {
                if ($data['location']['type'] === 'gps') {
                    $locationInfo = "📍 Lokasi GPS: " . $data['location']['latitude'] . ", " . $data['location']['longitude'];
                    $locationData = [
                        'type' => 'gps',
                        'latitude' => $data['location']['latitude'],
                        'longitude' => $data['location']['longitude'],
                        'address' => $data['location']['address'] ?? $locationInfo
                    ];
                } else if ($data['location']['type'] === 'text') {
                    $locationInfo = $data['location']['address'];
                    $locationData = [
                        'type' => 'text',
                        'address' => $data['location']['address']
                    ];
                }
            }

            $complaint = Complaint::create([
                'telegram_chat_id' => $chatId,
                'agency_id' => $agencyId,
                'agency_sub_category_id' => $data['agency_sub_category_id'] ?? null,
                'telegram_user_id' => $userId,
                'telegram_username' => $fullName,
                'title' => $data['title'] ?? 'Tidak ada judul',
                'category' => $data['main_category'] ?? 'Tidak ada kategori',
                'sub_category' => $data['sub_category'] ?? null,
                'description' => $data['description'] ?? 'Tidak ada deskripsi',
                'location' => $locationData,
                'photos' => $data['photos'] ?? [],
                'status' => 'pending',
                'submitted_at' => now()
            ]);

            $responseText = $this->formatSuccessMessage($data, $locationInfo, $complaint->id);

            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $responseText,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode(['remove_keyboard' => true])
            ]);

            $this->clearUserState($chatId);

            Log::info("Complaint saved: ID {$complaint->id}", [
                'main_category' => $data['main_category'],
                'sub_category' => $data['sub_category'] ?? null,
                'agency_id' => $agencyId,
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving complaint: ' . $e->getMessage());
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "Gagal menyimpan pengaduan. Silakan coba lagi."
            ]);
        }
    }

    protected function formatSuccessMessage($data, $locationInfo, $complaintId)
    {
        $text = "*PENGAJUAN BERHASIL!*\n\n"
            . " *Judul:* " . ($data['title'] ?? 'Tidak ada') . "\n"
            . " *Kategori:* " . ($data['main_category'] ?? 'Tidak ada') . "\n";

        if (isset($data['sub_category'])) {
            $text .= " *Sub Kategori:* " . $data['sub_category'] . "\n";
        }

        $text .= " *Deskripsi:* " . (strlen($data['description'] ?? '') > 100 ? substr($data['description'], 0, 100) . "..." : $data['description'] ?? 'Tidak ada') . "\n"
            . " *Lokasi:* " . $locationInfo . "\n"
            . " *Foto:* " . (count($data['photos'] ?? []) ? count($data['photos']) . " foto" : "Tidak ada") . "\n"
            . " *Status:* Pending\n\n"
            . " *No. Tiket:* " . $complaintId . "\n"
            . " Tim kami akan segera menindaklanjuti.\n\n"
            . " Gunakan /statusku untuk cek perkembangan.";

        return $text;
    }

    protected function sendDefaultMessage($chatId)
    {
        return Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "Silakan pilih menu di keyboard atau ketik:\n"
                . "/start - Menu utama\n"
                . "/aduan - Buat pengaduan\n"
                . "/status - Cek status\n"
                . "/bantuan - Panduan"
        ]);
    }

    protected function handleCallbackQuery($callbackQuery)
    {
        $message = $callbackQuery->getMessage();
        $chatId = $message->getChat()->getId();
        $data = $callbackQuery->getData();
        $user = $callbackQuery->getFrom();

        Log::info("Callback query received", [
            'chat_id' => $chatId,
            'callback_data' => $data,
            'user_id' => $user->getId()
        ]);

        Telegram::answerCallbackQuery([
            'callback_query_id' => $callbackQuery->getId()
        ]);

        $mainCategories = $this->categoryService->getMainCategories();

        // Handle sub category selection
        if (str_starts_with($data, 'subcat_')) {
            Log::info("Handling as sub category selection", ['callback_data' => $data]);
            return $this->handleSubCategorySelection($chatId, $data, $user);
        }

        // Handle main category selection
        if (in_array($data, $mainCategories)) {
            Log::info("Handling as main category selection", ['category' => $data]);
            return $this->handleMainCategorySelection($chatId, $data, $user);
        }

        Log::warning("Unknown callback data", [
            'callback_data' => $data,
            'available_categories' => $mainCategories
        ]);

        return Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "Kategori tidak dikenali. Silakan pilih kategori yang tersedia."
        ]);
    }
}
