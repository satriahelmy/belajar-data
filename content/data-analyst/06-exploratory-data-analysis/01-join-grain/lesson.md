JOIN sering dipahami sebagai cara menambahkan kolom. Padahal, JOIN juga dapat mengubah jumlah row dan grain hasil. Sebelum membaca metric setelah JOIN, periksa dulu hubungan antar-tabelnya.

## Tulis grain kedua tabel

Di NusaMart, `orders` memiliki satu row per order. `order_items` memiliki satu row per product line. Satu order dapat memiliki lebih dari satu product line, sehingga relationship-nya adalah one-to-many.

Kalau kita menggabungkan kedua tabel pada `order_id`, satu row dari `orders` dapat bertemu beberapa row dari `order_items`. Kolom order akan berulang di hasil, dan itu bukan error dengan sendirinya. Yang penting, kita tahu grain hasilnya.

:::callout type="common-mistake"
Row count yang lebih besar setelah JOIN bukan bukti bahwa data rusak. Row count itu adalah petunjuk relationship. Masalah muncul ketika metric pada grain order langsung dijumlahkan setelah menjadi grain order-item.
:::

## Lihat row count sebelum menyimpulkan

Bandingkan tiga hal: jumlah row left table, jumlah row right table, dan jumlah row hasil. Skenario `orders` ke `order_items` memperlihatkan bagaimana satu order dapat muncul beberapa kali. Skenario `order_items` ke `products` menunjukkan JOIN many-to-one yang menambahkan konteks tanpa menggandakan order-item.

:::join-practice id="join-grain-01"
:::

## Bedakan relationship dan key yang salah

Join key harus mewakili relationship yang benar. `orders.order_id` bertemu `order_items.order_id`, sedangkan `order_items.product_id` bertemu `products.product_id`. Jika key tidak ada di kedua tabel atau tidak menggambarkan hubungan yang dimaksud, hasil kosong atau hasil berlebih harus memicu pemeriksaan ulang.

Sebelum menghitung revenue, shipping cost, atau jumlah order setelah JOIN, tulis kalimat sederhana: “satu row hasil mewakili ...”. Kalimat itu membantu memilih `SUM`, `COUNT`, atau `COUNT DISTINCT` dengan lebih hati-hati.

Grain hasil JOIN sudah diperiksa. Berikutnya, gunakan ringkasan yang sesuai dengan unit analisis, bukan sekadar angka yang paling mudah dijumlahkan.
