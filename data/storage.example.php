<?php
if (!defined('POJOK_BERKAH_BOOTSTRAPPED')) {
    http_response_code(404);
    exit;
}
return [
    'wa_number' => '628000000000',
    'password_hash' => '',
    'articles' => [
        [
            'slug' => 'parkir-kamera-otomatis',
            'title' => 'Parkir Kamera Otomatis: Pengenalan Sistem Parkir Terpadu',
            'excerpt' => 'Kenali sistem parkir otomatis yang menghubungkan kamera, karcis, kiosk pembayaran, dan palang dalam satu alur. Pengelola dapat memantau transaksi serta bukti kendaraan, sementara pengendara menjalani proses masuk dan keluar secara mandiri.',
            'content' => <<<'ARTICLE'
Parkir Kamera Otomatis adalah aplikasi desktop untuk membantu pengelola menjalankan proses parkir dari gerbang masuk hingga gerbang keluar. Sistem ini menghubungkan kamera, sensor kendaraan, printer karcis, kiosk, palang, dan pencatatan transaksi agar kegiatan operasional lebih teratur dan mudah ditinjau.

Pengendara dapat mengikuti petunjuk di kiosk tanpa harus dilayani petugas di setiap gerbang. Sementara itu, pengelola tetap dapat memantau perangkat dan transaksi, serta melakukan pembukaan palang secara manual ketika diperlukan. Tindakan manual tersebut tercatat sebagai bagian dari jejak operasional.

Cara kerja di gerbang masuk
Saat kendaraan tiba, sensor mendeteksi keberadaannya dan kamera mengambil foto. Sistem kemudian mencetak karcis dengan kode unik bertanda tangan digital. Setelah karcis diambil, palang terbuka dan kendaraan dapat masuk. Foto dan catatan transaksi membantu pengelola meninjau kejadian jika muncul pertanyaan atau sengketa.

Cara kerja di gerbang keluar
Pengendara memindai karcis atau kartu member di kiosk. Sistem memeriksa data parkir dan menghitung tarif berdasarkan durasi serta golongan kendaraan. Pembayaran dapat dilakukan melalui QRIS yang terintegrasi dengan Midtrans. Setelah pembayaran terverifikasi, palang terbuka.

Fitur untuk mendukung operasional
Sistem menyediakan pencatatan foto kendaraan saat masuk dan keluar, kode karcis bertanda tangan digital, tarif berdasarkan golongan kendaraan, pembayaran QRIS, serta kartu member dengan mekanisme anti-passback. Tersedia pula alur penanganan karcis hilang dan tombol bantuan pada kiosk.

Pengelola dapat memantau status perangkat seperti printer, kamera, dan palang. Catatan transaksi dan pembukaan palang manual membantu menyediakan jejak audit yang lebih jelas.

Fitur yang masih direncanakan
Pembacaan plat nomor otomatis (ANPR), pencocokan plat saat kendaraan masuk dan keluar, pembatasan kapasitas, daftar hitam kendaraan, notifikasi Telegram, serta mode offline masih berada dalam rencana pengembangan. Fitur-fitur tersebut belum menjadi kemampuan yang dapat dijanjikan sebagai tersedia.

Pertanyaan umum
Apakah gerbang beroperasi tanpa petugas?
Pengendara dapat menjalani proses secara mandiri melalui kiosk. Pengelola tetap dapat memantau sistem dan membuka palang secara manual jika diperlukan; pembukaan manual dicatat.

Bagaimana jika karcis hilang?
Sistem menyediakan alur penanganan karcis hilang dengan denda sesuai tarif yang ditetapkan pengelola.

Apakah foto kendaraan disimpan?
Foto kendaraan saat masuk dan keluar disimpan sebagai bukti dan bahan pembanding transaksi. Kebijakan masa simpan dan akses foto perlu mengikuti kebijakan pengelola lokasi.

Perangkat apa yang dibutuhkan?
Aplikasi berjalan di komputer Windows yang terhubung ke perangkat yang digunakan, seperti kamera, printer, palang, kiosk, dan database.

Dengan menghubungkan proses gerbang, pembayaran, dokumentasi kendaraan, dan pemantauan perangkat, Parkir Kamera Otomatis membantu pengelola menjalankan operasional parkir secara lebih terstruktur. Kebutuhan perangkat dan alur pembayaran dapat dibahas sesuai kondisi lokasi.
ARTICLE,
            'status' => 'draft',
            'created_at' => '2026-10-17',
            'updated_at' => '2026-10-17',
        ],
    ],
];
