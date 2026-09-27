Missing value bukan satu jenis masalah. NULL dapat berarti belum diketahui, tidak berlaku, belum terjadi, atau gagal tercatat.

## Baca arti NULL dari context

Di NusaMart, delivery_date kosong pada order cancelled dapat berarti pengiriman memang tidak terjadi. Sebaliknya, product_category kosong pada order valid mungkin dapat dipulihkan dari product master.

| status | delivery_date | kemungkinan arti |
| --- | --- | --- |
| Delivered | 2026-08-02 | tanggal tersedia |
| Delivered | NULL | data perlu diperiksa |
| Cancelled | NULL | mungkin tidak berlaku |

:::callout type="common-mistake"
Mengisi semua NULL dengan “Unknown” dapat menyamarkan perbedaan antara tidak berlaku dan gagal tercatat.
:::

:::practice type="multiple_choice" id="missing-values-01"
:::

## Pilih treatment

Pilihan umum adalah mempertahankan missing, menghapus row, mengisi atau melakukan imputasi, dan memulihkan dari source lain. Jika product_id tersedia, lookup ke product master lebih defensible daripada menebak category.

:::practice type="text_self_assessment" id="missing-values-02"
:::

Jangan hanya menghitung total NULL. Bandingkan missing menurut status, periode, dan kategori, lalu catat dampaknya pada metric.
