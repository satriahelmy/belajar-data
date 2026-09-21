Metric yang baik membantu kita menelusuri perubahan sampai ke driver yang dapat diperiksa. Metric tree memberi struktur untuk penelusuran itu, tetapi tidak otomatis membuktikan penyebab.

## Mulai dari metric yang ingin dipahami

NusaMart ingin memahami mengapa **revenue** berubah. Mulai dari revenue, bukan dari chart atau kolom yang kebetulan tersedia. Untuk latihan ini, revenue dipecah menjadi dua bagian yang dapat dibaca bersama:

**Revenue = Orders × Average order value**

Orders menjelaskan berapa banyak transaksi yang terjadi. Average order value menjelaskan nilai rata-rata setiap order. Dua driver itu membuat pertanyaan lanjutan lebih terarah daripada hanya mengatakan revenue naik atau turun.

## Turunkan driver satu tingkat lagi

Average order value dapat dijelaskan melalui **Units per order** dan **Average price per unit**. Dengan begitu, perubahan revenue dapat ditelusuri dari jumlah order, jumlah unit dalam order, atau nilai rata-rata per unit.

:::callout type="common-mistake"
Hubungan dalam metric tree adalah struktur perhitungan atau penjelasan metric. Hubungan itu belum menjadi bukti bahwa salah satu driver menyebabkan perubahan bisnis.
:::

Susun hubungan metric pada latihan berikut. Pilih parent untuk setiap node dari pilihan yang sudah ditentukan, lalu baca kembali tree-nya dari Revenue ke driver paling bawah.

:::metric-tree id="metric-tree-01"
:::

## Gunakan tree untuk menentukan pemeriksaan berikutnya

Jika revenue turun, tree membantu kita menentukan pemeriksaan berikutnya: apakah orders berkurang, average order value berubah, atau driver yang membentuk average order value yang bergeser? Jawaban tersebut tetap membutuhkan data, periode, dan perbandingan yang tepat.

Jangan menambahkan node hanya untuk membuat tree lebih ramai. Setiap cabang sebaiknya menjawab pertanyaan yang dapat diperiksa dan memiliki definisi yang konsisten.

Setelah driver tersusun, dashboard dapat dipakai untuk memantau metric dan konteksnya secara berulang. Nilai dashboard tetap bergantung pada definisi metric dan evidence di baliknya.
