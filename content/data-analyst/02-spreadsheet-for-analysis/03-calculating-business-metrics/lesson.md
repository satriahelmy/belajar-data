Metric mengubah row transaksi menjadi ukuran yang dapat dibandingkan. Rumus hanya berguna jika unit dan pertanyaannya jelas.

## Bedakan total, average, dan count

Total revenue menjawab nilai keseluruhan. Average revenue memberi nilai tipikal per row. Count rows menghitung jumlah row, bukan otomatis jumlah order jika grain berada pada order-item.

:::spreadsheet-playground id="sheet-business-metrics-01"
:::

:::callout type="common-mistake"
COUNT(rows) tidak selalu sama dengan jumlah order. Pastikan identifier dan grain mendukung metric yang dipilih.
:::

## Tulis definisi sebelum formula

Sebelum mengetik SUM atau AVERAGE, tulis metric, unit analisis, periode, dan denominator. Average revenue per order berbeda dari average revenue per order-item.

:::practice type="multiple_choice" id="sheet-business-metrics-02"
:::

Metric yang sudah didefinisikan dapat diberi logic untuk menjawab kondisi bisnis tertentu.
