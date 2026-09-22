# Fake Klikresi Tracking (Offline Testing)

Fitur ini mengganti pemanggilan API **tracking Klikresi** dengan data palsu agar bisa
menguji alur tracking (resi, timeline, auto-status) **tanpa koneksi ke Klikresi**.
Ongkir (`rate()`) **tidak** ikut dipalsukan — tetap menggunakan API asli.

## Cara Kerja

`FakeKlikresiApi extends KlikresiApi` dan hanya menimpa method `tracking()`.
Karena `CheckoutStatusService` dan `PesananController` hanya tahu `KlikresiApi`,
cukup satu binding di `AppServiceProvider` yang memilih instance asli atau palsu
berdasarkan flag konfigurasi.

Saat `TRACKING_FAKE=true`, `tracking()` **menerima resi apa pun**:

- Resi berisi kata kunci `DEL`/`TRK`/`PIC` → memakai **data palsu** (progress simulasi).
- Resi lain → diteruskan ke **API Klikresi asli** (`parent::tracking()`), jadi resi
  asli tetap divalidasi dan di-track lewat API. Resi sampah ditolak API ("tidak valid").

Saat `TRACKING_FAKE=false`, semua tracking langsung ke API asli (tanpa fallback palsu).

## Mengaktifkan

1. Set di `.env`:

   ```dotenv
   TRACKING_FAKE=true
   ```

2. Bersihkan config cache (jika ada):

   ```bash
   php artisan config:clear
   ```

`false` / dihapus => tracking Klikresi asli seperti semula.

## Contoh Resi per Status

| Resi contoh | Status simulasi | Efek pada pesanan |
|---|---|---|
| `JXDEL001` | Delivered | `kirim` diterima; otomatis lanjut ke status `delivered` |
| `JXTRK001` | In transit | `kirim` diterima; tetap `shipping` |
| `JXPIC001` | Picked up | `kirim` diterima; tetap `shipping` |
| `JX999999` | Tidak valid | diteruskan ke API asli; jika resi asli valid akan diterima, jika sampah ditolak ("No resi tidak valid...") |

> Kata kunci: `DEL`, `TRK`, `PIC` — case-insensitive, boleh di posisi mana pun
> dalam nomor resi.

## Format Response

Response palsu meniru format asli Klikresi (setelah unwrap `data`):

```php
[
    'status'    => 'Delivered',   // | 'InTransit'
    'histories' => [
        ['status' => 'InTransit', 'message' => 'Paket akan dikirim ke alamat penerima.', 'date' => '2026-09-22T08:00:00+07:00'],
        ['status' => 'Delivered', 'message' => 'Item has been delivered.',             'date' => '2026-09-22T10:00:00+07:00'],
    ],
]
```

`delivered` otomatis (auto-status) dipicu oleh kata "Delivered"/"sampai"/"terkirim"
dalam response, sesuai `hasDeliveredMarker()` di `CheckoutStatusService`.

## File Terkait

- `app/Services/FakeKlikresiApi.php` — implementasi palsu
- `app/Providers/AppServiceProvider.php` — binding swap `KlikresiApi`
- `config/services.php` — `services.klikresi.tracking_fake`
- `.env` / `.env.example` — `TRACKING_FAKE`
