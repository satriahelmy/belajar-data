Sebelum menghitung, pastikan dataset yang dibaca memang dataset yang dimaksud. Python membantu membuat pemeriksaan awal ini dapat diulang.

## Mulai dari bentuk data

Gunakan tiga pemeriksaan kecil: baca file, lihat beberapa baris, lalu cek jumlah baris dan kolom. Contoh baris memberi konteks isi, sedangkan shape membantu menemukan file yang terlalu pendek atau kolom yang hilang.

:::python-practice id="python-load-02"
:::

## Bedakan contoh dari kesimpulan

Tiga baris pertama hanya sampel untuk inspeksi. Jangan menyebutnya sebagai pola seluruh transaksi sebelum menghitung cakupan yang sesuai dengan pertanyaan.

Jika jumlah baris tidak sesuai atau nama kolom berbeda dari yang diharapkan, berhenti dan periksa sumber data. Kesalahan di tahap ini akan ikut terbawa ke filter dan ringkasan berikutnya.

Pemeriksaan struktur selesai ketika kamu tahu tabel apa yang dibaca, ukuran tabelnya, dan apakah kolom yang dibutuhkan tersedia.
