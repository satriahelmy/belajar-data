Lihat tabel ini:

| order_id | product | quantity | revenue |
| --- | --- | ---: | ---: |
| ORD001 | Mouse | 1 | 100 |
| ORD001 | Keyboard | 1 | 150 |
| ORD002 | Monitor | 1 | 250 |

Ada tiga baris. Berapa order?

Dua. `ORD001` muncul dua kali karena satu order berisi dua item.

## Tiga baris, berapa order?

Satu row di tabel ini mewakili satu item produk dalam satu order. Itulah grain-nya.

Dari tabel kecil tadi:

- ada 3 order-item;
- ada 2 order unik;
- total quantity 3 unit;
- total revenue 500.

Kalau tabel diubah menjadi satu row per order, `ORD001` bisa muncul satu kali dengan revenue 250. Total revenue tetap sama, tetapi cara menghitung order dan item berubah.

## Grain mengubah cara menghitung

| Pertanyaan | Operasi yang mungkin | Unit yang dihitung |
| --- | --- | --- |
| Berapa unit terjual? | `SUM(quantity)` | Item |
| Berapa order? | `COUNT DISTINCT(order_id)` | Order |
| Berapa revenue? | `SUM(revenue)` | Revenue pada order-item |
| Berapa average order value? | Total revenue / distinct order | Order sebagai denominator |

`COUNT(*)` kebetulan benar kalau setiap order punya satu item. Begitu satu order memiliki dua item, hasilnya terlalu besar untuk pertanyaan jumlah order.

## Uji unit analisis

Saat membuka tabel baru, lengkapi kalimat ini:

> Satu row mewakili satu **[entity/event]** pada level **[detail]**.

Lalu cek apakah metric dan denominator berada pada unit yang sama dengan pertanyaan. Untuk average order value, revenue boleh dijumlahkan dari order-item, tetapi denominator-nya harus order unik.

:::practice type="multiple_choice" id="thinking-grain-01"
:::

:::practice type="text_self_assessment" id="thinking-grain-02"
:::

Setelah grain beres, angka baru bisa diringkas dan dibandingkan tanpa menghitung unit yang keliru.
