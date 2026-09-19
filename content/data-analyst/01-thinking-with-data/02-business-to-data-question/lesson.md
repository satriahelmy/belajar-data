“Penjualan kita kok terasa menurun?”

Ini belum menjadi data question. Kita belum tahu penjualan yang mana, dibandingkan dengan apa, atau keputusan apa yang akan dibuat dari jawabannya.

## Satu keluhan, beberapa pertanyaan

Anggap stakeholder NusaMart ingin memeriksa tiga hal yang berbeda:

> Apakah revenue September 2025 lebih rendah daripada revenue Agustus 2025?

> Kategori produk mana yang mengalami penurunan revenue terbesar dari Agustus ke September 2025?

> Apakah jumlah order berubah, dan region mana yang paling berkontribusi pada perubahan itu?

Semua pertanyaan itu masuk akal. Metric, periode, comparison, dan data pemecahnya berbeda karena keputusan yang ingin dibantu juga berbeda.

## Dari concern ke data

Untuk pertanyaan pertama, rantainya bisa ditulis seperti ini:

| Bagian | Pilihan NusaMart |
| --- | --- |
| Business concern | Penjualan terasa menurun |
| Analytical question | Apakah revenue September berbeda dari Agustus? |
| Metric | Total revenue |
| Period | Agustus dan September 2025 |
| Comparison | Month-over-month |
| Dimension/context | Region atau kategori, bila perubahan perlu diurai |
| Required data | `order_date`, `revenue`, `region`, `category`, `order_id` |

Kalau yang ingin dihitung adalah customer aktif, `order_id` saja tidak cukup. Kita perlu customer identifier dan definisi “aktif”, misalnya pernah membeli dalam 30 hari terakhir.

Satu detail yang berubah bisa mengubah keputusan. Karena itu, jangan melompat dari kalimat stakeholder langsung ke kolom yang kebetulan tersedia.

## Pertanyaan yang terdengar siap, tetapi belum

| Pertanyaan | Yang masih kosong |
| --- | --- |
| “Region mana yang performanya paling baik?” | Metric, periode, dan arti “baik” |
| “Mengapa revenue turun?” | Comparison, dimensi investigasi, dan evidence kausal |
| “Berapa customer aktif?” | Definisi aktif, periode, dan customer identifier |
| “Produk apa yang harus kita tambah?” | Tujuan keputusan dan evidence demand |

“Pada September 2025, region mana yang memiliki revenue tertinggi dan bagaimana perubahannya dibanding Agustus?” sudah lebih siap dihitung. Ia belum menjawab penyebab, dan memang belum mengklaim itu.

## Coba rapikan pertanyaannya

Pilih pertanyaan yang bisa langsung diuji. Setelah itu, ubah pertanyaan yang masih ambigu menjadi versi yang menyebut metric, periode, dan konteksnya.

:::practice type="multiple_choice" id="thinking-question-01"
:::

:::practice type="text_self_assessment" id="thinking-question-02"
:::

Pertanyaan yang sudah jelas memberi kita daftar field untuk diperiksa. Sebelum menghitung, kita masih harus memastikan arti row dan column di tabel.
