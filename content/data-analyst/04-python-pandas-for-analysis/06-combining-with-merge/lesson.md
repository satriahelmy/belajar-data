Data analisis sering tersebar di beberapa tabel. Merge membantu membawa konteks ke tabel fakta, tetapi hasilnya bergantung pada key dan jenis hubungan yang dipilih.

## Hubungkan dengan key yang tepat

Transactions memiliki product_id, sedangkan Products menyimpan category. Gunakan product_id sebagai penghubung, lalu periksa apakah setiap transaksi mendapat category.

:::python-practice id="python-merge-06"
:::

## Waspadai baris yang berubah

Merge bukan sekadar menambah kolom. Jika key di tabel konteks tidak unik, satu transaksi dapat berubah menjadi beberapa baris. Jika key tidak cocok, konteks dapat menjadi kosong.

Pada dataset NusaMart, hubungan product_id yang diharapkan memberi konteks untuk seluruh transaksi. Tetap periksa jumlah baris dan nilai category sebelum membuat ringkasan.

Setelah konteks terhubung, groupby dapat dipakai untuk membandingkan kategori tanpa menyalin label secara manual.
