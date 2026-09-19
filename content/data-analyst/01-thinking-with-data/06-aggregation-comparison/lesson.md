NusaMart mencatat:

| Periode | Revenue | Distinct orders |
| --- | ---: | ---: |
| Agustus | 450 | 3 |
| September | 600 | 5 |

Revenue naik. Apakah itu berarti setiap order juga lebih besar?

Belum tentu. Average order value turun dari 150 menjadi 120. Satu metric memberi cerita yang berbeda dari metric lain.

## Angka yang sama, cerita berbeda

Pilih aggregation berdasarkan pertanyaan:

| Pertanyaan | Aggregation | Catatan |
| --- | --- | --- |
| Berapa total revenue? | `SUM(revenue)` | Pastikan revenue tercatat pada grain yang tepat |
| Berapa order? | `COUNT DISTINCT(order_id)` | Jangan menghitung row order-item sebagai order |
| Berapa average order value? | `SUM(revenue) / COUNT DISTINCT(order_id)` | Denominator-nya order |
| Berapa conversion rate? | `successful orders / eligible visits` | Definisikan denominator-nya |

Revenue menjawab nilai penjualan. Jumlah order menjawab banyaknya transaksi. Average order value menghubungkan keduanya, tetapi hanya jika denominator-nya benar.

## Cari pembanding yang tepat

`September revenue = 600` belum berarti apa-apa tanpa baseline. Pilih pembanding sesuai keputusan:

- **Month-over-month:** September versus Agustus untuk melihat perubahan terbaru.
- **Year-over-year:** September versus September tahun lalu jika musim berpengaruh.
- **Target:** September versus target yang definisinya sudah jelas.

Untuk NusaMart, perubahan month-over-month adalah `(600 - 450) / 450 = 33,3%`. Itu menunjukkan revenue naik dibanding Agustus. Angka tersebut belum menjelaskan penyebabnya atau apakah target tercapai.

Pastikan periode pembanding sudah lengkap. Membandingkan September penuh dengan bulan yang datanya baru separuh bisa membuat perubahan terlihat lebih besar atau lebih kecil dari kondisi sebenarnya.

## Coba tulis finding-nya

Sebelum menyimpulkan, sebutkan metric, grain, denominator, comparison, dan keputusan yang akan dibantu. Lalu tulis finding singkat dari tabel NusaMart.

:::practice type="multiple_choice" id="thinking-aggregation-01"
:::

:::practice type="text_self_assessment" id="thinking-aggregation-02"
:::

Dengan aggregation dan comparison yang jelas, kita bisa membedakan angka yang tercatat dari insight yang layak disampaikan.

:::callout type="common-mistake"
Revenue naik tidak otomatis berarti semua aspek bisnis membaik. Lihat metric lain yang relevan sebelum membuat kesimpulan.
:::
