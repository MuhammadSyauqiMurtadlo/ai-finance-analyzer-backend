# AI Finance Analyzer Backend

Backend API untuk mencatat transaksi pemasukan dan pengeluaran, melihat ringkasan kondisi keuangan, serta meminta analisis berbasis AI. Proyek ini dibuat dengan Laravel dan menyediakan data dalam format JSON agar dapat digunakan oleh aplikasi web, mobile, atau client API lainnya.

## Gambaran singkat

Dengan API ini, pengguna dapat:

- Menambah, melihat, memperbarui, dan menghapus transaksi.
- Melihat total pemasukan, pengeluaran, saldo, grafik enam bulan terakhir, ringkasan kategori, dan transaksi terbaru.
- Meminta AI merangkum pola keuangan dan memberikan saran berdasarkan data transaksi.

**Untuk pembaca nonteknis dan HRD:** proyek ini menunjukkan contoh backend yang menggabungkan REST API, validasi data, database relasional, dan integrasi layanan AI. AI menerima ringkasan angka transaksi, bukan catatan pribadi pada setiap transaksi.

## Teknologi

- PHP 8.3 atau lebih baru
- Laravel 13
- MySQL atau MariaDB untuk penggunaan dashboard
- Layanan AI yang menyediakan API kompatibel dengan format OpenAI Chat Completions
- PHPUnit/Pest untuk pengujian

## Persyaratan

Pastikan perangkat Anda telah memasang:

- PHP 8.3+
- Composer
- MySQL atau MariaDB
- Git (opsional, untuk mengunduh proyek)

Node.js dan npm hanya diperlukan bila Anda mengembangkan atau membangun aset frontend bawaan Laravel. Fungsi API tidak memerlukan proses build frontend.

## Menjalankan proyek secara lokal

1. Unduh proyek dan masuk ke foldernya:

   ```bash
   git clone <URL_REPOSITORY>
   cd ai-finance-analyzer-backend
   ```

2. Pasang dependency PHP:

   ```bash
   composer install
   ```

3. Buat file konfigurasi lokal dari contoh:

   ```bash
   cp .env.example .env
   ```

   Di Windows PowerShell, gunakan:

   ```powershell
   Copy-Item .env.example .env
   ```

4. Buat database MySQL/MariaDB kosong, lalu sesuaikan pengaturan berikut di `.env`:

   ```dotenv
   APP_NAME="AI Finance Analyzer"
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ai_finance_analyzer
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   Isi `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` sesuai database lokal Anda. Dashboard menggunakan fungsi tanggal MySQL (`DATE_FORMAT`), sehingga MySQL/MariaDB diperlukan untuk fitur dashboard dan analisis AI.

5. Buat application key dan jalankan migrasi database:

   ```bash
   php artisan key:generate
   php artisan migrate
   ```

6. (Opsional) Atur koneksi AI pada `.env` untuk mengaktifkan endpoint analisis:

   ```dotenv
   AI_BASE_URL=https://api.openai.com/v1
   AI_API_KEY=isi_dengan_api_key_anda
   AI_MODEL=nama_model_yang_tersedia
   AI_TIMEOUT=30
   ```

   `AI_BASE_URL` harus merupakan URL dasar provider yang kompatibel dengan endpoint `/chat/completions`. Gunakan model yang tersedia pada provider pilihan Anda. Jangan memasukkan API key ke Git atau membagikannya.

7. Jalankan server lokal:

   ```bash
   php artisan serve
   ```

   Secara default, API tersedia di `http://127.0.0.1:8000/api`.

## Endpoint API

Semua endpoint mengembalikan atau menerima JSON. Saat mengirim body JSON, sertakan header `Content-Type: application/json` dan `Accept: application/json`.

| Method | Endpoint | Kegunaan |
| --- | --- | --- |
| `GET` | `/api/transactions` | Melihat semua transaksi, diurutkan dari tanggal terbaru |
| `POST` | `/api/transactions` | Menambahkan transaksi |
| `GET` | `/api/transactions/{id}` | Melihat satu transaksi |
| `PUT` / `PATCH` | `/api/transactions/{id}` | Memperbarui transaksi |
| `DELETE` | `/api/transactions/{id}` | Menghapus transaksi |
| `GET` | `/api/dashboard` | Melihat ringkasan dan data grafik |
| `POST` | `/api/ai/analyze` | Menganalisis ringkasan keuangan dengan AI |

### Contoh: menambahkan transaksi

```http
POST /api/transactions
Content-Type: application/json
Accept: application/json
```

```json
{
  "type": "expense",
  "amount": 45000,
  "category": "Food",
  "date": "2026-10-30",
  "note": "Makan siang"
}
```

`type` hanya boleh `income` atau `expense`. Kategori yang tersedia:

- Pemasukan (`income`): `Salary`, `Bonus`, `Other Income`
- Pengeluaran (`expense`): `Food`, `Transportation`, `Shopping`, `Entertainment`, `Bills`, `Other Expense`

`amount` wajib berupa angka lebih dari 0, `date` wajib berformat `YYYY-MM-DD`, dan `note` bersifat opsional (maksimal 1.000 karakter). Untuk memperbarui transaksi, kirim kembali semua field wajib.

Contoh respons sukses penambahan (HTTP `201`):

```json
{
  "data": {
    "id": 1,
    "type": "expense",
    "amount": "45000.00",
    "category": "Food",
    "date": "2026-10-30",
    "note": "Makan siang"
  }
}
```

Respons lengkap juga menyertakan `created_at` dan `updated_at`.

### Contoh: melihat dashboard

```bash
curl -H "Accept: application/json" http://127.0.0.1:8000/api/dashboard
```

Respons berisi `summary` (total pemasukan, pengeluaran, dan saldo), `income_vs_expense` (enam bulan terakhir), total pemasukan dan pengeluaran per kategori, serta maksimal sepuluh transaksi terbaru.

### Contoh: meminta analisis AI

```bash
curl -X POST \
  -H "Accept: application/json" \
  http://127.0.0.1:8000/api/ai/analyze
```

Endpoint ini tidak membutuhkan body. Respons `data` berisi `summary`, `top_category`, `insights`, dan `suggestions`. Analisis memerlukan minimal satu transaksi dan konfigurasi provider AI. Jika provider belum diatur, layanan tidak tersedia, atau belum ada transaksi, API mengembalikan pesan kesalahan dengan status yang sesuai.

## Validasi dan batasan penting

- Aplikasi saat ini belum menerapkan autentikasi atau otorisasi. Jangan gunakan endpoint terbuka ini untuk data keuangan nyata atau lingkungan produksi sebelum menambahkan pengamanan yang sesuai.
- Endpoint analisis AI dibatasi maksimal **5 permintaan per menit per alamat IP**. Panggilan dapat menggunakan kuota atau menimbulkan biaya pada provider AI.
- Catatan transaksi (`note`) tidak dikirim ke provider AI. Ringkasan keuangan dan maksimal lima pengeluaran terbesar (tanggal, kategori, dan nominal) dikirim untuk keperluan analisis.
- SQLite tercantum sebagai nilai bawaan pada `.env.example`, tetapi dashboard memakai fungsi tanggal MySQL. Atur koneksi MySQL/MariaDB seperti langkah instalasi di atas agar dashboard dan analisis dapat berjalan.

## Pengujian

Jalankan pengujian proyek dengan:

```bash
php artisan test
```

## Struktur utama

```text
app/
  Exceptions/       Penanganan error analisis AI
  Http/Controllers/ Endpoint API
  Http/Requests/    Validasi permintaan transaksi
  Models/           Model data transaksi
  Services/         Logika dashboard dan integrasi AI
database/
  migrations/       Struktur tabel database
routes/
  api.php           Daftar endpoint API
tests/              Pengujian aplikasi
```
