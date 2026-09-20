Spreadsheet terlihat sederhana karena bentuknya familiar. Tantangannya bukan menemukan tombol, tetapi memastikan setiap angka menjawab pertanyaan yang tepat.

## Mulai dari grain tabel

Di data NusaMart, satu row pada `Transactions` mewakili satu order. Kolom `quantity`, `unit_price`, dan `revenue` menjelaskan transaksi tersebut. Tabel `Products` menyimpan atribut produk, seperti kategori.

Jika kita lupa arti satu row, rumus yang benar pun dapat menjawab pertanyaan yang keliru. Sebelum menghitung, baca nama tabel, kolom, dan contoh barisnya.

:::callout type="common-mistake"
Jangan langsung menjumlahkan kolom hanya karena namanya terlihat familiar. Tanyakan dulu, satu row mewakili transaksi, product line, atau ringkasan yang lain.
:::

:::spreadsheet-playground id="sheet-inspection-01"
:::

## Rumus harus punya pertanyaan

Total revenue menjawab berapa nilai transaksi seluruh data. Average revenue menjawab ukuran transaksi yang tipikal. Keduanya bukan pertanyaan yang sama, meskipun sama-sama memakai kolom `revenue`.

Conditional logic membantu kita membaca hasil terhadap batas yang sudah ditentukan. Batas tersebut harus disebutkan, bukan disimpulkan dari warna atau posisi cell.

:::spreadsheet-playground id="sheet-metrics-01"
:::

## Lookup menghubungkan fakta dan konteks

`Transactions` memberi kita fakta transaksi. `Products` memberi konteks kategori. Lookup yang tepat menghubungkan `product_id` dengan kategori, sementara `SUMIF` mengumpulkan nilai transaksi untuk product yang dipilih.

Perhatikan dua pemeriksaan sebelum mempercayai lookup: key harus ada dan key seharusnya tidak ambigu. Jika product tidak ditemukan, hasilnya adalah error yang perlu diperbaiki, bukan angka nol yang boleh diabaikan.

:::spreadsheet-playground id="sheet-lookup-01"
:::

Setelah tabel, rumus, dan lookup dapat diperiksa, kita siap meringkas data menurut kategori dan membandingkan area. Tantangan berikutnya meminta kamu menjelaskan hasil, bukan hanya menghasilkan angka.
