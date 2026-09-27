Tableau membantu kita menyusun view dan dashboard, tetapi tool tidak menggantikan definisi metric atau pemeriksaan data. Workflow dimulai sebelum file dibuka: pilih dataset yang tepat, catat versinya, lalu pastikan arti satu baris.

## Unduh data yang dapat diperiksa

Gunakan [halaman download dataset Tableau NusaMart](/downloads/tableau/nusamart/v1). Halaman itu menyediakan file CSV, versi dataset, ukuran file, dan SHA-256. Untuk workflow Revenue Slowdown, gunakan `transactions.csv` dan `products.csv`.

Dataset ini fiktif dan aman untuk latihan. Versi `v1` memiliki transaksi Agustus dan September 2025. Jangan mengganti file dengan data perusahaan hanya karena strukturnya terlihat mirip.

:::callout type="important"
Catat dataset dan versi sebelum membuka Tableau. Jika file berubah, angka di worksheet dan hasil pemeriksaan dapat berubah juga.
:::

## Mulai dari grain

`transactions.csv` memiliki satu baris per order-item transaction pada fixture ini. `products.csv` memiliki satu baris per product. Grain menentukan apa yang dapat dihitung langsung dan apa yang membutuhkan deduplikasi atau relasi yang tepat.

Misalnya, `SUM(revenue)` menjumlahkan nilai pada baris transaksi. `COUNT(order_id)` hanya dapat dibaca sebagai jumlah order jika satu order memang hanya muncul satu kali pada tabel yang sedang dipakai. Jangan menganggap nama kolom sudah menjelaskan grain.

:::practice type="multiple_choice" id="tableau-download-01"
:::

Pertanyaan tentang grain sudah dibawa ke awal workflow. Setelah data dikenali, barulah kita menghubungkan tabel dan menetapkan metric yang akan digunakan pada view.
