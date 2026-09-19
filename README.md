# BelajarData

BelajarData adalah platform belajar Data Analyst berbasis Laravel. Aplikasi ini menggunakan pendekatan repository-first: materi pembelajaran, konfigurasi latihan, dan dataset disimpan sebagai file yang dapat ditinjau dan divalidasi sebelum deployment.

Aplikasi dirancang sebagai Laravel modular monolith yang server-rendered, dengan progressive JavaScript enhancement untuk komponen interaktif. Bentuk deployment-nya tetap konvensional dan ramah shared hosting: PHP, MySQL, dan aset frontend hasil build.

## Status proyek

- M0A: fondasi teknis selesai.
- M0B: technical spikes Gate A sampai E selesai dan keputusan teknis telah dicatat.
- M0C: fondasi produk selesai.
- M1A: public learning experience dan visual direction V2 selesai.
- Module 01: konten produksi dan final editorial QA selesai.
- M1B: learner-state foundation selesai untuk scope saat ini.

Yang sudah tersedia pada increment M1B saat ini:

- registrasi, login, dan logout;
- status topic `started` dan `completed`;
- progress guest melalui `localStorage`;
- penyimpanan progress learner terautentikasi di MySQL;
- merge progress guest ke akun setelah login atau registrasi;
- operasi progress yang idempotent dan tidak menurunkan status `completed`;
- bookmark topic dengan stable content key;
- halaman Progress untuk recent, resume, ringkasan status, dan bookmark.

Password reset/email verification dan assessment attempts belum termasuk increment ini.

## Teknologi dan versi lokal

Versi yang digunakan saat ini:

- PHP 8.2.12
- Laravel 12.69.2
- Composer 2.9.2
- MySQL 8.0.45 melalui PDO
- Node.js 24.11.1
- npm 11.6.2
- Vite 6.4.3

## Menjalankan secara lokal

Pastikan PHP, Composer, Node.js/npm, dan MySQL tersedia. Database development yang digunakan adalah `belajar_data_v2`.

1. Salin konfigurasi environment:

   ```powershell
   Copy-Item .env.example .env
   ```

2. Atur koneksi database di `.env`. Password lokal hanya boleh berada di `.env` dan tidak boleh di-commit:

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=belajar_data_v2
   DB_USERNAME=root
   DB_PASSWORD=<password-lokal>
   ```

3. Install dependency dan siapkan aplikasi:

   ```powershell
   composer install
   npm install
   php artisan key:generate
   php artisan migrate
   npm run build
   ```

4. Jalankan server development:

   ```powershell
   php artisan serve
   ```

   Buka `http://127.0.0.1:8000`.

Untuk development frontend dengan Vite, gunakan terminal terpisah:

```powershell
npm run dev
```

## Verifikasi

Perintah utama untuk memeriksa aplikasi:

```powershell
php artisan test
node --test tests/Frontend/*.test.js
php artisan content:validate
npm run build
git diff --check
```

`content:validate` memeriksa kontrak konten repository, referensi practice, struktur lesson, dan dataset yang digunakan oleh materi. Jalankan validasi ini sebelum deployment atau setelah mengubah konten.

## Rute utama

- `/` : homepage.
- `/learn` : learning path.
- `/learn/{module}` : ringkasan module.
- `/learn/{path}/{module}/{topic}` : lesson server-rendered.
- `/skills` : skills overview.
- `/projects` : projects overview.
- `/login` dan `/register` : akses akun learner.
- `/progress` : recent learning, resume, ringkasan progress, dan bookmark untuk learner yang login.
- `/__foundation` : smoke route fondasi teknis.
- `/__spike/*` : route teknis sementara untuk technical spikes.

Endpoint progress account berada di bawah `/progress/*`, sedangkan bookmark berada di bawah `/bookmarks/*`. Keduanya hanya dapat diakses oleh learner yang sudah login.

## Struktur repository

```text
app/Domain/Learning/       konten, lesson, dan learning contracts
app/Domain/Learner/        progress dan state learner
app/Domain/Projects/       batas domain projects
app/Domain/Datasets/       manifest dan akses dataset
app/Http/                  controller dan request pipeline
app/Models/                model persistence Laravel
content/                   Markdown lesson dan konfigurasi latihan
datasets/                  dataset pembelajaran berversi
database/                  migrations dan database support
resources/views/           server-rendered Blade views
resources/js/              progressive enhancements dan interactive components
resources/css/             stylesheet aplikasi
tests/Feature/             test HTTP, content, database, dan domain
tests/Frontend/            test JavaScript ringan
docs/                      PRD, design, architecture, dan content guide
task.md                    implementation plan dan status milestone
```

## Prinsip implementasi

- Konten kurikulum tetap version-controlled dan bukan CMS.
- Halaman biasa dirender oleh Laravel; JavaScript hanya meningkatkan pengalaman yang membutuhkan interaksi.
- Progress learner disimpan di MySQL setelah autentikasi, sedangkan guest state bersifat ringan di browser.
- Tidak ada Redis, queue, microservice, server-side Python, atau SPA sebagai prasyarat deployment.
- SQL, spreadsheet, Python, visualisasi, dan komponen Markdown mengikuti keputusan technical spike masing-masing.
- Prerequisite adalah rekomendasi belajar, bukan access lock.

## Dokumentasi

Dokumen sumber dan keputusan teknis utama berada di:

- [`docs/PRD.md`](docs/PRD.md)
- [`docs/design.md`](docs/design.md)
- [`docs/architecture.md`](docs/architecture.md)
- [`docs/content-guide.md`](docs/content-guide.md)
- [`task.md`](task.md)

README ini mendokumentasikan kondisi implementasi saat ini. Rencana milestone yang lebih rinci dan pekerjaan yang belum selesai tetap dicatat di `task.md`.
