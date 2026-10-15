# Dokumentasi API — Pojok Berkah (admin.php)

> Base URL lokal: `http://localhost/pojok_berkah/admin.php`
> Base URL produksi: `https://pojokberkah.online/admin.php`
> Format: JSON untuk endpoint publik, `multipart/form-data` / `application/x-www-form-urlencoded` untuk aksi CMS. Auth CMS memakai cookie session + token CSRF.

## 1. GET konten publik — `GET admin.php?public=1`

Dipakai semua halaman publik via `fetch("admin.php?public=1", {cache:"no-store"})`.

Contoh request:

```js
fetch("admin.php?public=1", { cache: "no-store" })
  .then((r) => r.json())
  .then(console.log);
```

Contoh response (dipersingkat). Kolom status = dipakai `index.html` korporat atau arsip:

```json
{
  "wa_number": "628159501935",          // LIVE — semua tombol WA landing
  "faq": [{ "question": "...", "answer": "..." }],  // LIVE — FAQ korporat
  "images": {
    "logo": "",                          // LIVE — header + favicon
    "tryout": "https://images.unsplash.com/photo-...?w=960&q=80",   // ARSIP — Tentang Kami + 404
    "gadai": "https://images.unsplash.com/photo-...?w=960&q=80",    // ARSIP — Tentang Kami
    "kitchen": "https://images.unsplash.com/photo-...?w=960&q=80"   // ARSIP — Tentang Kami
  },
  "service_urls": {                     // ARSIP — Tentang Kami + subdomain, tak dipakai landing
    "tryout": "https://tryout.pojokberkah.online",
    "gadai": "https://gadai.pojokberkah.online",
    "kitchen": "https://kitchen.pojokberkah.online"
  },
  "service_names": { "tryout": "Tryout TKA", "gadai": "Gadai BPKB", "kitchen": "Alfi Kitchen" },  // ARSIP
  "stats": {                            // ARSIP — blok stats sudah dihapus dari landing
    "services": { "value": "3", "label": "layanan" },
    "gadai": { "value": "29", "label": "cabang gadai aktif" },
    "kitchen": { "value": "Always Fresh", "label": "komitmen Alfi Kitchen" }
  },
  "service_tags": { "tryout": ["Soal AI"], "gadai": ["Bandung"], "kitchen": ["Puding"] }  // ARSIP
}
```

Header: `Content-Type: application/json; charset=utf-8`, `Cache-Control: no-store`, `X-Robots-Tag: noindex, nofollow`.
Fallback: jika nomor tidak valid, homepage tetap memakai default `6287724039666`; jika gambar upload ada, nilainya menjadi `admin.php?image={slot}&v={12-char}`.

## 2. GET gambar upload — `GET admin.php?image={slot}`

- `slot`: `logo | tryout | gadai | kitchen`
- Syarat: nama file di storage cocok pola `[a-f0-9]{32}.(jpg|png|webp)` dan file ada di `data/uploads/`.
- Header: `Content-Type` sesuai ekstensi, `Cache-Control: public, max-age=86400`, `X-Robots-Tag: noindex, nofollow`.
- Error: `404` jika slot salah / file hilang.

Contoh: `GET admin.php?image=kitchen` → binary `image/jpeg`.

## 3. Aksi CMS — `POST admin.php`

Semua POST wajib menyertakan `csrf` (dari session) dan `action`. Jika CSRF salah → pesan "Sesi keamanan tidak valid".

| action | Auth | Field | Validasi |
|---|---|---|---|
| `setup` | belum ada `password_hash` | `password`, `password_confirmation` | 6–72 char, ada huruf+angka |
| `login` | belum login | `password` | max 5x salah / 15 mnt → lock 15 mnt |
| `save` | login | `wa_number` | 8–15 digit, `0...` → `62...` |
| `save_service_urls` | login | `service_urls[tryout|gadai|kitchen]` | URL penuh http/https, ≤2048 |
| `save_service_names` | login | `service_names[...]` | wajib isi, ≤400 byte |
| `save_service_descriptions` | login | `service_descriptions[...]` | wajib isi, ≤2000 byte |
| `save_service_tags` | login | `service_tags[slot]` (textarea, 1 tag/baris) | ≤8 tag/slot, tiap ≤240 byte |
| `save_stats` | login | `stats[slot][value]`, `stats[slot][label]` | wajib isi, value ≤320, label ≤480 |
| `save_faq` | login | `faq_questions[]`, `faq_answers[]` | 1–20 item, Q ≤320, A ≤4000 |
| `upload_image` | login | `image_slot`, `service_image` (file) | jpg/png/webp, ≤5MB, ≤12000px |
| `remove_image` | login | `image_slot` | kembali ke default |
| `change_password` | login | `current_password`, `new_password`, `new_password_confirmation` | aturan password + harus beda |
| `logout` | login | — | hapus session |

Contoh cURL login + simpan nomor (perlu cookie session dari GET dulu):

```bash
curl -c jar.txt "https://pojokberkah.online/admin.php" -o /dev/null
# salin token csrf dari HTML, lalu:
curl -b jar.txt -c jar.txt -X POST "https://pojokberkah.online/admin.php" \
  -d "csrf=TOKEN&action=save&wa_number=6281234567890"
```

Catatan: tidak ada API key / REST JSON untuk tulis — hanya form CMS.
