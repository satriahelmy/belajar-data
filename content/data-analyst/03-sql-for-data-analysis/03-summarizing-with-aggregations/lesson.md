Aggregation mengubah banyak row menjadi metric. Arti metric tetap bergantung pada grain dan denominator yang dipakai.

## Pilih aggregation sesuai pertanyaan

COUNT(*) menghitung row. COUNT DISTINCT menghitung identifier unik. SUM menghitung nilai total, sedangkan AVG menggambarkan nilai tipikal pada unit yang dipilih.

:::sql-playground id="sql-aggregation-01"
:::

:::callout type="common-mistake"
COUNT(*) pada order_items bukan jumlah order. Satu order dapat memiliki beberapa product line.
:::

## Baca metric bersama unitnya

Revenue total, average unit price, dan order count menjawab pertanyaan berbeda. Sebelum menjalankan query, tulis satu kalimat tentang unit output dan denominator.

:::practice type="multiple_choice" id="sql-aggregation-02"
:::

Aggregation sudah tepat ketika hasilnya dapat dijelaskan kembali dari grain source.
