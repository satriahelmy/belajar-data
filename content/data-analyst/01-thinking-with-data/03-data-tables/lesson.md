Sebelum menghitung, lihat dulu tabelnya.

| order_id | product_id | quantity | revenue |
| --- | --- | ---: | ---: |
| O-1001 | P-1001 | 1 | 100 |
| O-1001 | P-2001 | 2 | 200 |
| O-1002 | P-3001 | 1 | 150 |

Data ini punya tiga baris. Apakah berarti ada tiga order?

Belum tentu. `O-1001` muncul dua kali karena satu order membeli dua produk. Satu baris di sini mewakili order-item, bukan order.

## Lihat field-nya, bukan hanya angkanya

Sebelum memakai kolom, cari tahu artinya.

| Field NusaMart | Yang perlu diketahui |
| --- | --- |
| `order_id` | Identifier; bisa berulang pada beberapa item |
| `order_date` | Tanggal transaksi dan periode yang akan dipakai |
| `region` | Nilai region yang valid |
| `category` | Cara kategori didefinisikan dan dijaga konsisten |
| `quantity` | Satuan unit atau paket |
| `revenue` | Net atau gross, dan tercatat pada grain apa |

Tipe data memberi petunjuk, tetapi tidak menentukan peran sendirian. `order_id` berupa text, tetapi dapat menjadi dasar menghitung order unik. `revenue` berupa angka, tetapi belum tentu boleh langsung dijumlahkan sebelum grain-nya jelas.

## Jangan buru-buru menghitung baris

Dari tabel tadi:

- ada 3 observations;
- ada 2 order unik;
- total quantity adalah 4;
- total revenue adalah 450.

Untuk pertanyaan “berapa item terjual?”, jumlahkan `quantity`. Untuk pertanyaan “berapa order?”, hitung `order_id` yang distinct. `COUNT(*)` akan menjawab jumlah baris, bukan otomatis jumlah order.

Tabel customer akan punya cerita lain: satu row bisa mewakili satu customer, bukan satu transaksi. Nama field yang mirip tidak menjamin grain-nya sama.

## Satu baris di tabel ini mewakili apa?

Tulis kalimat ini setiap kali membuka dataset baru:

> Satu row dalam tabel ini mewakili satu ____ pada level ____.

Untuk contoh di atas: satu item produk dalam satu order. Coba jawab dengan kalimatmu sendiri, lalu pilih operasi yang aman untuk menghitung order.

:::practice type="multiple_choice" id="thinking-data-01"
:::

:::practice type="text_self_assessment" id="thinking-data-02"
:::

Begitu unit row sudah jelas, barulah kita bisa memilih metric dan dimension yang tepat untuk pertanyaan tadi.
