Data yang terlihat rapi belum tentu siap dipakai untuk analisis. Sebelum memperbaiki apa pun, pahami grain, arti field, dan masalah yang benar-benar memengaruhi pertanyaan bisnis.

## Kenali jenis masalahnya

Masalah kualitas data dapat berupa missing value, duplicate, nilai tidak konsisten, nilai invalid, format ambigu, atau nilai yang tidak biasa. Satu row dapat memiliki lebih dari satu masalah.

Contoh NusaMart:

| order_id | order_date | category | quantity | revenue |
| --- | --- | --- | ---: | ---: |
| A001 | 2026-01-03 | Electronics | 2 | 450000 |
| A002 | 03/01/2026 | electronic | 1 | 225000 |
| A003 | NULL | Furniture | 2 | 780000 |
| A003 | NULL | Furniture | 2 | 780000 |
| A004 | 2026-01-04 | Electronics | -3 | 675000 |

Tanggal A002 perlu context. Quantity negatif dan row A003 perlu investigasi.

:::callout type="common-mistake"
Warning bukan keputusan. Menemukan NULL atau row yang mirip belum cukup untuk menentukan bahwa row harus dihapus.
:::

:::practice type="multiple_choice" id="cleaning-problem-01"
:::

## Hubungkan issue dengan pertanyaan

Pertanyaan “ada 17 nilai kosong” belum cukup. Tanyakan apakah nilai tersebut mengubah metric dan unit analisis yang sedang dipakai. delivery_date kosong pada order cancelled dapat valid, sedangkan product_id kosong pada transaksi revenue mungkin lebih serius.

:::practice type="text_self_assessment" id="cleaning-problem-02"
:::

Cleaning membantu menjawab pertanyaan analitis, bukan sekadar membuat dataset tampak sempurna. Berikutnya, nilai missing akan dibaca berdasarkan arti bisnisnya.
