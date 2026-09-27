Setelah file tersedia, Tableau perlu tahu bagaimana tabel saling berhubungan dan bagaimana metric dihitung. Dua keputusan ini lebih penting daripada jumlah chart di dashboard.

## Hubungkan tabel tanpa menggandakan baris

`transactions.csv` menyimpan `product_id` pada setiap transaksi. `products.csv` menyimpan satu definisi untuk setiap product. Hubungannya adalah banyak transaksi ke satu product.

Dalam Tableau, gunakan relationship yang mengikuti grain tabel. Jangan langsung menempelkan tabel hanya karena kedua tabel memiliki kolom dengan nama yang sama. Jika satu baris product bertemu banyak baris transaksi, hasilnya harus tetap dapat menjawab pertanyaan pada grain transaksi.

:::callout type="common-mistake"
Jika total revenue berubah setelah tabel dihubungkan, berhenti dan bandingkan row count serta total sebelum dan sesudah relationship. Dashboard yang terlihat lengkap tetap dapat berisi angka yang terduplikasi.
:::

:::practice type="multiple_choice" id="tableau-relationship-01"
:::

## Tulis metric yang bisa dijelaskan

Pada fixture NusaMart, revenue pada setiap baris dapat diturunkan dari `quantity × unit_price`. Jika kolom revenue sudah disediakan, pastikan definisinya tetap sama dan jangan menjumlahkan quantity seolah-olah itu revenue.

Calculated field yang baik memiliki nama, rumus, dan grain yang jelas. Contoh sederhana:

```text
Revenue = [quantity] * [unit_price]
```

Metric tersebut baru menjadi informasi ketika diberi konteks, misalnya revenue per month atau revenue per category. Definisi metric harus tetap konsisten ketika dipakai oleh beberapa worksheet.

:::practice type="multiple_choice" id="tableau-calculated-field-01"
:::

Relationship dan calculated field sudah memiliki batas yang jelas. Selanjutnya kita menyusun view yang menjawab pertanyaan dan memeriksa angkanya sebelum menarik kesimpulan.
