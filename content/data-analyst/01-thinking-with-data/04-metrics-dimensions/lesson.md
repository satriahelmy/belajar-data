“Region mana yang menghasilkan revenue paling tinggi?”

Untuk menjawabnya, kita butuh dua hal: angka yang diukur dan cara memecah angka itu.

| Pertanyaan | Metric | Dimension atau context |
| --- | --- | --- |
| Berapa total penjualan September? | `SUM(revenue)` | September |
| Region mana yang revenue-nya paling tinggi? | `SUM(revenue)` | `region` |
| Channel mana yang punya lebih banyak order? | `COUNT DISTINCT(order_id)` | `channel` |
| Berapa nilai rata-rata satu order? | `revenue / distinct orders` | Periode atau region |

Metric menjawab “berapa”. Dimension membantu kita melihat “di mana, kapan, atau untuk kelompok apa”.

## Apa yang mau diukur?

Jangan menyebut semua angka sebagai metric. Jika ingin tahu jangkauan transaksi, hitung jumlah order unik. Jika ingin tahu nilai penjualan, hitung revenue. Dua pertanyaan ini dapat memakai tabel yang sama tetapi memberi cerita yang berbeda.

`product_id` adalah identifier, bukan ukuran “produk terbaik”. Untuk membandingkan produk, kita masih perlu memilih metric, misalnya revenue, quantity, atau margin, serta periode dan arti “terbaik”.

Dimension juga bukan penyebab. `region` bisa menunjukkan bagian mana yang berubah; ia belum menjelaskan mengapa perubahan itu terjadi.

## Satu field bisa punya beberapa peran

`order_date` bisa dipakai sebagai filter periode, diubah menjadi dimension bulan, atau menjadi dasar comparison. `order_id` tidak dijumlahkan, tetapi bisa menjadi dasar metric jumlah order unik.

Tanyakan dua hal sebelum memakai pasangan metric-dimension:

1. Apakah metric ini benar-benar mewakili concern?
2. Apakah dimension ini membantu keputusan yang ingin dibuat?

## Coba pasangkan metric dan dimension

Untuk pertanyaan “region mana yang revenue-nya paling tinggi?”, pasang `SUM(revenue)` dengan `region`. Untuk pertanyaan “apakah jangkauan transaksi meningkat?”, pasang distinct order count dengan periode yang dibandingkan. Pilih pasangan dari pertanyaannya, bukan dari bentuk kolomnya.

:::practice type="multiple_choice" id="thinking-metrics-01"
:::

:::practice type="multiple_choice" id="thinking-metrics-02"
:::

Metric dan dimension belum lengkap tanpa tahu satu row mewakili apa. Itu yang akan menentukan apakah aggregation yang dipilih benar.
