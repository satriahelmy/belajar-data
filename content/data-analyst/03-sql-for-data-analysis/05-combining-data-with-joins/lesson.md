JOIN dibutuhkan ketika evidence tersebar di beberapa tabel. Key dan relationship menentukan apakah context bertambah tanpa merusak metric.

## Cocokkan foreign key

order_items memiliki order_id dan product_id. Orders menyediakan customer dan tanggal. Products menyediakan category. Customers menyediakan segment. Pilih key yang merepresentasikan relationship tersebut.

:::sql-playground id="sql-join-01"
:::

:::callout type="common-mistake"
JOIN one-to-many dapat mengulang kolom order-level. Shipping cost yang dijumlahkan setelah JOIN ke order_items dapat terlihat lebih besar dari nilai sebenarnya.
:::

## Validasi setelah JOIN

Bandingkan row count sebelum dan sesudah, cek unmatched key, dan tulis grain hasil. Hasil yang lebih banyak tidak otomatis salah, tetapi metric harus dihitung pada unit yang tepat.

:::practice type="text_self_assessment" id="sql-join-02"
:::

JOIN sudah dipahami ketika perubahan row dan risiko metric dapat dijelaskan, bukan hanya ketika query berjalan.
