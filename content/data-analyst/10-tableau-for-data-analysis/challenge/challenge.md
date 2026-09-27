NusaMart meminta ringkasan Revenue Slowdown dari Agustus ke September. Kamu akan mengerjakan bagian implementasi di Tableau, lalu kembali ke BelajarData untuk memeriksa angka, konteks, dan batas evidence.

## Workflow yang harus dijalankan

1. Buka [dataset Tableau NusaMart v1](/downloads/tableau/nusamart/v1) dan unduh `transactions.csv` serta `products.csv`.
2. Buka Tableau Desktop atau Tableau Public secara lokal. Hubungkan kedua file tanpa mengubah grain transaksi.
3. Buat calculated field `Revenue = [quantity] * [unit_price]` jika memakai kolom turunan dari file mentah.
4. Buat view revenue menurut month, lalu tambahkan category atau region sebagai konteks pemeriksaan.
5. Validasi total dan periode di bawah sebelum menulis finding.

File `orders.csv` dan `customers.csv` di halaman yang sama menyediakan workflow kedua untuk contoh Customer Retention. Gunakan workflow itu untuk melihat jumlah order dan segmentasi customer, bukan untuk mencampur definisinya dengan revenue transaksi.

:::callout type="warning"
Jika memilih Tableau Public, hanya gunakan dataset fiktif NusaMart. Jangan publish data confidential, private, proprietary, atau data perusahaan.
:::

## Bawa kembali hasil yang bisa diperiksa

Jangan hanya membawa screenshot. Catat metric, periode, filter, total, dan satu finding yang membedakan pola terlihat dari penyebab yang masih belum terbukti. Gunakan reference answer dan checklist untuk self-assessment.

:::practice type="numeric" id="tableau-challenge-total-01"
:::

:::practice type="numeric" id="tableau-challenge-growth-01"
:::

:::practice type="multi_select" id="tableau-challenge-dashboard-01"
:::

:::practice type="text_self_assessment" id="tableau-challenge-reflection-01"
:::
