Lookup menghubungkan fakta transaksi dengan konteks dari tabel lain. Hubungan itu harus memakai key yang tepat dan hasilnya harus dapat diperiksa.

## Hubungkan dua tabel

Transactions berisi product_id dan revenue. Products berisi product_id dan category. Lookup membawa category ke context transaksi, sedangkan SUMIF dapat meringkas revenue untuk product tertentu.

:::spreadsheet-playground id="sheet-lookup-topic-01"
:::

## Perlakukan missing key sebagai sinyal

Jika product_id tidak ditemukan, #N/A lebih jujur daripada angka nol. Nol berarti key ditemukan tetapi tidak ada nilai, sedangkan missing key berarti hubungan data belum tersedia.

:::callout type="common-mistake"
Jangan menyamarkan lookup error dengan 0. Nilai itu dapat mengubah ringkasan dan menyulitkan investigasi kualitas data.
:::

:::practice type="multiple_choice" id="sheet-lookup-topic-02"
:::

Lookup selesai ketika key, hasil, dan dampaknya terhadap metric sudah diperiksa.
