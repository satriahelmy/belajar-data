Filter dan sort bukan tujuan akhir. Keduanya membantu analyst melihat subset dan urutan yang relevan dengan pertanyaan.

## Mulai dari subset yang tepat

Pertanyaan “order mana yang paling besar di West?” membutuhkan filter region West dan sort revenue descending. Hasilnya harus tetap dibaca sebagai transaksi, bukan sebagai total region.

:::spreadsheet-playground id="sheet-filter-sort-01"
:::

:::callout type="common-mistake"
Filter yang aktif mengubah baris yang terlihat, tetapi tidak otomatis mengubah arti satu row atau menjadi bukti bahwa satu region menyebabkan hasil tertentu.
:::

## Gunakan beberapa kondisi dengan sadar

Filter bulan dan region dapat dipakai bersama ketika pertanyaan memang meminta subset tersebut. Setelah filter diterapkan, periksa jumlah row dan periode yang tampil agar tidak salah membaca hasil kosong sebagai tidak ada transaksi.

:::practice type="multiple_choice" id="sheet-filter-sort-02"
:::

Subset sudah jelas. Berikutnya, ubah row transaksi menjadi metric yang dapat menjawab pertanyaan bisnis.
