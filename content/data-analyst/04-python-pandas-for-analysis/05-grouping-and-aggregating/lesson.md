Ringkasan membuat banyak baris dapat dibaca sebagai pola per kategori atau periode. Namun ringkasan hanya berguna jika grain, measure, dan agregasinya jelas.

## Tentukan apa yang diringkas

Revenue per category menjawab pertanyaan berbeda dari revenue total. Category menjadi dimension, sedangkan revenue menjadi measure. GroupBy lalu menjumlahkan measure untuk setiap nilai dimension.

:::python-practice id="python-groupby-05"
:::

## Periksa total dan urutan

Setelah membuat ringkasan, jumlahkan kembali nilai kategori dan bandingkan dengan total transaksi. Pemeriksaan ini membantu menemukan kategori yang hilang atau filter yang tidak sengaja diterapkan.

Urutan hasil juga merupakan keputusan komunikasi. Mengurutkan revenue menurun membantu menemukan kontribusi terbesar, tetapi tidak otomatis menjelaskan penyebabnya.

Gunakan groupby untuk menjawab pertanyaan yang spesifik, bukan untuk menghasilkan tabel ringkasan tanpa tujuan.
