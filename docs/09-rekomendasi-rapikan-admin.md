# 09 — Rekomendasi Rapikan CMS Admin pasca Landing Korporat

> Update 16 Okt 2026: Paket A + Paket B SELESAI — urutan panel WA/FAQ/Logo di atas,
> arsip dalam `<details>` lipat, logo kartu tunggal, docs 02/03 ditandai. Teruji `php -l` + `?public=1`.
> Update 16 Okt 2026 (revisi): CMS modularisasi ke folder `admin/` — `admin.php` thin wrapper,
> `admin/index.php` router, `admin/lib/`, `admin/actions/`, `admin/views/`. Session `session_start()`
> dipindah ke `admin/index.php` sebelum cek `$_SESSION`.
> Tanggal: 16 Okt 2026. Status: EKSEKUSI — modularisasi selesai, sisa rekomendasi UI opsional.
> Konteks: `index.html` sudah Opsi A (korporat, tanpa produk). CMS admin sudah modular.

## 1. Temuan audit (kenapa admin terasa berantakan)

Landing baru HANYA memakai 3 kunci dari `GET admin.php?public=1`:

| Dipakai landing | Tidak dipakai landing |
|---|---|
| `wa_number` (semua tombol `wa.me/`) | `service_urls`, `service_names`, `service_descriptions`, `service_tags` |
| `images.logo` saja | `images.tryout/gadai/kitchen`, `stats.*` |
| `faq` (ditimpa via JS `faq.replaceChildren()`) | — |

Masalah turunan:

1. **BUG KRITIS — default FAQ menimpa FAQ korporat.** `$defaultFaq` di `admin/index.php:62-67` masih 4 item produk. Fallback statis `index.html` + JSON-LD sudah korporat. Bila storage kosong, API mengembalikan FAQ produk lalu JS (`index.html`) menghapus FAQ korporat dan menggantinya dengan FAQ produk.
2. **4 panel yatim:** `Konten layanan` (`admin/views/tab-layanan.php`), `Statistik beranda` (`admin/views/tab-statistik.php`), 3 slot gambar non-logo (`admin/views/tab-gambar.php`) — tidak dirender landing tapi tampil paling menonjol di admin.
3. **Copy basi:** lead di `admin/index.php` menyebut "nama, alamat, deskripsi, gambar, statistik" (tak ada field alamat); copy di views menyebut "tampil di kartu, footer, Tentang Kami" (kartu sudah dihapus).
4. **Pengecualian:** `tentang-kami.html:142-175,197-219` MASIH memakai `service_*` + 3 gambar + `wa_number` + `logo`. `404.html:29,37` masih memakai `img[data-image-slot]` tryout. Jadi backend TIDAK boleh dihapus total.

## 2. Prinsip

- Frontend abaikan, backend tetap simpan (lanjutan Opsi A, `docs/05` §5). Rollback via Git.
- Jangan ubah skema `data/storage.php`, jangan ubah signature `?public=1` / `?image=`.
- Yang diubah: `$defaultFaq`, label/copy, urutan panel, badge status.
## 3. Rekomendasi (urut prioritas)

### R0 — Wajib: selaraskan default FAQ (tanpa ubah UI)
Ganti `$defaultFaq` dengan 4 item yang SAMA PERSIS dengan fallback `index.html`:
Apa itu Pojok Berkah, cara hubungi admin, apakah perlu akun, di mana info resmi.
Instalasi lama yang storage-nya sudah berisi FAQ produk perlu 1x Simpan ulang.

### R1 — Panel FAQ + WA + Logo naik ke atas
Urutan baru: 1) Nomor WA, 2) Logo situs, 3) FAQ, 4) sisanya masuk `<details>` arsip.

### R2 — Bungkus panel yatim dalam `<details>` arsip
`Konten layanan` dan `Statistik beranda` dibungkus details berlabel
"Arsip — tidak tampil di landing, tetap tersimpan untuk Tentang Kami/subdomain".
Handler POST (`save_service_*`, `save_stats`) TIDAK diubah, tetap jalan.

### R3 — Gambar: pecah logo vs arsip
Slot `logo` jadi kartu tunggal paling atas. 3 slot lain tetap di grid tapi dalam
`<details>` "Gambar arsip (dipakai Tentang Kami dan 404)".

### R4 — Perbaiki copy basi
Lead `771` dan copy `848/852/882/917/966` ditulis ulang agar tak menyebut
kartu beranda atau 3 layanan.

### R5 — Badge status per panel (CSS sekitar 10 baris)
`.badge-live` hijau (Dipakai landing) untuk WA/Logo/FAQ;
`.badge-archive` abu (Arsip) untuk 4 panel yatim.

### R6 — Dok API ditandai aktif vs arsip
Update `docs/02` pasal 1 dan `docs/03` pasal 2-3: alur baca baru hanya
`wa_number`, `images.logo`, `faq`.

### R7 — Opsional tahap 2 (JANGAN sekarang)
Hapus total `service_*/stats` dari backend DITOLAK dulu karena Tentang Kami
masih butuh. CMS untuk testimoni/cara-kerja juga ditunda.

## 4. Paket eksekusi

| Paket | Isi | Risiko |
|---|---|---|
| A — Minimal aman (disarankan) | R0+R4+R5 (sekitar 30 baris) | Nyaris nol |
| B — Rapi penuh | A + R1+R2+R3+R6 (sekitar 1 jam + uji `?public=1`) | Kecil, handler tetap |
| C — Bersih total | B + hapus backend legacy | TINGGI — merusak Tentang Kami/404. Tidak disarankan |

## 5. Kriteria selesai
- Storage kosong menghasilkan `?public=1` dengan FAQ korporat.
- Sisa kata Tryout/Gadai/Kitchen di admin.php hanya dalam blok arsip berlabel.
- Semua POST lama tetap 200; index.html tanpa error console.
