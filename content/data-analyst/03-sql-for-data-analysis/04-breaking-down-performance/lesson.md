GROUP BY menerjemahkan pertanyaan metric by dimension menjadi output yang memiliki satu baris per kelompok.

## Pilih dimension yang relevan

Revenue per category memakai category sebagai dimension. Orders per customer segment memakai segment. Jangan memilih grouping hanya karena field tersedia.

:::sql-playground id="sql-grouping-01"
:::

## Periksa grain output

Setelah GROUP BY, satu row output harus memiliki arti yang jelas. Jika category muncul dua kali karena join atau definisi berbeda, periksa key dan relationship sebelum menulis finding.

:::practice type="multiple_choice" id="sql-grouping-02"
:::

Breakdown membantu menemukan contributor. JOIN berikutnya akan menambahkan context dari tabel lain, tetapi juga dapat mengubah jumlah row.
