SQL filter harus dimulai dari business question. WHERE memilih baris yang masuk ke evidence, bukan sekadar mengurangi output.

## Tulis scope sebelum query

Pertanyaan “berapa order online pada September?” memiliki channel dan periode sebagai criteria. Gunakan order_date untuk periode dan channel untuk kondisi, lalu tampilkan kolom yang membantu memeriksa hasil.

:::sql-playground id="sql-filter-01"
:::

:::callout type="common-mistake"
Filter yang terlalu longgar memasukkan row yang tidak sesuai. Filter yang terlalu ketat dapat membuat hasil terlihat kosong tanpa menjelaskan scope yang dipakai.
:::

## Periksa hasil filter

Bandingkan jumlah row, periode, dan identifier yang tampil. Jika pertanyaan meminta transaksi, pastikan tabel yang dipilih memang memiliki grain transaksi yang dimaksud.

:::practice type="multiple_choice" id="sql-filter-02"
:::

Filter yang jelas menjadi dasar agregasi dan breakdown berikutnya.
