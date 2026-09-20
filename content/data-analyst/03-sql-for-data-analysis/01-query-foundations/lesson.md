“Tampilkan revenue per bulan” terdengar cukup jelas sampai kita bertanya: revenue dari tabel mana, satu row mewakili apa, dan bagaimana item terhubung ke order?

## Pilih tabel berdasarkan grain

Di NusaMart, `orders` memiliki satu row per order. `order_items` memiliki satu row per product line dalam order. Keduanya menjawab pertanyaan yang berbeda.

Jika pertanyaannya tentang biaya kirim per order, mulai dari `orders`. Jika pertanyaannya tentang revenue per kategori, mulai dari `order_items`, lalu hubungkan ke `products` untuk mendapatkan kategori.

:::callout type="common-mistake"
JOIN bukan sekadar cara menambah kolom. JOIN juga dapat mengubah jumlah row yang ikut dihitung. Sebelum menjumlahkan metric, tulis dulu grain tabel dan grain output yang kamu inginkan.
:::

## Mulai dari output yang dibutuhkan

Query analitis yang dapat diperiksa biasanya dapat dibaca dari outputnya:

1. Kolom apa yang ingin dilihat?
2. Baris mana yang masuk?
3. Satu baris hasil mewakili apa?
4. Apakah angka perlu dikelompokkan atau dibandingkan?

Query awal berikut meminta order online dan hanya memilih dua kolom yang dibutuhkan:

```sql
SELECT order_id, customer_id
FROM orders
WHERE channel = 'Online'
ORDER BY order_id;
```

Urutan query membantu kita membaca maksudnya. `FROM` memilih grain awal, `WHERE` menyaring baris, `SELECT` memilih bukti yang ditampilkan, dan `ORDER BY` membuat output mudah diperiksa.

## Coba pertanyaan pertama

Tampilkan `order_id` dan `customer_id` untuk order dengan channel `Online`, urutkan berdasarkan `order_id`. Perhatikan bahwa latihan ini memeriksa hasil tabel, bukan bentuk query tertentu.

:::sql-playground id="sql-foundation-01"
:::

## Dari baris ke agregasi

Setelah tahu grain, barulah agregasi lebih aman dibaca. `COUNT(*)` pada `order_items` menghitung product line, bukan jumlah order. Untuk jumlah order, gunakan `COUNT(DISTINCT order_id)`.

Ketika nanti kita menggabungkan `orders` dan `order_items`, pertanyaan pentingnya tetap sama: satu row hasil mewakili apa? Jawaban itu menentukan apakah `SUM`, `COUNT`, atau `COUNT(DISTINCT ...)` masuk akal.

Pertanyaan tabelnya sudah jelas. Sebelum menghitung revenue lintas tabel, kita masih harus memastikan hubungan antar-grain agar JOIN tidak menggandakan angka order-level.
