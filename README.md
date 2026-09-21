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
- M2: assessment core selesai untuk scope V1; practice shell, validator, dan attempt persistence dasar tersedia.
- M3: SQL Playground selesai untuk representative Module 03 slice; runtime browser-only, fixture NusaMart, result validation, dan challenge dasar tersedia.
- M4: Spreadsheet Playground selesai untuk representative Module 02 slice; evaluator formula terbatas, filter/sort, configured summary, result validation, dan challenge dasar tersedia.
- M5: Python/Pandas Practice selesai untuk representative Module 04 slice; bounded code/output fallback, deterministic checks, fixture NusaMart, dan downloadable notebook tersedia tanpa runtime Python atau service server-side.
- M6A/M6B/M6C/M6D/M6E: Visualization Playground, JOIN Row Multiplication, Sampling & Uncertainty, Metric Tree Builder, dan Communication Builder representative slices tersedia; interactive dimuat lazy dengan konfigurasi bounded, fallback server-rendered, deterministic validation, grain warnings, reproducible sample estimates, predefined metric relationships, serta reference/checklist self-assessment tanpa AI grading.

Yang sudah tersedia pada increment M1B saat ini:

- registrasi, login, dan logout;
- status topic `started` dan `completed`;
- progress guest melalui `localStorage`;
- penyimpanan progress learner terautentikasi di MySQL;
- merge progress guest ke akun setelah login atau registrasi;
- operasi progress yang idempotent dan tidak menurunkan status `completed`;
- bookmark topic dengan stable content key;
- halaman Progress untuk recent, resume, ringkasan status, dan bookmark.

Password reset/email verification belum termasuk increment ini.

Yang sudah tersedia pada increment M2 saat ini:

- shared Practice Shell untuk lima kontrak latihan;
- validator deterministic untuk categorical, numeric, table result, dan guided self-assessment;
- progressive hint dan feedback yang dapat dikonfigurasi;
- attempt loading, retry, reset, bounded payload, dan completion persistence untuk learner login;
- agregasi progress challenge dari exercise yang terdaftar.
- frontend interaction/accessibility contract tests dan browser smoke QA.

Yang sudah tersedia pada increment M3 saat ini:

- SQL Playground berbasis `sql.js` 1.14.2 di Web Worker, dimuat lazy hanya pada lesson/challenge SQL;
- schema browser fixture NusaMart versi `v1`, editor query, Run, Cek hasil, Reset runtime, output cap 100 baris, dan error yang mempertahankan query;
- validasi hasil berbasis kolom dan baris dengan dukungan ordering, numeric tolerance, dan attempt persistence;
- representative Module 03 topic dan challenge untuk grain, agregasi, JOIN, serta diagnosis JOIN multiplication.

Yang sudah tersedia pada increment M4 saat ini:

- Spreadsheet Playground browser-only dengan native HTML table dan evaluator formula yang dibatasi allowlist, tanpa dependency spreadsheet baru;
- fixture NusaMart versi `v1` dengan tabel Transactions dan Products, schema browser, filter/sort terbatas, formula, lookup exact, dan ringkasan kategori terkonfigurasi;
- validasi output berbasis target cell atau tabel, error `#N/A`, `#DUPLICATE!`, `#REF!`, dan `#DIV/0!`, reset deterministik, serta attempt persistence melalui kontrak M2;
- representative Module 02 topic dan challenge untuk inspeksi dataset, metric, conditional logic, lookup, summary, comparison, missing key, dan finding.

Yang sudah tersedia pada increment M5 saat ini:

- bounded browser practice untuk inspect, filter/sort, transform, merge, aggregate, dan comparison pada fixture CSV NusaMart;
- code editor dengan starter code, output tabel dan metrik terkonfigurasi, error untuk langkah yang belum lengkap, reset, hint, serta validasi hasil melalui kontrak M2;
- representative Module 04 topic dan challenge dengan alur dari inspeksi dataset sampai finding September;
- downloadable notebook fallback untuk eksplorasi Python yang lebih terbuka;
- tidak ada learner Python yang dikirim atau dieksekusi oleh Laravel, dan ordinary lesson tidak memuat Pyodide.

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
- `/attempts/*` : endpoint internal terproteksi untuk memuat, memeriksa, dan mereset attempt latihan learner.
- `/learn/data-analyst/02-spreadsheet-for-analysis/01-spreadsheet-foundations` : representative Spreadsheet lesson dengan evaluator browser-only.
- `/learn/data-analyst/02-spreadsheet-for-analysis/challenge` : representative Spreadsheet challenge NusaMart.
- `/learn/data-analyst/03-sql-for-data-analysis/01-query-foundations` : representative SQL lesson dengan SQL Playground browser-only.
- `/learn/data-analyst/03-sql-for-data-analysis/challenge` : representative SQL challenge NusaMart.
- `/learn/data-analyst/04-python-pandas-for-analysis/01-python-foundations` : representative bounded Python/Pandas lesson NusaMart.
- `/learn/data-analyst/04-python-pandas-for-analysis/challenge` : representative bounded Python/Pandas challenge NusaMart.
- `/downloads/nusamart-module-04-fallback.ipynb` : downloadable notebook fallback untuk Module 04.
- `/learn/data-analyst/08-data-visualization/01-choosing-a-visual` : representative bounded Visualization Playground lesson.
- `/learn/data-analyst/08-data-visualization/challenge` : representative visualization challenge dengan composition dan guided finding.
- `/learn/data-analyst/06-exploratory-data-analysis/01-join-grain` : representative JOIN Row Multiplication lesson dengan predefined NusaMart tables.
- `/learn/data-analyst/06-exploratory-data-analysis/challenge` : representative JOIN grain challenge.
- `/learn/data-analyst/07-statistics-for-analysts/01-sampling-uncertainty` : representative bounded Sampling & Uncertainty lesson dengan repeated sample means.
- `/learn/data-analyst/07-statistics-for-analysts/challenge` : representative sampling challenge dengan bounded state dan guided reflection.
- `/learn/data-analyst/09-metrics-dashboards/01-metric-tree` : representative bounded Metric Tree Builder lesson dengan predefined Revenue relationships.
- `/learn/data-analyst/09-metrics-dashboards/challenge` : representative metric tree challenge dengan bounded relationship validation dan guided reflection.
- `/learn/data-analyst/12-communicating-insights/01-communication-builder` : representative bounded Communication Builder lesson dengan lima field komunikasi dan reference/checklist review.
- `/learn/data-analyst/12-communicating-insights/challenge` : representative Communication Builder challenge untuk menyusun finding NusaMart.
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
