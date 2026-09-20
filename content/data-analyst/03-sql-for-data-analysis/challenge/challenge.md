Stakeholder berkata:

> “Revenue berubah. Cari tahu apa yang terjadi dan pastikan angka yang kamu bandingkan tidak salah grain.”

Gunakan data NusaMart yang sama. Tantangan ini tidak meminta dashboard atau kesimpulan sebab-akibat. Fokuskan query pada grain, agregasi, dan hubungan antar-tabel.

## Mulai dengan memeriksa jumlah baris

`order_items` memiliki satu row per product line. Hitung jumlah row-nya sebagai pemeriksaan awal.

:::sql-playground id="challenge-03-grain"
:::

## Bandingkan revenue per bulan

Hubungkan `orders` dengan `order_items`, ambil bulan dari `order_date`, lalu jumlahkan `quantity * unit_price` per bulan.

:::sql-playground id="challenge-03-aggregation"
:::

## Pecah revenue berdasarkan kategori

Kategori berada di `products`, sedangkan nilai transaksi berada di `order_items`. Gunakan JOIN yang sesuai dan tampilkan revenue per kategori.

:::sql-playground id="challenge-03-join"
:::

## Diagnosis JOIN multiplication

Biaya kirim berada pada grain order. Query yang langsung JOIN ke `order_items` akan mengulang biaya kirim untuk setiap item line. Perbaiki query sehingga total biaya kirim dihitung sekali per order.

:::sql-playground id="challenge-03-diagnostic"
:::

Finding yang baik menyebutkan output, grain yang digunakan, dan batas evidence. Angka yang benar belum otomatis menjelaskan penyebab perubahan.
