Python dan Pandas berguna ketika langkah analisis perlu diulang dengan data yang sama. Kuncinya bukan menghafal sintaks, tetapi menjaga alur dari data mentah ke finding tetap dapat diperiksa.

Latihan ini menggunakan dataset NusaMart yang sudah disediakan. Fokusnya adalah membaca struktur, memilih baris, membuat kolom, menghubungkan tabel, dan memeriksa output. Untuk pekerjaan yang lebih terbuka, gunakan notebook yang dapat diunduh.

## Baca dataset sebelum transformasi

Mulai dari `head()` dan `shape`. `head()` memberi contoh baris, sedangkan `shape` membantu memeriksa ukuran data. Pemeriksaan sederhana ini dapat menangkap file yang salah atau kolom yang belum sesuai.

:::python-practice id="python-inspect-01"
:::

## Filter dan urutkan sebelum menyimpulkan

Pertanyaan “bagaimana performa September?” membutuhkan kolom periode dan ukuran yang jelas. Buat `revenue` dari `quantity * unit_price`, pilih baris September, lalu urutkan agar transaksi terbesar terlihat.

:::python-practice id="python-filter-sort-01"
:::

## Buat kolom yang menjelaskan konteks

Tanggal yang tersimpan sebagai teks belum siap dipakai untuk analisis periode. Parse `order_date`, buat kolom `month`, dan simpan hasil transformasi sebagai bagian dari data yang dapat diperiksa.

:::python-practice id="python-transform-01"
:::

## Merge fakta dengan konteks

Transactions menyimpan fakta transaksi, sementara Products menyimpan kategori. `merge` menambahkan konteks melalui `product_id`. Setelah itu, `groupby` dapat meringkas revenue per kategori tanpa menyalin kategori secara manual.

:::python-practice id="python-merge-aggregate-01"
:::

Setelah alur ini berjalan, output bukan sekadar tabel baru. Output menjadi evidence yang dapat dipakai untuk membandingkan periode, memilih fokus, dan menulis finding dengan batas yang jelas.
