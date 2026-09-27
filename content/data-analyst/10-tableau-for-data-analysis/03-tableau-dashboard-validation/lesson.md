Dashboard bukan kumpulan worksheet yang ditempel bersama. Dashboard harus membantu audience menjawab pertanyaan tertentu, lalu memberi jalan untuk memeriksa angka yang mendasarinya.

## Pilih view yang menjawab pertanyaan

Untuk Revenue Slowdown, mulai dengan perubahan revenue menurut month. Tambahkan category atau region ketika pertanyaan membutuhkan konteks. Filter yang baik mengurangi ruang pencarian; filter yang terlalu banyak hanya memindahkan kebingungan ke audience.

Dashboard yang sederhana dapat memuat:

- satu metric utama dengan definisi yang jelas;
- perbandingan revenue Agustus dan September;
- view kategori atau region untuk mencari lokasi perubahan;
- filter periode yang tidak menghapus konteks awal.

:::practice type="multi_select" id="tableau-dashboard-01"
:::

## Validasi angka sebelum menulis finding

Gunakan angka yang sama di Tableau dan di BelajarData untuk pemeriksaan cepat. Pada `transactions.csv`, total revenue September adalah 600. Revenue Agustus adalah 450, sehingga perubahan relatifnya sekitar 33,3 persen.

Jika angka berbeda, periksa filter periode, aggregation, relationship, dan apakah kolom revenue dihitung pada grain yang benar. Jangan memperbaiki selisih dengan mengubah format angka atau menyembunyikan kategori.

:::practice type="numeric" id="tableau-september-revenue-01"
:::

:::callout type="warning"
Tableau Public hanya boleh dipakai untuk data yang aman dipublikasikan. Jangan pernah mengunggah data confidential, private, proprietary, atau data perusahaan ke Tableau Public.
:::

## Kembali dengan evidence, bukan screenshot saja

Setelah menyusun view di Tableau, bawa kembali metric, periode, filter, dan finding ke BelajarData. Screenshot dapat membantu diskusi, tetapi tidak menggantikan angka yang dapat diperiksa.

Dashboard sudah dapat dibaca. Challenge berikutnya menggabungkan download data, model, metric, validation, dan batas publikasi dalam satu workflow.
