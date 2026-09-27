Analisis waktu membutuhkan tanggal, baseline, dan urutan. Query yang benar tetap perlu menjelaskan periode mana yang dibandingkan.

## Ekstrak periode dengan hati-hati

order_date dapat diubah menjadi month untuk membuat ringkasan. Filter September kemudian dapat dibandingkan dengan Agustus, tetapi perbedaan itu belum menjawab penyebab.

:::sql-playground id="sql-time-01"
:::

## Gunakan comparison yang tepat

LAG membantu membawa nilai periode sebelumnya ke row saat ini. Difference dan percentage change harus memakai denominator yang sesuai dan dijelaskan ketika baseline tidak tersedia.

:::practice type="multiple_choice" id="sql-time-02"
:::

Berikutnya, CTE membantu memisahkan base data, monthly summary, dan comparison agar query dapat diverifikasi.
