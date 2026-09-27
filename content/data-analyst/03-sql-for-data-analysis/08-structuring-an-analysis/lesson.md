Query yang berjalan belum tentu mudah dipercaya. Struktur query membantu reviewer mengikuti dari mana metric berasal dan di mana comparison dibuat.

## Pisahkan langkah dengan CTE

Alur umum:

~~~text
base_orders
   ↓
monthly_sales
   ↓
comparison
   ↓
final_result
~~~

CTE membuat grain dan transformasi terlihat sebagai tahapan, bukan satu expression panjang yang sulit diperiksa.

:::sql-playground id="sql-cte-01"
:::

## Review output dan batas evidence

Periksa nama kolom, urutan periode, NULL pada baseline, serta kesesuaian metric dengan grain. SQL dapat menunjukkan perubahan, tetapi dataset transaksi belum tentu menjelaskan penyebab.

:::practice type="text_self_assessment" id="sql-cte-02"
:::

Struktur analisis ini menjadi dasar challenge NusaMart, yang menggabungkan filter, aggregation, JOIN, comparison, dan diagnosis grain.
